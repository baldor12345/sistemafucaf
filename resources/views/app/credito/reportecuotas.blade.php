
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
                    <td style="font-size: 8px" colspan="2">Monto Préstamo: </td>
                    <td style="font-size: 8px" colspan="1">{{ round($credito->valor_credito, 1) }}</td>
                    <td style="font-size: 8px" colspan="1"> </td>
                    <td style="font-size: 8px" colspan="2">Tasa Efectiva Mensual: </td>
                    <td style="font-size: 8px" colspan="1">{{ round($credito->tasa_interes, 1) }} %</td>
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
                <tr>
                    <th style="font-size: 8px" colspan="1"><strong>FECHA PRESTAMO</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Fecha de pago</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Numero de cuota</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Monto de cuota S/.</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Capital</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Interes</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Fecha real de pago</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Interes moratorio</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Monto real cuota S/.</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Saldo de capital S/.</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>Estado de cuota</strong></th>
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
                    <td style="font-size: 8px" colspan="1">{{ round($credito->valor_credito, 1) }}</td>
                    <td style="font-size: 8px" colspan="1">--</td>
                </tr>
                @foreach ($lista as $value)
                    <tr >
                        <td style="font-size: 8px" colspan="1"></td>
                        <td style="font-size: 8px" colspan="1">{{ Date::parse($value->fecha_programada_pago)->format('d/m/Y') }}</td>
                        <td style="font-size: 8px" colspan="1">{{ $value->numero_cuota }}/{{ $credito->periodo }}</td>
                        <td style="font-size: 8px" colspan="1">{{ round($value->interes + $value->parte_capital, 1) }}</td>
                        <td style="font-size: 8px" colspan="1">{{ round($value->parte_capital, 1) }}</td>
                        <td style="font-size: 8px" colspan="1">{{ round($value->interes, 1) }}</td>
                        <td style="font-size: 8px" colspan="1">{{ ($value->fecha_pago != null)?Date::parse($value->fecha_pago)->format('d/m/Y'):""}}</td>
                        <td style="font-size: 8px" colspan="1">{{ round($value->interes_mora, 1) }}</td>
                        <td style="font-size: 8px" colspan="1">{{ round($value->parte_capital + $value->interes + $value->interes_mora, 1) }}</td>
                        <td style="font-size: 8px" colspan="1">{{ round($value->saldo_restante, 1) }}</td>
                        <td style="font-size: 8px" colspan="1">{{ ($value->estado != 0 )?'P':'-' }}</td>
                    </tr>
                @endforeach

               
            <tbody>
		</table>
	</div>
</body>
</html>