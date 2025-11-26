@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">Listado de Clientes</h4>
                <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-round ms-auto">
                <i class="fa fa-plus"></i>
                Registrar
                </a>
            </div>
            </div>
            <div class="card-body">
            <div class="table-responsive">
                <table id="basic-datatables" class="display table table-striped table-hover">
                <thead>
                    <tr>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    @role('Admin') {{-- El Admin ve quién lo registró --}}
                        <th>Registrado por</th>
                    @endrole
                    <th style="width: 10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($clientes as $cliente)
                    <tr>
                    <td>{{ $cliente->nombre_completo }}</td>
                    <td>{{ $cliente->email }}</td>
                    <td>{{ $cliente->telefono }}</td>
                    <td>
                        {{-- (Aquí puedes poner badges de colores según el estado) --}}
                        {{ $cliente->estado }}
                    </td>
                    @role('Admin')
                        <td>{{ $cliente->vendedor->name ?? 'N/A' }}</td>
                    @endrole
                    <td>
                        <div class="form-button-action">
                        <a href="{{ route('ventas.create', ['cliente_id' => $cliente->id]) }}" 
                            data-bs-toggle="tooltip" 
                            title="Nueva Venta / Matricular" 
                            class="btn btn-link btn-success btn-lg">
                            <i class="fas fa-cart-plus"></i>
                        </a>    
                        <a href="{{ route('clientes.edit', $cliente->id) }}" data-bs-toggle="tooltip" title="Editar" class="btn btn-link btn-primary btn-lg">
                            <i class="fa fa-edit"></i>
                        </a>
                        {{-- (Falta el 'show' o detalle) --}}
                        <a href="#" data-bs-toggle="tooltip" title="Ver Detalle" class="btn btn-link btn-info btn-lg">
                            <i class="fa fa-eye"></i>
                        </a>
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