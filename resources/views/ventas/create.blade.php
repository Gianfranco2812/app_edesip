@extends('layouts.admin')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Registrar Nueva Matrícula</h3>
    </div>
    <div class="row">
        <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <form action="{{ route('ventas.store') }}" method="POST">
                @csrf

                <h4 class="fw-bold mb-3">1. Cliente a Matricular</h4>
                <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                    <label class="form-label">Nombre del Cliente</label>
                    <input type="text" class="form-control" value="{{ $cliente->nombre_completo }}" readonly>
                    {{-- Este es el campo que SÍ se envía --}}
                    <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="text" class="form-control" value="{{ $cliente->email }}" readonly>
                    </div>
                </div>
                </div>

                <hr>

                <h4 class="fw-bold mb-3">2. Seleccionar Grupo</h4>
                <div class="row">
                <div class="col-md-10">
                    <div class="mb-3">
                    <label for="grupo_id" class="form-label">Grupo Disponible</label>
                    <select class="form-select @error('grupo_id') is-invalid @enderror" id="grupo_id" name="grupo_id" required>
                        <option value="" disabled selected>Selecciona un grupo...</option>
                        @foreach($gruposDisponibles as $grupo)
                        {{-- 
                            Guardamos los datos del grupo en atributos 'data-'
                            para usarlos con JavaScript.
                        --}}
                        <option value="{{ $grupo->id }}" 
                                data-costo="{{ $grupo->costo_total }}"
                                data-matricula="{{ $grupo->costo_matricula }}"
                                data-cuotas="{{ $grupo->numero_cuotas }}"
                                data-promo="{{ $grupo->texto_promocional }}">
                            {{ $grupo->programa->nombre }} (Código: {{ $grupo->codigo_grupo }})
                        </option>
                        @endforeach
                    </select>
                    @error('grupo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                </div>

                <div class="row mt-3" id="detalles_grupo" style="display: none;">
                <h5 class="fw-bold mb-3 text-primary">Resumen Financiero del Grupo</h5>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label class="form-label">Costo Total (S/)</label>
                    <input type="text" class="form-control" id="grupo_costo" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label class="form-label">Matrícula (S/)</label>
                    <input type="text" class="form-control" id="grupo_matricula" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label class="form-label">N° de Cuotas</label>
                    <input type="text" class="form-control" id="grupo_cuotas" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                    <label class="form-label">Promoción</label>
                    <input type="text" class="form-control" id="grupo_promo" readonly>
                    </div>
                </div>
                </div>
                
                <div class="text-end mt-4">
                <a href="{{ route('clientes.index') }}" class="btn btn-danger">Cancelar Matrícula</a>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-file-signature"></i>
                    Generar Contrato y Matricular
                </button>
                </div>
            </form>

            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Este script hace que los detalles del grupo aparezcan cuando lo seleccionas --}}
<script>
    document.getElementById('grupo_id').addEventListener('change', function() {
        // Busca la opción seleccionada
        const selectedOption = this.options[this.selectedIndex];
        
        // Si la opción no es la de "Selecciona...")
        if (selectedOption.value) {
        // Muestra el div de detalles
        document.getElementById('detalles_grupo').style.display = 'flex';
        
        // Rellena los campos con los datos del 'data-'
        document.getElementById('grupo_costo').value = selectedOption.dataset.costo;
        document.getElementById('grupo_matricula').value = selectedOption.dataset.matricula;
        document.getElementById('grupo_cuotas').value = selectedOption.dataset.cuotas;
        document.getElementById('grupo_promo').value = selectedOption.dataset.promo || 'N/A';
        } else {
        // Oculta si vuelven a "Selecciona..."
        document.getElementById('detalles_grupo').style.display = 'none';
        }
    });
</script>
@endpush