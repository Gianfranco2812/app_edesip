@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestión de Grupos (Vendibles)</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Listado de Grupos</h4>
                <a href="{{ route('grupos.create') }}" class="btn btn-primary btn-round ms-auto">
                <i class="fa fa-plus"></i>
                Crear Nuevo Grupo
                </a>
            </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover">
                <thead>
                    <tr>
                    <th>Código</th>
                    <th>Programa</th>
                    <th>F. Inicio</th>
                    <th>Costo Total</th>
                    <th>Cuotas</th>
                    <th>Modalidad</th>
                    <th>Estado</th>
                    <th style"width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupos as $grupo)
                    <tr>
                    <td>{{ $grupo->codigo_grupo }}</td>
                    {{-- Usamos la relación 'programa' --}}
                    <td>{{ $grupo->programa->nombre ?? 'N/A' }}</td>
                    <td>{{ $grupo->fecha_inicio->format('d/m/Y') }}</td>
                    <td>S/ {{ number_format($grupo->costo_total, 2) }}</td>
                    <td>{{ $grupo->numero_cuotas }}</td>
                    <td>{{ $grupo->modalidad }}</td>
                    <td>
                        {{-- (Puedes añadir badges de colores aquí según el estado) --}}
                        {{ $grupo->estado }}
                    </td>
                    <td>
                        <div class="form-button-action">
                        <a href="{{ route('grupos.edit', $grupo->id) }}" data-bs-toggle="tooltip" title="Editar" class="btn btn-link btn-primary btn-lg">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('grupos.destroy', $grupo->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" data-bs-toggle="tooltip" title="Eliminar" class="btn btn-link btn-danger" onclick="return confirm('¿Estás seguro?');">
                            <i class="fa fa-times"></i>
                            </button>
                        </form>
                        </div>
                    </td>
                    </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection