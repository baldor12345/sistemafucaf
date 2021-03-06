
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
        font-size: 9px;
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
			<td style="text-transform: uppercase;" align="center" style="font-size: 15px" colspan="4">INGRESOS DEL MES DE {{ $mes }} DE {{ $anio }}</td>
			</tr>
		</table>
	</div>

	<table width="100%" class="tabla3">
			<thead>
				<tr>
					<td rowspan="2" cellspacing="1" width="5%" align="center" class="fondo"><strong>Item</strong></td>
					<td rowspan="2" cellspacing="1" width="24%" align="center" class="fondo"><strong>Nombre del Socio o Cliente</strong></td>
					<td rowspan="2" cellspacing="1" width="8%" align="center" class="fondo"><strong>Deposito Ahorros</strong></td>
					<td rowspan="2" cellspacing="1" width="8%" align="center" class="fondo"><strong>Pagos de Capital S/.</strong></td>
					<td rowspan="2" cellspacing="2" width="8%" align="center" class="fondo"><strong>intereses Recibidos</strong></td>
					<td rowspan="2" cellspacing="2" width="8%" align="center" class="fondo"><strong>Acciones S/.</strong></td>
					<td cellspacing="2" width="29%" align="center" class="fondo"><strong>OTROS</strong></td>
					<td rowspan="2" width="10%" align="center" class="fondo"><strong>Total Ingresos</strong></td>
				</tr>
				<tr>
					<td width="8%" align="center" class="fondo"><strong>S/.</strong></td>
					<td width="8%" align="center" class="fondo"><strong>Rec Capital</strong></td>
					<td width="13%" align="center" class="fondo"><strong>Especificar</strong></td>
				</tr>
			</thead>
			<tbody>
				@foreach($lista as $value )
				@if((($value->deposito_ahorros + $value->monto_ahorro) <= 0) and (number_format($value->pagos_de_capital,1) <= 0) and (number_format($value->intereces_recibidos,1) <=0) and (number_format($value->acciones,1)<= 0) and (number_format($value->comision_voucher,1) <= 0))
				@else
				<tr>
					<td width="5%" align="center"><span class="text">{{ $loop->iteration }}</span></td>
					<td width="24%" align="left"><span class="text">{{$value->persona_apellidos.'  '.$value->persona_nombres}}</span></td>
					@if(($value->deposito_ahorros + $value->monto_ahorro) != 0)
					<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format(($value->deposito_ahorros + $value->monto_ahorro),1) }}</span></td>
					@else
					<td width="8%" align="center" style="font-size: 9px"><span class="text">-</span></td>
					@endif
					@if($value->pagos_de_capital != '')
					<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->pagos_de_capital,1) }}</span></td>
					<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->intereces_recibidos,1) }}</span></td>
					@else
					<td width="8%" align="center" style="font-size: 9px"><span class="text">-</span></td>
					<td width="8%" align="center" style="font-size: 9px"><span class="text">-</span></td>
					@endif
					
					<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->acciones,1) }}</span></td>
					@if($value->comision_voucher != '')
					<td width="8%" align="center" style="font-size: 9px"><span class="text">{{ number_format($value->comision_voucher,1) }}</span></td>
					@else
					<td width="8%" align="center" style="font-size: 9px"><span class="text">-</span></td>
					@endif
					@if($value->rec_capital != '')
					<td width="8%" align="center" style="font-size: 9px"><span>{{ number_format($value->rec_capital,1) }}</span></td>
					@else
					<td width="8%" align="center" style="font-size: 9px"><span>-</span></td>
					@endif
					@if($value->comision_voucher != null)
					<td width="13%" align="center" style="font-size: 8px"><span class="text">Com. Rec.</span></td>
					@else
					<td width="13%" align="center" style="font-size: 9px"><span class="text">-</span></td>
					@endif
					<td width="10%" align="center" style="font-size: 9px">
						<span class="text">
						{{ round($value->deposito_ahorros,1) + round($value->intereces_recibidos,1) + round($value->pagos_de_capital,1) + round($value->acciones,1) + round($value->monto_ahorro,1) + round($value->comision_voucher,1) }}
						</span>
					</td>
				</tr>
				@endif
				@endforeach

				@foreach($lista_ingresos_por_concepto as $value )
				<tr>
					<td width="5%" align="center"><span class="text">-</span></td>
					<td width="24%" align="left"><span class="text">{{$value->concepto_titulo}}</span></td>
					<td width="8%" align="center"><span class="text">-</span></td>
					<td width="8%" align="center"><span class="text">-</span></td>
					<td width="8%" align="center"><span class="text">-</span></td>
					<td width="8%" align="center"><span class="text">-</span></td>
					<td width="8%" align="center"><span class="text" style="font-size: 9px">{{ number_format($value->transaccion_monto,1) }}</span></td>
					<td width="8%" align="center"><span class="text">-</span></td>
					<td width="13%" align="center"><span class="text" style="font-size: 8px">{{ $value->transaccion_descrpcion }}</span></td>
					<td width="10%" align="center" style="font-size: 9px">
						<span class="text">
								{{ number_format($value->transaccion_monto,1) }}
						</span>
					</td>
				</tr>
				@endforeach
				
				<tr>
					<td  cellspacing="2" width="29%" align="center" style="font-size: 8px" class="fondo"><strong>TOTAL DE INGRESOS DEL MES</strong></td>
					<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_deposito_ahorros_mes_actual,1) }}</strong></td>
					<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_pagos_de_capital_mes_actual,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_interese_recibidos_mes_actual,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_acciones_mes_actual,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_otros_mes_actual,1) }}</strong></td>
					<td cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_rec_capital_mes_actual,1) }}</strong></td>
					<td  cellspacing="2" width="13%" align="center" style="font-size: 9px" class="fondo"><strong>-</strong></td>
					<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_ingresos_totales_mes_actual,1) }}</strong></td>
				</tr>

				<tr>
					<td  cellspacing="2" width="29%" align="center" style="font-size: 8px" class="fondo"><strong>INGRESOS ACUMULADOS AL MES ANTERIOR</strong></td>
					<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_deposito_ahorros_asta_mes_anterior,1) }}</strong></td>
					<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_pagos_de_capital_asta_mes_anterior,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_interese_recibidos_asta_mes_anterior,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_acciones_asta_mes_anterior,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_otros_asta_mes_anterior,1) }}</strong></td>
					<td cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_rec_capital_asta_mes_anterior,1) }}</strong></td>
					<td  cellspacing="2" width="13%" align="center" style="font-size: 9px" class="fondo"><strong>-</strong></td>
					<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_ingresos_totales_asta_mes_anterior,1) }}</strong></td>
				</tr>
				
				<tr>
					<td  cellspacing="2" width="29%" align="center" style="font-size: 8px" class="fondo"><strong>TOTAL DE INGRESOS ACUMULADOS A LA FECHA(*)</strong></td>
					<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_deposito_ahorros_acumulados,1) }}</strong></td>
					<td  cellspacing="1" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_pagos_de_capital_acumulados,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_interese_recibidos_acumulados,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_acciones_acumulados,1) }}</strong></td>
					<td  cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_otros_acumulados,1) }}</strong></td>
					<td cellspacing="2" width="8%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_rec_capital_acumulados,1) }}</strong></td>
					<td  cellspacing="2" width="13%" align="center" style="font-size: 9px" class="fondo"><strong>-</strong></td>
					<td  cellspacing="2" width="10%" align="center" style="font-size: 9px" class="fondo"><strong>{{ number_format($sum_ingresos_totales_acumulados,1) }}</strong></td>
				</tr>
			</tbody>
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
				<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">{{ $tesorero->apellidos.' '.$tesorero->nombres}}</td>
				<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">{{$presidente->apellidos.' '.$presidente->nombres}}</td>
			</tr>
		</table>
	</div>

</body>
</html>