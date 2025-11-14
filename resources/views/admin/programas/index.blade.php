@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Catálogo de Programas</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Listado de Programas</h4>
                <a href="{{ route('programas.create') }}" class="btn btn-primary btn-round ms-auto">
                <i class="fa fa-plus"></i>
                Crear Nuevo Programa
                </a>
            </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover">
                <thead>
                    <tr>
                    <th>Nombre del Programa</th>
                    <th>Tipo</th>
                    <th>Plantilla Contrato</th>
                    <th>Horas</th>
                    <th>Estado</th>
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($programas as $programa)
                    <tr>
                    <td>{{ $programa->nombre }}</td>
                    {{-- Usamos las relaciones que cargamos en el controlador --}}
                    <td>{{ $programa->tipoPrograma->nombre ?? 'N/A' }}</td>
                    <td>{{ $programa->plantillaContrato->nombre_plantilla ?? 'N/A' }}</td>
                    <td>{{ $programa->horas_totales }}</td>
                    <td>
                        @if($programa->estado == 'Activo')
                        <span class="badge bg-success">Activo</span>
                        @else
                        <span class="badge bg-secondary">Archivado</span>
                        @endif
                    </td>
                    <td>
                        <div class="form-button-action">
                        <a href="{{ route('programas.edit', $programa->id) }}" data-bs-toggle="tooltip" title="Editar" class="btn btn-link btn-primary btn-lg">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('programas.destroy', $programa->id) }}" method="POST" style="display:inline;">
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