<table>
    <thead>
    {{-- FILA 1: TÍTULO PRINCIPAL MERGEADO --}}
    <tr>
        <th colspan="8" style="font-size: 16px; font-weight: bold; text-align: center; background-color: #0d6efd; color: white; height: 30px;">
            REPORTE DE VENTAS ({{ $fechaInicio }} al {{ $fechaFin }})
        </th>
    </tr>

    {{-- FILA 2: ENCABEZADOS DE COLUMNA --}}
    <tr>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">ID</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">FECHA</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">CLIENTE</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">DNI / DOC</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">PROGRAMA</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">GRUPO</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">TOTAL</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center;">VENDEDOR</th>
    </tr>
    </thead>

    <tbody>
    @foreach($ventas as $venta)
        <tr>
            <td style="border: 1px solid #cccccc; text-align: center;">{{ $venta->id }}</td>
            <td style="border: 1px solid #cccccc; text-align: center;">{{ $venta->fecha_venta->format('d/m/Y') }}</td>
            <td style="border: 1px solid #cccccc;">{{ $venta->cliente->nombre }} {{ $venta->cliente->apellido }}</td>
            <td style="border: 1px solid #cccccc; text-align: left;">{{ $venta->cliente->numero_documento }}</td>
            <td style="border: 1px solid #cccccc;">{{ $venta->grupo->programa->codigo ?? '-' }}</td>
            <td style="border: 1px solid #cccccc; text-align: center;">{{ $venta->grupo->codigo_grupo }}</td>
            
            {{-- Formato Moneda --}}
            <td style="border: 1px solid #cccccc; text-align: right;">
                {{ number_format($venta->costo_total_venta, 2) }}
            </td>
            
            <td style="border: 1px solid #cccccc;">{{ $venta->vendedor->name ?? 'Sistema' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>