@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Agregar Método de Pago</h2>
    <div class="card p-4">
        <form action="{{ route('metodos_pago.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Nombre Banco / Billetera</label>
                    <input type="text" name="nombre_banco" class="form-control" placeholder="Ej: BCP, Yape, Plin" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="Cuenta Bancaria">Cuenta Bancaria</option>
                        <option value="Billetera Digital">Billetera Digital (Yape/Plin)</option>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Número de Cuenta / Celular</label>
                    <input type="text" name="numero_cuenta" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Titular de la Cuenta</label>
                    <input type="text" name="titular" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label>Imagen del QR (Opcional)</label>
                <input type="file" name="qr_imagen" class="form-control" accept="image/*">
                <small class="text-muted">Sube la captura del QR de Yape o Plin.</small>
            </div>

            <div class="mb-3">
                <label>Estado Inicial</label>
                <select name="estado" class="form-select">
                    <option value="Activo">Activo (Visible para alumnos)</option>
                    <option value="Inactivo">Inactivo (Oculto)</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Guardar Cuenta</button>
        </form>
    </div>
</div>
@endsection