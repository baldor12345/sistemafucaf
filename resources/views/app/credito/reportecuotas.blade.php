
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
        .linebordercenter tr th, td{
            border: 0.9px solid #3f3f3f;
            text-align : center;
            
        }
        .linebordercenter{
            padding: 2px;
            margin: 1px;
        }

        .lineborderleft tr td{
            border: 0.9px solid #3f3f3f;
            text-align : left;;
        }
    
    </style>

</head>
<body>
	<div class="">
        <table class="lineborderleft" width ="40%">
                <tr><td><strong>Nombre del Socio o Cliente</strong> </td></tr>
                <tr><td> {{ $nombres_cliente }}</td></tr>
        </table>
        <h3>Reporte de Cuotas</h3>
		<table class ="linebordercenter">
            <thead>
                <tr>
                    <th style="font-size: 8px" colspan="1">FECHA PRESTAMO</th>
                    <th style="font-size: 8px" colspan="1">Fecha de pago</th>
                    <th style="font-size: 8px" colspan="1">Numero de cuota</th>
                    <th style="font-size: 8px" colspan="1">Monto de cuota S/.</th>
                    <th style="font-size: 8px" colspan="1">Capital</th>
                    <th style="font-size: 8px" colspan="1">Interes</th>
                    <th style="font-size: 8px" colspan="1">Fecha real de pago</th>
                    <th style="font-size: 8px" colspan="1">Interes moratorio</th>
                    <th style="font-size: 8px" colspan="1">Monto real cuota S/.</th>
                    <th style="font-size: 8px" colspan="1">Saldo de capital S/.</th>
                    <th style="font-size: 8px" colspan="1">Estado de cuota</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-size: 8px" colspan="1">{{ $credito->fechai }}</td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="1">{{ $credito->valor_credito }}</td>
                    <td style="font-size: 8px" colspan="1">--</td>
                </tr>
                @foreach ($lista as $value)
                    <tr >
                        <td style="font-size: 8px" colspan="1"></td>
                        <td style="font-size: 8px" colspan="1">{{ Date::parse($value->fecha_programada_pago)->format('d/m/Y') }}</td>
                        <td style="font-size: 8px" colspan="1">{{ $value->numero_cuota }}/{{ $credito->periodo }}</td>
                        <td style="font-size: 8px" colspan="1">{{ $value->interes + $value->parte_capital }}</td>
                        <td style="font-size: 8px" colspan="1">{{ $value->parte_capital }}</td>
                        <td style="font-size: 8px" colspan="1">{{ $value->interes }}</td>
                        <td style="font-size: 8px" colspan="1">{{ ($value->fecha_pago != null)?Date::parse($value->fecha_pago)->format('d/m/Y'):""}}</td>
                        <td style="font-size: 8px" colspan="1">{{ $value->interes_mora }}</td>
                        <td style="font-size: 8px" colspan="1">{{ $value->parte_capital + $value->interes + $value->interes_mora }}</td>
                        <td style="font-size: 8px" colspan="1">{{ $value->saldo_restante }}</td>
                        <td style="font-size: 8px" colspan="1">{{ ($value->estado != 0 )?'P':'-' }}</td>
                    </tr>
                @endforeach

                <tr>
                    <td style="font-size: 8px" colspan="2">Monto Préstamo: </td>
                    <td style="font-size: 8px" colspan="1">{{ $credito->valor_credito }}</td>
                    <td style="font-size: 8px" colspan="1"> </td>
                    <td style="font-size: 8px" colspan="2">Tasa Efectiva Mensual: </td>
                    <td style="font-size: 8px" colspan="1">{{ $credito->tasa_interes }} %</td>
                    <td style="font-size: 8px" colspan="4"> </td>
                    
                </tr>
                <tr>
                    <td style="font-size: 8px" colspan="2">NUmero de Cuotas: </td>
                    <td style="font-size: 8px" colspan="1">{{ $credito->periodo }}</td>
                    <td style="font-size: 8px" colspan="1"></td>
                    <td style="font-size: 8px" colspan="2">Estado de Cuota: </td>
                    <td style="font-size: 8px" colspan="3">P=Pagada C=Pago total capital</td>
                    <td style="font-size: 8px" colspan="2"> </td>
                </tr>
            <tbody>
		</table>
	</div>
</body>
</html>