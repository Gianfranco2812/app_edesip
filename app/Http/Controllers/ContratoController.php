<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Venta;
use App\Models\Cuota;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ContratoController extends Controller
{

    public function vistaPublica($token)
    {
        
        $contrato = Contrato::with('venta.cliente', 'venta.grupo.programa')
                            ->where('token_acceso', $token)
                            ->firstOrFail();
        
        
        if ($contrato->estado === 'Firmado') {
            return redirect()->route('login')->with('info', 'Este contrato ya fue firmado.');
        }

        return view('contratos.firmar', compact('contrato'));
    }


    public function procesarFirma(Request $request, $token)
    {
        return DB::transaction(function() use ($token) {
            
            
            $contrato = Contrato::with('venta.cliente')->where('token_acceso', $token)->firstOrFail();
            $cliente = $contrato->venta->cliente;

            if ($contrato->estado === 'Firmado') {
                return back();
            }

            
            $rutaPDF = $this->generarYGuardarPDF($contrato, $cliente);

            
            $contrato->update([
                'estado' => 'Firmado',
                'fecha_firma' => now(),
                'ruta_pdf' => $rutaPDF,
                'ip_firma' => request()->ip()
            ]);

            
            $datosAcceso = $this->gestionarUsuario($cliente);

            
            return view('contratos.bienvenida', [
                'usuario' => $datosAcceso['usuario'],
                'password' => $datosAcceso['password'],
                'esNuevo' => $datosAcceso['esNuevo'],
                'ruta_pdf' => $rutaPDF
            ]);
        });
    }

    public function verPdf($id)
    {
        $contrato = Contrato::with('venta')->findOrFail($id);

        $esAdmin = Auth::user()->hasRole('Admin');
        $esElVendedor = Auth::id() === $contrato->venta->vendedor_id;

        if (!$esAdmin && !$esElVendedor) {
            abort(403, 'No tienes permiso para ver este documento.');
        }

        if (!$contrato->ruta_pdf || !Storage::disk('public')->exists($contrato->ruta_pdf)) {
            return back()->with('error', 'El archivo PDF no se encuentra o no ha sido generado aún.');
        }

        return response()->file(storage_path('app/public/' . $contrato->ruta_pdf));
    }

    private function gestionarUsuario($cliente)
    {
        $usuario = User::where('username', $cliente->numero_documento)->first();
        $esNuevo = false;
        
        $password = $cliente->numero_documento; 

        if (!$usuario) {
            $usuario = User::create([

                'name'     => $cliente->nombre . ' ' . $cliente->apellido, 
                
                'email'    => $cliente->email,
                

                'username' => $cliente->numero_documento,   
                
                'password' => Hash::make($password),
            ]);


            $usuario->assignRole('Cliente');
            
            $esNuevo = true;
        }


        $cliente->user_id = $usuario->id;
        $cliente->save();

        return [
            'usuario' => $usuario,
            'password' => $password,
            'esNuevo' => $esNuevo
        ];
    }

    private function generarYGuardarPDF($contrato, $cliente)
    {
        $pdf = Pdf::loadView('contratos.pdf_plantilla', [
            'contrato' => $contrato,
            'cliente' => $cliente,
            'fecha' => now()->format('d/m/Y H:i')
        ]);
        
        $pdf->setPaper('a4', 'portrait');


        $apellidoLimpio = Str::slug($cliente->apellido); 
        
        $nombreArchivo = strtoupper($apellidoLimpio) . '_' . $cliente->numero_documento . '_C' . $contrato->id . '.pdf';

        
        $carpeta = 'contratos'; 
        
        $rutaStorage = $carpeta . '/' . $nombreArchivo;

        Storage::disk('public')->put($rutaStorage, $pdf->output());

        $contrato->update([
            'ruta_pdf' => $rutaStorage
        ]);

        return $rutaStorage;
    }
}