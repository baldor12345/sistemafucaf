
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
                <td rowspan="2" cellspacing="1" width="6%" align="center" class="fondo"><strong>Dia</strong></td>
				<td rowspan="2" cellspacing="1" width="28%" align="center" class="fondo"><strong>Concepto y/o Nombre del Socio o Cliente</strong></td>
                <td rowspan="2" cellspacing="1" width="8%" align="center" class="fondo"><strong>Retiros Ahorros S/.</strong></td>
                <td rowspan="2" cellspacing="1" width="8%" align="center" class="fondo"><strong>Préstamos S/.</strong></td>
                <td rowspan="2" cellspacing="2" width="8%" align="center" class="fondo"><strong>Interes Pagado S/.</strong></td>
				<td rowspan="2" cellspacing="2" width="8%" align="center" class="fondo"><strong>Gastos Admin. S/.</strong></td>
				<td rowspan="2" cellspacing="2" width="8%" align="center" class="fondo"><strong>Utilidad Distrib. S/.</strong></td>
				<td cellspacing="2" width="24%" align="center" class="fondo"><strong>OTROS</strong></td>
				<td rowspan="2" width="10%" align="center" class="fondo"><strong>Total Egresos</strong></td>
			</tr>
			<tr>
				<td width="8%" align="center" class="fondo"><strong>S/.</strong></td>
				<td width="16%" align="center" class="fondo"><strong>Especificar</strong></td>
			</tr>
            @foreach($lista as $value )
            <tr>
				<td width="6%" align="center"><span class="text">{{$day.'-'.$mesItm}}</span></td>
				<td width="28%" align="center"><span class="text">{{$value->persona_nombres.' '.$value->persona_apellidos}}</span></td>
				<td width="8%" align="center"><span class="text">{{ $value->deposito_ahorros }}</span></td>
				<td width="8%" align="center"><span class="text">{{ $value->pagos_de_capital }}</span></td>
				<td width="8%" align="center"><span class="text">{{ $value->intereces_recibidos }}</span></td>
				<td width="8%" align="center"><span class="text">{{ $value->acciones }}</span></td>
				<td width="8%" align="center"><span class="text">-</span></td>
				<td width="16%" align="center"><span class="text">Com. Rec.</span></td>
				<td width="10%" align="center">
					<span class="text">
					{{ ($value->deposito_ahorros+$value->intereces_recibidos+$value->pagos_de_capital+$value->acciones) }}
					</span>
				</td>
            </tr>
			@endforeach
			
			<tr>
				<td  cellspacing="2" width="34%" align="center" class="fondo"><strong>TOTAL DE INGRESOS DEL MES</strong></td>
				<td  cellspacing="1" width="8%" align="center" class="fondo"><strong>{{ $sum_deposito_ahorros_mes_actual }}</strong></td>
				<td  cellspacing="1" width="8%" align="center" class="fondo"><strong>{{ $sum_pagos_de_capital_mes_actual }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_interese_recibidos_mes_actual }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_acciones_mes_actual }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_otros_mes_actual }}</strong></td>
				<td  cellspacing="2" width="16%" align="center" class="fondo"><strong>-</strong></td>
				<td  cellspacing="2" width="10%" align="center" class="fondo"><strong>{{ $sum_ingresos_totales_mes_actual }}</strong></td>
			</tr>

			<tr>
				<td  cellspacing="2" width="34%" align="center" class="fondo"><strong>INGRESOS ACUMULADOS AL MES ANTERIOR</strong></td>
				<td  cellspacing="1" width="8%" align="center" class="fondo"><strong>{{ $sum_deposito_ahorros_asta_mes_anterior }}</strong></td>
				<td  cellspacing="1" width="8%" align="center" class="fondo"><strong>{{ $sum_pagos_de_capital_asta_mes_anterior }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_interese_recibidos_asta_mes_anterior }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_acciones_asta_mes_anterior }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_otros_asta_mes_anterior }}</strong></td>
				<td  cellspacing="2" width="16%" align="center" class="fondo"><strong>-</strong></td>
				<td  cellspacing="2" width="10%" align="center" class="fondo"><strong>{{ $sum_ingresos_totales_asta_mes_anterior }}</strong></td>
			</tr>
			
			<tr>
				<td  cellspacing="2" width="34%" align="center" class="fondo"><strong>TOTAL DE INGRESOS ACUMULADOS A LA FECHA (*)</strong></td>
				<td  cellspacing="1" width="8%" align="center" class="fondo"><strong>{{ $sum_deposito_ahorros_acumulados }}</strong></td>
				<td  cellspacing="1" width="8%" align="center" class="fondo"><strong>{{ $sum_pagos_de_capital_acumulados }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_interese_recibidos_acumulados }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_acciones_acumulados }}</strong></td>
				<td  cellspacing="2" width="8%" align="center" class="fondo"><strong>{{ $sum_otros_acumulados }}</strong></td>
				<td  cellspacing="2" width="16%" align="center" class="fondo"><strong>-</strong></td>
				<td  cellspacing="2" width="10%" align="center" class="fondo"><strong>{{ $sum_ingresos_totales_acumulados }}</strong></td>
			</tr>

    </table>

	

</body>
</html>