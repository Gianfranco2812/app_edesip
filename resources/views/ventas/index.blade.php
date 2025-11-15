@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestión de Ventas</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Listado de Ventas</h4>
                {{-- (Por ahora no tenemos el 'create') --}}
                <a href="#" class="btn btn-primary btn-round ms-auto" style="display: none;">
                <i class="fa fa-plus"></i>
                Registrar Venta
                </a>
            </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover">
                <thead>
                    <tr>
                    <th>ID Venta</th>
                    <th>Cliente</th>
                    <th>Programa</th>
                    <th>Grupo (Código)</th>
                    <th>Costo Total</th>
                    <th>Estado Venta</th>
                    <th>Contrato</th>
                    @role('Admin')
                        <th>Asesor</th>
                    @endrole
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                    <tr>
                    <td>{{ $venta->id }}</td>
                    <td>{{ $venta->cliente->nombre_completo ?? 'N/A' }}</td>
                    <td>{{ $venta->grupo->programa->nombre ?? 'N/A' }}</td>
                    <td>{{ $venta->grupo->codigo_grupo ?? 'N/A' }}</td>
                    <td>S/ {{ number_format($venta->costo_total_venta, 2) }}</td>
                    <td>
                        {{-- (Badges de colores) --}}
                        @if($venta->estado == 'Cerrada')
                        <span class="badge bg-success">Cerrada</span>
                        @elseif($venta->estado == 'En Proceso')
                        <span class="badge bg-warning text-dark">En Proceso</span>
                        @else
                        <span class="badge bg-danger">Anulada</span>
                        @endif
                    </td>
                    <td>
                        {{-- (Estado del contrato) --}}
                        @if($venta->contrato && $venta->contrato->estado == 'Confirmado')
                        <span class="badge bg-primary">Firmado</span>
                        @else
                        <span class="badge bg-secondary">Pendiente</span>
                        @endif
                    </td>
                    @role('Admin')
                        <td>{{ $venta->vendedor->name ?? 'N/A' }}</td>
                    @endrole
                    <td>
                        <div class="form-button-action">
                        <a href="#" data-bs-toggle="tooltip" title="Ver Detalle" class="btn btn-link btn-info btn-lg">
                            <i class="fa fa-eye"></i>
                        </a>
                        </div>
                    </td>
                    </tr>
                    @empty
                    <tr>
                    <td colspan="{{ Auth::user()->hasRole('Admin') ? '9' : '8' }}" class="text-center">No hay ventas registradas.</td>
                    </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection