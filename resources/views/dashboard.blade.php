@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    
    {{-- ENCABEZADO --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Panel de Control</h1>
            <p class="text-muted mb-0">Bienvenido, {{ Auth::user()->name }}</p>
        </div>
        <div class="text-end">
            <small class="d-block text-muted">{{ now()->translatedFormat('l d \d\e F, Y') }}</small>
        </div>
    </div>

    {{-- FILA 1: TARJETAS KPI --}}
    <div class="row">

        {{-- 1. INGRESOS --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-4 border-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                Ingresos (Mes Actual)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">S/ {{ number_format($ingresosMes, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sack-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. POR VALIDAR (Vouchers subidos) --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-4 border-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">
                                Vouchers por Validar</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $pagosPendientesValidar }}</div>
                        </div>
                        <div class="col-auto">
                            @if($pagosPendientesValidar > 0)
                                <i class="fas fa-bell fa-2x text-danger fa-beat"></i>
                            @else
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            @endif
                        </div>
                    </div>
                    @if($pagosPendientesValidar > 0)
                        {{-- OJO: Asegúrate de tener una ruta para ir a validar pagos, o mándalo a Ventas --}}
                        <a href="{{ route('cobranzas.index') }}" class="stretched-link text-danger text-decoration-none small" style="position: relative; z-index: 2;">
                            Ver pagos pendientes <i class="fas fa-arrow-right"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- 3. VENTAS (MATRÍCULAS) --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-4 border-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                Matrículas Nuevas</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $ventasMes }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. PROSPECTOS --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-start border-4 border-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                Prospectos Activos</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalProspectos }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILA 2: TABLA Y ACCESOS --}}
    <div class="row">
        
        <div class="row mb-4">
            @role('Admin')
            {{-- 1. GRÁFICO VENTAS (AZUL) --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                        <h6 class="m-0 fw-bold text-primary">🏆 Top Ventas</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="position: relative; height: 200px;">
                            <canvas id="myBarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. GRÁFICO PROSPECTOS (CYAN) --}}
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom-info">
                        <h6 class="m-0 fw-bold text-info">👥 Cartera Prospectos</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area" style="position: relative; height: 200px;">
                            <canvas id="myProspectsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            @endrole
            {{-- 3. ACCESOS DIRECTOS (IGUAL QUE ANTES) --}}
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 fw-bold text-secondary">⚡ Accesos Rápidos</h6>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="d-grid gap-3">
                            <a href="{{ route('clientes.create') }}" class="btn btn-light border text-start py-2">
                                <i class="fas fa-user-plus text-success me-2"></i> Registrar Prospecto
                            </a>
                            <a href="{{ route('ventas.index') }}" class="btn btn-light border text-start py-2">
                                <i class="fas fa-receipt text-primary me-2"></i> Gestionar Pagos/Ventas
                            </a>
                            
                            @if(Auth::user()->hasRole('Admin'))
                                <a href="{{ route('metodos_pago.index') }}" class="btn btn-light border text-start py-2">
                                    <i class="fas fa-wallet text-dark me-2"></i> Configurar Cuentas y QRs
                                </a>
                            @endif
                        </div>
                        
                        <hr>
                        <div class="alert alert-info d-flex align-items-center mb-0 p-2 small">
                            <i class="fas fa-info-circle fa-lg me-2"></i>
                            <span>Revisa la bandeja de <b>Vouchers</b> antes del cierre.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                        <h6 class="m-0 fw-bold text-primary">📋 Últimas 5 Matrículas Registradas</h6>
                        <a href="{{ route('ventas.create') }}" class="btn btn-sm btn-primary shadow-sm">
                            <i class="fas fa-plus"></i> Nueva Venta
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Alumno</th>
                                        <th>Programa / Grupo</th>
                                        
                                        {{-- COLUMNA SOLO PARA ADMIN --}}
                                        @role('Admin')
                                            <th>Asesor (Vendedor)</th>
                                        @endrole

                                        <th>Contrato</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ultimasVentas as $venta)
                                        <tr>
                                            {{-- Fecha --}}
                                            <td class="small">{{ $venta->fecha_venta->format('d/m H:i') }}</td>
                                            
                                            {{-- Alumno --}}
                                            <td class="fw-bold text-dark">
                                                {{ Str::limit($venta->cliente->nombre_completo ?? $venta->cliente->nombre . ' ' . $venta->cliente->apellido, 30) }}
                                            </td>
                                            
                                            {{-- Programa --}}
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="badge bg-light text-dark border mb-1">
                                                        {{ $venta->grupo->programa->nombre ?? 'Curso' }}
                                                    </span>
                                                    <small class="text-muted" style="font-size: 0.75rem;">
                                                        Grp: {{ $venta->grupo->codigo_grupo ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </td>

                                            {{-- ASESOR (Solo Admin) --}}
                                            @role('Admin')
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-user-tie"></i> {{ $venta->vendedor->name ?? 'Sistema' }}
                                                    </span>
                                                </td>
                                            @endrole

                                            {{-- Contrato (Botones Lógicos) --}}
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if($venta->contrato && $venta->contrato->estado == 'Pendiente' && $venta->estado != 'Anulada')
                                                        <a href="{{ route('ventas.previsualizar', $venta->id) }}" 
                                                        class="btn btn-warning fw-bold"
                                                        data-bs-toggle="tooltip" 
                                                        title="Gestionar Firma">
                                                            <i class="fas fa-file-signature"></i>
                                                        </a>
                                                    @endif

                                                    @if($venta->contrato && $venta->contrato->ruta_pdf)
                                                        <a href="{{ route('contratos.ver_pdf', $venta->contrato->id) }}" 
                                                        target="_blank"
                                                        class="btn btn-danger" 
                                                        data-bs-toggle="tooltip"
                                                        title="Ver PDF Firmado">
                                                            <i class="fas fa-file-pdf"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- Acciones Generales --}}
                                            <td class="text-end pe-3">
                                                <div class="form-button-action">
                                                        <a href="{{ route('cobranzas.show', $venta->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Cobranza">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                    {{-- ELIMINAR (Solo Admin) --}}
                                                    @role('Admin')
                                                        <form action="{{ route('ventas.destroy', $venta->id) }}" method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-link text-danger" title="Anular Venta" onclick="return confirm('¿Estás seguro de anular esta venta? Esto afectará la caja.');">
                                                                <i class="fa fa-times"></i>
                                                            </button>
                                                        </form>
                                                    @endrole
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="@if(Auth::user()->hasRole('Admin')) 6 @else 5 @endif" class="text-center py-5 text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3 text-gray-300"></i><br>
                                                No hay matrículas registradas recientemente.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-center">
                        <a href="{{ route('ventas.index') }}" class="text-decoration-none small fw-bold">Ver historial completo <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        var ctx1 = document.getElementById("myBarChart");
        if (ctx1) {
            var nombres = {!! json_encode($labelsGrafico ?? []) !!};
            var ventas  = {!! json_encode($dataGrafico ?? []) !!};

            new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: nombres,
                    datasets: [{
                        label: "Ventas",
                        backgroundColor: "#4e73df", // AZUL
                        hoverBackgroundColor: "#2e59d9",
                        borderColor: "#4e73df",
                        data: ventas,
                        barPercentage: 0.6,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 6 } },
                        y: { ticks: { min: 0, maxTicksLimit: 5, padding: 10, callback: function(val) { if(val%1===0)return val; } }, grid: { borderDash: [2], drawBorder: false } },
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // ----------------------------------------------------
        // GRÁFICO 2: PROSPECTOS (TURQUESA) - ¡NUEVO!
        // ----------------------------------------------------
        var ctx2 = document.getElementById("myProspectsChart");
        if (ctx2) {
            var nombresP = {!! json_encode($labelsProspectos ?? []) !!};
            var countsP  = {!! json_encode($dataProspectos ?? []) !!};

            new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: nombresP,
                    datasets: [{
                        label: "Prospectos",
                        backgroundColor: "#36b9cc", // TURQUESA / CYAN
                        hoverBackgroundColor: "#2c9faf",
                        borderColor: "#36b9cc",
                        data: countsP,
                        barPercentage: 0.6,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                    scales: {
                        x: { grid: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 6 } },
                        y: { 
                            ticks: { 
                                min: 0, 
                                maxTicksLimit: 5, 
                                padding: 10, 
                                callback: function(val) { if(val%1===0)return val; } 
                            }, 
                            grid: { 
                                color: "rgb(234, 236, 244)", 
                                borderDash: [2], 
                                drawBorder: false 
                            } 
                        },
                    },
                    plugins: { 
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: "rgb(255,255,255)",
                            bodyColor: "#858796",
                            titleColor: '#6e707e',
                            borderColor: '#dddfeb',
                            borderWidth: 1,
                            displayColors: false,
                        }
                    }
                }
            });
        }

    });
</script>
@endsection