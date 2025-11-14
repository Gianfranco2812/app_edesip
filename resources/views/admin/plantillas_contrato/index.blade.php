@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Plantillas de Contrato</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Listado de Plantillas</h4>
                <a href="{{ route('plantillas-contrato.create') }}" class="btn btn-primary btn-round ms-auto">
                <i class="fa fa-plus"></i>
                Crear Nueva Plantilla
                </a>
            </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover">
                <thead>
                    <tr>
                    <th>ID</th>
                    <th>Nombre de Plantilla</th>
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($plantillas as $plantilla)
                    <tr>
                    <td>{{ $plantilla->id }}</td>
                    <td>{{ $plantilla->nombre_plantilla }}</td>
                    <td>
                        <div class="form-button-action">
                        <a href="{{ route('plantillas-contrato.edit', $plantilla->id) }}" data-bs-toggle="tooltip" title="Editar" class="btn btn-link btn-primary btn-lg">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('plantillas-contrato.destroy', $plantilla->id) }}" method="POST" style="display:inline;">
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