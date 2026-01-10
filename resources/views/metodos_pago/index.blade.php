@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>💰 Gestión de Cuentas y QRs</h2>
        <a href="{{ route('metodos_pago.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Método
        </a>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Banco/Billetera</th>
                        <th>Datos</th>
                        <th>QR</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($metodos as $metodo)
                    <tr>
                        <td>
                            <strong>{{ $metodo->nombre_banco }}</strong><br>
                            <span class="badge bg-secondary">{{ $metodo->tipo }}</span>
                        </td>
                        <td>
                            <small class="text-muted">Titular:</small> {{ $metodo->titular }}<br>
                            <small class="text-muted">Nro:</small> <strong>{{ $metodo->numero_cuenta }}</strong>
                            @if($metodo->cci)
                                <br><small class="text-muted">CCI:</small> {{ $metodo->cci }}
                            @endif
                        </td>
                        <td>
                            @if($metodo->qr_imagen)
                                <img src="{{ asset('storage/' . $metodo->qr_imagen) }}" 
                                    alt="QR" 
                                    class="img-thumbnail" 
                                    style="height: 60px; cursor: zoom-in;"
                                    onclick="window.open(this.src)">
                            @else
                                <span class="text-muted">- Sin QR -</span>
                            @endif
                        </td>
                        <td>
                            @if($metodo->estado == 'Activo')
                                <span class="badge bg-success">ACTIVO</span>
                            @else
                                <span class="badge bg-danger">INACTIVO</span>
                            @endif
                        </td>
                        <td>
                            {{-- Botón para Activar/Desactivar --}}
                            <form action="{{ route('metodos_pago.toggle', $metodo->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                @if($metodo->estado == 'Activo')
                                    <button class="btn btn-sm btn-outline-danger" title="Desactivar">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-success" title="Activar">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection