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
        // Traemos el contrato con los datos de la venta, cliente y programa
        $contrato = Contrato::with('venta.cliente', 'venta.grupo.programa')
                            ->where('token_acceso', $token)
                            ->firstOrFail();
        
        // Si ya firmó, no mostramos el contrato, mostramos login o aviso
        if ($contrato->estado === 'Firmado') {
            return redirect()->route('login')->with('info', 'Este contrato ya fue firmado.');
        }

        return view('contratos.firmar', compact('contrato'));
    }

    // -------------------------------------------------------------------------
    // 2. ORQUESTADOR (El método que ejecuta todo al dar clic en "Firmar")
    // -------------------------------------------------------------------------
    public function procesarFirma(Request $request, $token)
    {
        return DB::transaction(function() use ($token) {
            
            // Validaciones
            $contrato = Contrato::with('venta.cliente')->where('token_acceso', $token)->firstOrFail();
            $cliente = $contrato->venta->cliente;

            if ($contrato->estado === 'Firmado') {
                return back();
            }

            // A. LLAMADA AL MÉTODO DE PDF (Separado)
            $rutaPDF = $this->generarYGuardarPDF($contrato, $cliente);

            // B. Actualizar BD
            $contrato->update([
                'estado' => 'Firmado',
                'fecha_firma' => now(),
                'ruta_pdf' => $rutaPDF,
                'ip_firma' => request()->ip()
            ]);

            // C. LLAMADA AL MÉTODO DE USUARIO (Separado)
            $datosAcceso = $this->gestionarUsuario($cliente);

            // D. Retornar vista de éxito
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
        // 1. Buscamos el contrato y su venta relacionada
        $contrato = Contrato::with('venta')->findOrFail($id);

        // 2. SEGURIDAD: ¿Quién puede ver esto?
        // - El Administrador
        // - O el Vendedor que hizo la venta
        $esAdmin = Auth::user()->hasRole('Admin');
        $esElVendedor = Auth::id() === $contrato->venta->vendedor_id;

        if (!$esAdmin && !$esElVendedor) {
            abort(403, 'No tienes permiso para ver este documento.');
        }

        // 3. Verificamos que el archivo físico exista
        // Asumiendo que guardaste la ruta en la columna 'ruta_pdf' de la tabla contratos
        // Si no tienes esa columna, avísame y usamos otra lógica.
        if (!$contrato->ruta_pdf || !Storage::disk('public')->exists($contrato->ruta_pdf)) {
            return back()->with('error', 'El archivo PDF no se encuentra o no ha sido generado aún.');
        }

        // 4. Mostramos el archivo en el navegador
        // response()->file() abre el PDF en el navegador en lugar de descargarlo forzosamente
        return response()->file(storage_path('app/public/' . $contrato->ruta_pdf));
    }

    // =========================================================================
    // MÉTODO PRIVADO: GESTIONAR USUARIO (Usuario = Numero Documento)
    // =========================================================================
    private function gestionarUsuario($cliente)
    {
        // 1. Buscamos si ya existe alguien con ese USERNAME (DNI)
        $usuario = User::where('username', $cliente->numero_documento)->first();
        $esNuevo = false;
        
        // La contraseña será el mismo número de documento
        $password = $cliente->numero_documento; 

        if (!$usuario) {
            // 2. CREAR NUEVO USUARIO
            $usuario = User::create([
                // Concatenamos nombre y apellido de la tabla clientes
                'name'     => $cliente->nombre . ' ' . $cliente->apellido, 
                
                'email'    => $cliente->email,
                
                // AQUÍ ESTÁ EL CAMBIO: Usamos 'username' para guardar el DNI
                'username' => $cliente->numero_documento,   
                
                'password' => Hash::make($password),
            ]);

            // Asignar Rol 'Cliente' (Spatie)
            $usuario->assignRole('Cliente');
            
            $esNuevo = true;
        }

        // 3. Vincular Cliente con Usuario (Actualizamos la FK en tabla clientes)
        $cliente->user_id = $usuario->id;
        $cliente->save();

        return [
            'usuario' => $usuario,
            'password' => $password,
            'esNuevo' => $esNuevo
        ];
    }

    // =========================================================================
    // MÉTODO PRIVADO: GENERAR PDF
    // =========================================================================
    private function generarYGuardarPDF($contrato, $cliente)
    {
        $pdf = Pdf::loadView('contratos.pdf_plantilla', [
            'contrato' => $contrato,
            'cliente' => $cliente,
            'fecha' => now()->format('d/m/Y H:i')
        ]);
        
        $pdf->setPaper('a4', 'portrait');

        // ==============================================================
        // 1. PERSONALIZAR EL NOMBRE DEL ARCHIVO (Apellido_DNI.pdf)
        // ==============================================================
        // Str::slug convierte "Pérez Gómez" en "perez-gomez" (quita tildes y espacios)
        $apellidoLimpio = Str::slug($cliente->apellido); 
        
        // Resultado: perez-gomez_12345678_contrato-55.pdf
        $nombreArchivo = strtoupper($apellidoLimpio) . '_' . $cliente->numero_documento . '_C' . $contrato->id . '.pdf';

        // ==============================================================
        // 2. MODIFICAR LA CARPETA DE DESTINO
        // ==============================================================
        // Si cambias 'contratos' por 'matriculas', se creará esa carpeta nueva.
        // También puedes organizar por año: 'contratos/2026/'
        
        $carpeta = 'contratos'; // <--- CAMBIA ESTO AL NOMBRE QUE QUIERAS
        
        $rutaStorage = $carpeta . '/' . $nombreArchivo;

        // Guardamos en el disco público (storage/app/public/matriculas/...)
        Storage::disk('public')->put($rutaStorage, $pdf->output());

        $contrato->update([
            'ruta_pdf' => $rutaStorage
        ]);

        return $rutaStorage;
    }
}