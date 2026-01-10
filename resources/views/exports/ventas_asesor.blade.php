<table>
    <thead>
    {{-- FILA 1: TÍTULO --}}
    <tr>
        <th colspan="3" style="font-size: 14px; font-weight: bold; text-align: center; background-color: #6610f2; color: white; height: 30px;">
            RANKING DE RENDIMIENTO ({{ $fechaInicio }} al {{ $fechaFin }})
        </th>
    </tr>

    {{-- FILA 2: ENCABEZADOS --}}
    <tr>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center; width: 350px;">NOMBRE DEL ASESOR</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center; width: 120px;">VENTAS</th>
        <th style="font-weight: bold; border: 1px solid #000000; background-color: #e2e3e5; text-align: center; width: 190px;">PARTICIPACIÓN</th>
    </tr>
    </thead>

    <tbody>
    @php 
        $totalGlobal = $asesores->sum('ventas_count'); 
        $posicion = 1;
    @endphp
    
    @foreach($asesores as $asesor)
        <tr>
            {{-- Nombre --}}
            <td style="border: 1px solid #cccccc;">
                {{ $posicion++ }}. {{ $asesor->name }}
            </td>
            
            {{-- Cantidad --}}
            <td style="border: 1px solid #cccccc; text-align: center; font-weight: bold;">
                {{ $asesor->ventas_count }}
            </td>
            
            {{-- Porcentaje del total --}}
            <td style="border: 1px solid #cccccc; text-align: center; color: #6c757d;">
                {{ $totalGlobal > 0 ? round(($asesor->ventas_count / $totalGlobal) * 100, 1) : 0 }}%
            </td>
        </tr>
    @endforeach

    {{-- FILA FINAL: TOTAL --}}
    <tr>
        <td style="text-align: right; font-weight: bold; border: 1px solid #000000;">TOTAL VENTAS:</td>
        <td style="text-align: center; font-weight: bold; border: 1px solid #000000; background-color: #d1e7dd;">
            {{ $totalGlobal }}
        </td>
        <td style="border: 1px solid #000000; background-color: #d1e7dd;"></td>
    </tr>
    </tbody>
</table>