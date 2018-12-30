
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
	.contenedor{
			position: absolute;
			top:50%;
			left:50%;
			width:200px;
			margin-left:-200px;
			/*determinamos una altura*/
			height:300px;
			/*indicamos que el margen superior, es la mitad de la altura*/
			margin-top:-150px;
			border:1px solid #808080;
			padding:5px;
		}
</style>

</head>
<body>
	<div class="">
            <div class="contenedor">
                <h3>Historico de ahorros en el año : {{ $anio }}</h3>
                <h3>Nombres del Socio o Cliente</h3>
                <h3>{{ $persona->nombres.' '.$persona->apellidos }}</h3>
            </div>
		<table border="0" cellspacing="3" cellpadding="2" style="margin: 50px;" class="table table-striped">
            <thead>
                <tr>
                    <th style="font-size: 8px" colspan="1"></th>
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