<table>
    <thead>
    {{-- FILA 1: TÍTULO PRINCIPAL MERGEADO --}}
    <tr>
        <th colspan="7" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #198754; color: white; height: 30px;">
            REPORTE DE INGRESOS ({{ $fechaInicio }} al {{ $fechaFin }})
        </th>
    </tr>

    {{-- FILA 2: ENCABEZADOS DE COLUMNA --}}
    <tr>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center; width: 20px;">FECHA PAGO</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center; width: 30px;">CLIENTE</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">CONCEPTO</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">PROGRAMA</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">MONTO (S/)</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">MÉTODO</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">N° OPERACIÓN</th>
    </tr>
    </thead>

    <tbody>
    @foreach($cuotas as $cuota)
        <tr>
            <td style="border: 1px solid #cccccc; text-align: center;">
                {{ $cuota->fecha_pago ? $cuota->fecha_pago->format('d/m/Y H:i') : '-' }}
            </td>
            <td style="border: 1px solid #cccccc;">
                {{ $cuota->venta->cliente->nombre_completo ?? 'Cliente eliminado' }}
            </td>
            <td style="border: 1px solid #cccccc;">
                {{ $cuota->descripcion }}
            </td>
            <td style="border: 1px solid #cccccc;">
                {{ $cuota->venta->grupo->programa->nombre ?? '-' }}
            </td>
            
            {{-- Columna de Dinero --}}
            <td style="border: 1px solid #cccccc; text-align: right;">
                {{ number_format($cuota->monto_cuota, 2) }}
            </td>
            
            <td style="border: 1px solid #cccccc; text-align: center;">
                {{ $cuota->metodo_pago }}
            </td>
            <td style="border: 1px solid #cccccc; text-align: left;">
                {{ $cuota->transaccion_id }}
            </td>
        </tr>
    @endforeach
    
    {{-- FILA FINAL: TOTAL SUMADO --}}
    <tr>
        <td colspan="4" style="text-align: right; font-weight: bold; border: 1px solid #000000;">TOTAL INGRESOS:</td>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #d1e7dd;">
            {{ number_format($cuotas->sum('monto_cuota'), 2) }}
        </td>
        <td colspan="2" style="border: 1px solid #000000;"></td>
    </tr>
    </tbody>
</table>