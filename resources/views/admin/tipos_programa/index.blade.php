@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Tipos de Programa</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Listado de Tipos</h4>
                <a href="{{ route('tipos-programa.create') }}" class="btn btn-primary btn-round ms-auto">
                <i class="fa fa-plus"></i>
                Crear Nuevo Tipo
                </a>
            </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover">
                <thead>
                    <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tipos as $tipo)
                    <tr>
                    <td>{{ $tipo->id }}</td>
                    <td>{{ $tipo->nombre }}</td>
                    <td>
                        <div class="form-button-action">
                        <a href="{{ route('tipos-programa.edit', $tipo->id) }}" data-bs-toggle="tooltip" title="Editar" class="btn btn-link btn-primary btn-lg">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('tipos-programa.destroy', $tipo->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type-="submit" data-bs-toggle="tooltip" title="Eliminar" class="btn btn-link btn-danger" onclick="return confirm('¿Estás seguro?');">
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