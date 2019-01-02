
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
        .table td{
            border: 0.9px solid #000;
            text-align : left;
        }
</style>

</head>
<body>
	<div class="contenedor">
            <?php
            $nombremes = array('1'=>'Ene',
            '1'=>'Ene',
            '01'=>'Ene',
            '2'=>'Feb',
            '02'=>'Feb',
            '3'=>'Mar',
            '03'=>'Mar',
            '4'=>'Abr',
            '04'=>'Abr',
            '5'=>'May',
            '05'=>'May',
            '6'=>'Jun',
            '06'=>'Jun',
            '7'=>'Jul',
            '07'=>'Jul',
            '8'=>'Ago',
            '08'=>'Ago',
            '9'=>'Sep',
            '09'=>'Sep',
            '10'=>'Oct',
            '11'=>'Nov',
            '12'=>'Dic',);
            ?>

		<table border="0" cellspacing="3" cellpadding="2" style="margin: 5px;" class="table table-striped">
			<tr>
                <td style="font-size: 8px" colspan="2">ESTADO DE CUENTA FUCAF</td>
                <td style="font-size: 8px" colspan="7"></td>
                
            </tr>
            <!-- 2 -->
            <tr>
                <td style="font-size: 8px" colspan="5">{{ $persona->nombres.' '.$persona->apellidos }}</td>
                <td style="font-size: 8px" colspan="2">Cod. Cliente FUCAF: </td>
                <td style="font-size: 8px" colspan="2">{{ $persona->codigo }}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="5">{{ $persona->direccion }}</td>
                <td style="font-size: 8px" colspan="2">Último día de pago: </td>
                <td style="font-size: 8px" colspan="2">{{ date('d/m/Y',strtotime($cuota->fecha_programada_pago))}}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="5"></td>
                <td style="font-size: 8px" colspan="2">Periodo: </td>
                <td style="font-size: 8px" colspan="2">{{ $periodocredito }}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="2">LINEA DE CREDITO</td>
                <td style="font-size: 8px" colspan="3">° Incluye capital, intereses, gastos de cuotas atrasadas</td>
                <td style="font-size: 8px" colspan="2">Mes: </td>
                <td style="font-size: 8px" colspan="2">{{ $nombremes[explode('-',date('Y-m-d', strtotime($cuota_s->fecha_programada_pago)))[1]] }}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="2">TOTAL</td>
                <td style="font-size: 8px" colspan="3">° Línea de crédito para disposición de efectivo</td>
                <td style="font-size: 8px" colspan="4"></td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="2"></td>
                <td style="font-size: 8px" colspan="3">° No incluye cuotas por vencer</td>
                <td style="font-size: 8px" colspan="4"> </td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="2"></td>
                <td style="font-size: 8px" colspan="3">° Se podrá modificar por endeudamiento, comportamiento</td>
                <td style="font-size: 8px" colspan="4"> </td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="9"></td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="2">DETALLE DE MOVIMIENTOS</td>
                <td style="font-size: 8px" colspan="7"> </td>
            </tr>
            <tr>
                <td style="font-size: 8px" rowspan="2">FECHA DE TRANSACCION</td>
                <td style="font-size: 8px" rowspan="2">FECHA DE PROCESO</td>
                <td style="font-size: 8px" rowspan="2">DESCRIPCION</td>
                <td style="font-size: 8px" rowspan="2">ESTABLECIMIENTO</td>
                <td style="font-size: 8px" rowspan="2">PAIS</td>
                <td style="font-size: 8px" rowspan="2">NRO CUOTA CARGADA</td>
                <td style="font-size: 8px" colspan="2">VALOR CUOTA (S/.)</td>
                <td style="font-size: 8px" rowspan="2">CARGO / ABONO (S/.)</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="1">CAPITAL</td>
                <td style="font-size: 8px" colspan="1">INTERES</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="1">{{ date('d/m/Y',strtotime( $cuota->fecha_pago)) }}</td>
                <td style="font-size: 8px" colspan="1">{{ date('d/m/Y',strtotime( $cuota->fecha_pago)) }}</td>
                <td style="font-size: 8px" colspan="1">PRESTAMO EFECTIVO</td>
                <td style="font-size: 8px" colspan="1">Local FUCAF</td>
                <td style="font-size: 8px" colspan="1">PERU</td>
                <td style="font-size: 8px" colspan="1">{{ $cuota->numero_cuota }}/{{ $periodocredito }}</td>
                <td style="font-size: 8px" colspan="1">{{ $cuota->parte_capital }}</td>
                <td style="font-size: 8px" colspan="1">{{ $cuota->interes }}</td>
                <td style="font-size: 8px" colspan="1">{{ $cuota->parte_capital + $cuota->interes }}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="1">{{ $cuota->fecha_pago}}</td>
                <td style="font-size: 8px" colspan="1">{{ $cuota->fecha_pago }}</td>
                <td style="font-size: 8px" colspan="1">COMISION POR RECIBO DE PAGO</td>
                <td style="font-size: 8px" colspan="1"></td>
                <td style="font-size: 8px" colspan="1">PERU</td>
                <td style="font-size: 8px" colspan="1"></td>
                <td style="font-size: 8px" colspan="1"></td>
                <td style="font-size: 8px" colspan="1"></td>
                <td style="font-size: 8px" colspan="1">0.2</td>
            </tr>
            <tr>
                <td></td><td></td><td style="font-size: 8px">TEA CUOTA 30%</td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td></td><td></td><td style="font-size: 8px">TEA MORATORIO 36%</td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="3">COMO ESTA DISPUESTA SU DEUDA</td>
                <td style="font-size: 8px" colspan="4"></td>
                <td style="font-size: 8px" colspan="2">MENSAJE AL CLIENTE</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="3" rowspan="1">CUOTAS ATRASADAS AL {{ date('d/m/Y',strtotime($cuota->fecha_programada_pago))  }}</td>
                <td style="font-size: 8px" colspan="3" rowspan="1">CUOTAS DEL MES </td>
                <td style="font-size: 8px" colspan="1" rowspan="2">TOTAL A PAGAR AL {{ date('d/m/Y',strtotime($cuota->fecha_programada_pago))  }}</td>
                <td style="font-size: 8px" colspan="2" rowspan="1">Saldo de Capital</td>
            </tr>
            
            <tr>
                <td></td><td></td><td></td>
                <td style="font-size: 8px" colspan="1">CUOTAS</td>
                <td style="font-size: 8px" colspan="1">Comisiones</td>
                <td style="font-size: 8px" colspan="1">TOTAL</td>
                <td style="font-size: 8px" colspan="2">{{ $cuota->saldo_restante }}</td>
            </tr>
            <tr>
                <td></td><td></td><td></td>
                <td style="font-size: 8px" colspan="1">{{ $cuota->parte_capital + $cuota->interes }}</td>
                <td style="font-size: 8px" colspan="1">0.2</td>
                <td style="font-size: 8px" colspan="1">{{ $cuota->parte_capital + $cuota->interes+ $cuota->interes_mora + 0.2 }}</td>
                <td style="font-size: 8px" colspan="2">{{ $cuota->parte_capital + $cuota->interes+ $cuota->interes_mora + 0.2   }}</td>
                <td></td><td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td style="font-size: 8px" colspan="3">CUOTAS DE LOS PROXIMOS 3 MESES</td>
                <td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td style="font-size: 8px" colspan="1">{{ $nombremes[explode('-',date('Y-m-d', strtotime($cuota_s->fecha_programada_pago)))[1]] }}-{{ explode('-',date('Y-m-d', strtotime($cuota_s->fecha_programada_pago)))[0] }}</td>
                <td style="font-size: 8px" colspan="1">{{ $nombremes[explode('-',date('Y-m-d', strtotime($cuota_s1->fecha_programada_pago)))[1]] }}-{{ explode('-',date('Y-m-d', strtotime($cuota_s1->fecha_programada_pago)))[0] }}</td>
                <td style="font-size: 8px" colspan="1">{{ $nombremes[explode('-',date('Y-m-d', strtotime($cuota_s2->fecha_programada_pago)))[1]] }}-{{ explode('-',date('Y-m-d', strtotime($cuota_s2->fecha_programada_pago)))[0] }}</td>
                <td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td style="font-size: 8px" colspan="1"> {{ $cuota_s->parte_capital + $cuota_s->interes + $cuota_s->interes_mora }}</td>
                <td style="font-size: 8px" colspan="1">{{ $cuota_s1->parte_capital + $cuota_s1->interes + $cuota_s1->interes_mora }}</td>
                <td style="font-size: 8px" colspan="1">{{ $cuota_s2->parte_capital + $cuota_s2->interes + $cuota_s2->interes_mora }}</td>
                <td></td>
            </tr>
		</table>
	</div>
</body>
</html>