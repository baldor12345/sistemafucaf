
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
	table{
        border-collapse: collapse;
    }
    td{
        font-size: 10px;
    }
    h1{
        font-size: 21px;
        text-align:center;
        font-weight: bold;
    }
    .tabla2 {
        margin-bottom: 10px;
    }

    .tabla3 td{
        border: 0.9px solid #000;
        text-align : left;;
    }
    .emisor{
        color: red;
    }
    .linea{
        border-bottom: 1px dotted #000;
    }
    .border{
        border: 1px solid #000;
    }
    .fondo{
        background-color: #dfdfdf;
    }
    .fisico{
        color: #fff;
    }
    .fisico td{
        color: #fff;
    }
    .fisico .border{
        border: 1px solid #fff;
    }
    .fisico .tabla3 td{
        border: 1px solid #fff;
    }
    .fisico .linea{
        border-bottom: 1px dotted #fff;
    }
</style>

</head>
<body>
	<div class="contenedor">
		<table border="0" cellspacing="3" cellpadding="2" style="margin: 50px;" class="table table-striped">
			<tr>
			<td style="text-transform: uppercase;" align="center" style="font-size: 15px" colspan="4">EGRESOS DEL MES DE {{ $mes }} DE {{ $anio }}</td>
			</tr>
		</table>
	</div>

	<table width="100%" class="tabla3">
            <tr>
                <td rowspan="2" cellspacing="1" width="6%" align="center" style="font-size: 10px" class="fondo"><strong>Item</strong></td>
				<td rowspan="2" cellspacing="1" width="28%" align="center" style="font-size: 10px" class="fondo"><strong>Concepto y/o Nombre del Socio o Cliente</strong></td>
                <td rowspan="2" cellspacing="1" width="8%" align="center" style="font-size: 10px" class="fondo"><strong>Retiros Ahorros S/.</strong></td>
                <td rowspan="2" cellspacing="1" width="8%" align="center" style="font-size: 10px" class="fondo"><strong>Préstamos S/.</strong></td>
                <td rowspan="2" cellspacing="2" width="8%" align="center" style="font-size: 10px" class="fondo"><strong>Interes Pagado S/.</strong></td>
				<td rowspan="2" cellspacing="2" width="8%" align="center" style="font-size: 10px" class="fondo"><strong>Gastos Admin. S/.</strong></td>
				<td rowspan="2" cellspacing="2" width="8%" align="center" style="font-size: 10px" class="fondo"><strong>Utilidad Distrib. S/.</strong></td>
				<td cellspacing="2" width="16%" align="center" style="font-size: 10px" class="fondo"><strong>OTROS</strong></td>
				<td rowspan="2" width="10%" align="center" style="font-size: 10px" class="fondo"><strong>Total Egresos</strong></td>
			</tr>
			<tr>
				<td width="6%" align="center" style="font-size: 10px" class="fondo"><strong>S/.</strong></td>
				<td width="10%" align="center" style="font-size: 10px" class="fondo"><strong>Especificar</strong></td>
			</tr>
            @foreach($lista as $value )
            <tr>
				<td width="6%" align="center" style="font-size: 10px" > <span class="text">{{ $loop->iteration }}</span></td>
				<td width="28%" align="left" style="font-size: 10px"><span class="text">{{$value->persona_nombres.' '.$value->persona_apellidos}}</span></td>
				@if($value->monto_ahorro != '')
				<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->monto_ahorro,1) }}</span></td>
				@else
				<td width="8%" align="center" style="font-size: 9px"><span class="text">-</span></td>
				@endif
				@if($value->monto_credito != '')
				<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->monto_credito,1) }}</span></td>
				@else
				<td width="8%" align="center" style="font-size: 9px"><span class="text"></span>-</td>
				@endif
				@if($value->interes_ahorro)
				<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->interes_ahorro,1) }}</span></td>
				@else
				<td width="8%" align="center" style="font-size: 9px"><span class="text">-</span></td>
				@endif
				
				<td width="8%" align="center" style="font-size: 9px"><span class="text">-</span></td>
				@if($value->utilidad_distribuida != '')
				<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->utilidad_distribuida,1) }}</span></td>
				@else
				<td width="8%" align="center" style="font-size: 9px"><span class="text">-</span></td>
				@endif
				@if($value->otros_egresos != '')
				<td width="6%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->otros_egresos,1) }} </span></td>
				@else
				<td width="6%" align="center" style="font-size: 9px"><span class="text">-</span></td>
				@endif
				<td width="10%" align="center" style="font-size: 9px"><span class="text">-</span></td>
				<td width="10%" align="center" style="font-size: 9px">
					<span class="text">
					{{ round($value->monto_ahorro,1) + round($value->monto_credito,1) + round($value->interes_ahorro,1) + round($value->utilidad_distribuida,1) + round($value->otros_egresos,1) }}
					</span>
				</td>
            </tr>
			@endforeach

			@foreach($lista_por_conceptoAdmin as $value )
            <tr>
				<td width="6%" align="center"><span class="text">-</span></td>
				<td width="28%" align="left"><span class="text">{{ $value->concepto_titulo }}</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->transaccion_monto,1) }}</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="6%" align="center"><span class="text">-</span></td>
				<td width="10%" align="center" style="font-size: 8px"><span class="text">{{$value->comentario }}</span></td>
				<td width="10%" align="center">
					<span class="text">
							{{ round($value->transaccion_monto,1) }}
					</span>
				</td>
            </tr>
			@endforeach

			@foreach($lista_por_conceptoOthers as $value )
            <tr>
				<td width="6%" align="center"><span class="text">-</span></td>
				<td width="28%" align="left"><span class="text">{{ $value->concepto_titulo }}</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="6%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->transaccion_monto,1) }}</span></td>
				<td width="10%" style="font-size: 8px" align="center"><span class="text">{{$value->comentario }}</span></td>
				<td width="10%" align="center">
					<span class="text">
							{{ round($value->transaccion_monto,1) }}
					</span>
				</td>
            </tr>
			@endforeach

			
			<tr>
				<td  cellspacing="2" width="34%" align="center" style="font-size: 9px" class="fondo"><strong>TOTAL DE EGRESOS DEL MES</strong></td>
				<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_retiro_ahorros_mes_actual,1) }}</strong></td>
				<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_prestamo_de_capital_mes_actual,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_interes_pagado_mes_actual,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_gasto_administrativo_mes_actual,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong> {{ number_format($sum_utilidad_distribuida,1) }} </strong></td>
				<td  cellspacing="2" width="6%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_otros_egresos_mes_actual,1) }}</strong></td>
				<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>-</strong></td>
				<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_egresos_totales_mes_actual,1) }}</strong></td>
			</tr>

			<tr>
				<td  cellspacing="2" width="34%" align="center" style="font-size: 9px" class="fondo"><strong>EGRESOS ACUMULADOS AL MES ANTERIOR</strong></td>
				<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_retiro_ahorros_mes_anterior,1) }}</strong></td>
				<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_prestamo_de_capital_mes_anterior,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_interes_pagado_mes_anterior,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_gasto_administrativo_asta_mes_anterior,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_utilidad_distribuida_mes_anterior,1) }}</strong></td>
				<td  cellspacing="2" width="6%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_otros_egresos_asta_mes_anterior,1)}}</strong></td>
				<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>-</strong></td>
				<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_egresos_totales_mes_anterior,1) }}</strong></td>
			</tr>
			
			<tr>
				<td  cellspacing="2" width="34%" align="center" style="font-size: 9px" class="fondo"><strong>TOTAL DE EGRESOS ACUMULADOS A LA FECHA (*)</strong></td>
				<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_retiro_ahorros_acumulados,1) }}</strong></td>
				<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_prestamo_de_capital_acumulados,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_interes_pagado_acumulados,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_gasto_administrativo_acumulado,1) }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_utilidad_distribuida_acumulado,1) }}</strong></td>
				<td  cellspacing="2" width="6%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_otros_gastos_acumulado,1) }}</strong></td>
				<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>-</strong></td>
				<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_egresos_totales_acumulados,1) }}</strong></td>
			</tr>

    </table>

	<br/>
	<br/>
	
	<table width="100%" class="tabla3">
		<tr>
			<td cellspacing="4" width="25%" align="left" class="fondo"><strong></strong></td>
			<td  width="15%" align="center" class="fondo"><strong>S/.</strong></td>
		</tr>
		<tr>
			<td cellspacing="4" width="25%" align="left" class="fondo"><strong>Saldo del mes anterior</strong></td>
			<td  width="15%" align="center" class="fondo"><strong>{{ number_format($saldo_del_mes_anterior,1) }}</strong></td>
		</tr>
		<tr>
			<td cellspacing="4" width="25%" align="left" class="fondo"><strong>INGRESOS del mes</strong></td>
			<td  width="15%" align="center" class="fondo"><strong>{{ number_format($ingresos_del_mes,1) }}</strong></td>
		</tr>
		<tr>
			<td cellspacing="4" width="25%" align="left" class="fondo"><strong>Total de INGRESOS del mes</strong></td>
			<td  width="15%" align="center" class="fondo"><strong>{{ number_format($total_ingresos_del_mes,1) }}</strong></td>
		</tr>
		<tr>
			<td cellspacing="4" width="25%" align="left" class="fondo"><strong>Egresos del mes</strong></td>
			<td  width="15%" align="center" class="fondo"><strong>{{ number_format($egresos_del_mes,1) }}</strong></td>
		</tr>
		<tr>
			<td cellspacing="4" width="25%" align="left" class="fondo"><strong>Saldo</strong></td>
			<td  width="15%" align="center" class="fondo"><strong>{{ number_format($saldo,1) }}</strong></td>
		</tr>
	</table>
	
	<br/>
	<br/>
	
	<table width="100%" class="tabla3">
		<tr>
			<td cellspacing="6" width="60%" align="left" class="fondo"><strong>TOTAL DE INGRESOS ACUMULADOS A LA FECHA (*) S/.</strong></td>
			<td  width="15%" align="center"class="fondo"><strong>{{ number_format($sum_ingresos_totales_acumulados,1) }}</strong></td>
		</tr>
		<tr>
			<td cellspacing="6" width="60%" align="left" class="fondo"><strong>TOTAL DE EGRESOS ACUMULADOS A LA FECHA (*) S/.</strong></td>
			<td  width="15%" align="center" class="fondo"><strong>{{ number_format($sum_egresos_totales_acumulados,1) }}</strong></td>
		</tr>
    </table>


	<br/>
	<br/>
	<br/>
	<div class="contenedor">
		<table border="0" cellspacing="3" cellpadding="2" style="margin: 50px;" class="table table-striped">
			<tr>
			<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2"> </td>
			<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2"> </td>
			<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">TESORERO------------------------------------</td>
			<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">PRESIDENTE----------------------------------</td>
			</tr>
			<tr>
				<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2"> </td>
				<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2"> </td>
				<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">{{ $tesorero->apellidos.' '.$tesorero->nombres }}</td>
				<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">{{ $presidente->apellidos.' '.$presidente->nombres}}</td>
			</tr>
		</table>
	</div>

</body>
</html>