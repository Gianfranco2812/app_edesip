<table>
    <thead>
    {{-- FILA 1: TÍTULO ROJO --}}
    <tr>
        <th colspan="8" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #dc3545; color: white; height: 30px;">
            REPORTE DE MOROSIDAD / DEUDAS (Al {{ now()->format('d/m/Y') }})
        </th>
    </tr>

    {{-- FILA 2: ENCABEZADOS --}}
    <tr>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; text-align: center; width: 20px;">VENCIMIENTO</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; text-align: center; width: 15px;">DÍAS</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; text-align: center; width: 35px;">ALUMNO</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; text-align: center; width: 20px;">TELÉFONO</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; text-align: center;">PROGRAMA</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; text-align: center;">CONCEPTO</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; text-align: center;">DEUDA (S/)</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; text-align: center;">VENDEDOR</th>
    </tr>
    </thead>

    <tbody>
    @foreach($cuotas as $cuota)
        @php
            $diasRetraso = now()->diffInDays($cuota->fecha_vencimiento);
        @endphp
        <tr>
            <td style="border: 1px solid #cccccc; text-align: center;">
                {{ $cuota->fecha_vencimiento->format('d/m/Y') }}
            </td>
            
            {{-- Resaltar si el retraso es grave (+30 días) --}}
            <td style="border: 1px solid #cccccc; text-align: center; color: {{ $diasRetraso > 30 ? 'red' : 'black' }}; font-weight: {{ $diasRetraso > 30 ? 'bold' : 'normal' }}">
                {{ $diasRetraso }}
            </td>

            <td style="border: 1px solid #cccccc;">
                {{ $cuota->venta->cliente->nombre_completo ?? 'N/A' }}
            </td>
            
            {{-- Teléfono importante para cobrar --}}
            <td style="border: 1px solid #cccccc; text-align: center;">
                {{ $cuota->venta->cliente->telefono ?? '-' }}
            </td>

            <td style="border: 1px solid #cccccc;">
                {{ $cuota->venta->grupo->programa->nombre ?? '-' }}
            </td>
            <td style="border: 1px solid #cccccc;">
                {{ $cuota->descripcion }}
            </td>

            <td style="border: 1px solid #cccccc; text-align: right;">
                {{ number_format($cuota->monto_cuota, 2) }}
            </td>

            <td style="border: 1px solid #cccccc;">
                {{ $cuota->venta->vendedor->name ?? 'Sistema' }}
            </td>
        </tr>
    @endforeach

    {{-- FILA FINAL: TOTAL DEUDA --}}
    <tr>
        <td colspan="6" style="text-align: right; font-weight: bold; border: 1px solid #000000;">TOTAL CARTERA VENCIDA:</td>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000; background-color: #f8d7da; color: red;">
            {{ number_format($cuotas->sum('monto_cuota'), 2) }}
        </td>
        <td style="border: 1px solid #000000;"></td>
    </tr>
    </tbody>
</table>