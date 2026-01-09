<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body { font-family: sans-serif; font-size: 12px; line-height: 1.5; }
        .firma-digital { 
            margin-top: 50px; 
            border: 1px dashed #000; 
            padding: 15px; 
            text-align: center; 
            background-color: #f0f0f0; 
        }
    </style>
</head>
<body>
    {{-- LOGO --}}
    <div style="text-align: center; margin-bottom: 20px;">
        <h2>EDESIP S.A.C.</h2>
    </div>

    {{-- CONTENIDO DE LA PLANTILLA --}}
    <div>
        {!! $contrato->contenido_generado !!}
    </div>

    {{-- SELLO DE FIRMA --}}
    <div class="firma-digital">
        <strong>FIRMADO DIGITALMENTE</strong><br>
        Por: {{ $cliente->nombre }} {{ $cliente->apellido }}<br>
        
        {{ $cliente->tipo_documento }}: {{ $cliente->numero_documento }}<br>
        
        Fecha: {{ $fecha }}<br>
        <small>IP: {{ request()->ip() }} | Token: {{ $contrato->token_acceso }}</small>
    </div>
</body>
</html>