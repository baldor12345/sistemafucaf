
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
	<div class="contenedor">
            <?php
            $nombremes = array('1'=>'Ene',
            '1'=>'Ene',
            '2'=>'Feb',
            '3'=>'Mar',
            '4'=>'Abr',
            '5'=>'May',
            '6'=>'Jun',
            '7'=>'Jul',
            '8'=>'Ago',
            '9'=>'Sep',
            '10'=>'Oct',
            '11'=>'Nov',
            '12'=>'Dic',);
            ?>

		<table border="0" cellspacing="3" cellpadding="2" style="margin: 50px;" class="table table-striped">
			<tr>
                <td style="font-size: 15px" colspan="2">ESTADO DE CUENTA FUCAF</td>
                <td style="font-size: 15px" colspan="7"></td>
                
            </tr>
            <!-- 2 -->
            <tr>
                <td style="font-size: 15px" colspan="4">{{ $persona->nombres." ".{{ $persona->apellidos }}} }}</td>
                <td style="font-size: 15px" colspan="2">Cod. Cliente FUCAF: </td>
                <td style="font-size: 15px" colspan="2">{{ $persona->codigo }}</td>
                <td style="font-size: 15px" colspan="1"></td>
            </tr>
            <tr>
                <td style="font-size: 15px" colspan="4">{{ $persona->direccion }}</td>
                <td style="font-size: 15px" colspan="2">Último día de pago: </td>
                <td style="font-size: 15px" colspan="2">{{ $cuota->fechapago }}</td>
                <td style="font-size: 15px" colspan="1"></td>
            </tr>
            <tr>
                <td style="font-size: 15px" colspan="4"></td>
                <td style="font-size: 8px" colspan="2">Periodo: </td>
                <td style="font-size: 15px" colspan="2">{{ $periodo }}</td>
                <td style="font-size: 15px" colspan="1"></td>
            </tr>
            <tr>
                <td style="font-size: 15px" colspan="2">LINEA DE CREDITO</td>
                <td style="font-size: 8px" colspan="2">° Incluye capital, intereses, gastos de cuotas atrasadas</td>
                <td style="font-size: 15px" colspan="2">Mes: </td>
                <td style="font-size: 15px" colspan="2">{{ $mescuota }}</td>
                <td style="font-size: 15px" colspan="1"></td>
            </tr>
            <tr>
                <td style="font-size: 15px" colspan="2">TOTAL</td>
                <td style="font-size: 8px" colspan="2">° Línea de crédito para disposición de efectivo</td>
                <td style="font-size: 15px" colspan="5">Periodo: </td>
            </tr>
            <tr>
                <td style="font-size: 15px" colspan="2"></td>
                <td style="font-size: 8px" colspan="2">° No incluye cuotas por vencer</td>
                <td style="font-size: 15px" colspan="5"> </td>
            </tr>
            <tr>
                <td style="font-size: 15px" colspan="2"></td>
                <td style="font-size: 8px" colspan="2">° Se podrá modificar por endeudamiento, comportamiento</td>
                <td style="font-size: 15px" colspan="5"> </td>
            </tr>
            <tr>
                <td style="font-size: 15px" colspan="9"></td>
            </tr>
            <tr>
                <td style="font-size: 15px" colspan="2">DETALLE DE MOVIMIENTOS</td>
                <td style="font-size: 15px" colspan="7"> </td>
            </tr>
            <tr>
                <td style="font-size: 10px" rowspan="2">FECHA DE TRANSACCION</td>
                <td style="font-size: 10px" rowspan="2">FECHA DE PROCESO</td>
                <td style="font-size: 10px" rowspan="2">DESCRIPCION</td>
                <td style="font-size: 10px" rowspan="2">ESTABLECIMIENTO</td>
                <td style="font-size: 10px" rowspan="2">PAIS</td>
                <td style="font-size: 10px" rowspan="2">NRO CUOTA CARGADA</td>
                <td style="font-size: 10px" colspan="2">VALOR CUOTA (S/.)</td>
                <td style="font-size: 10px" rowspan="2">CARGO / ABONO (S/.)</td>
            </tr>
            <tr>
                <td style="font-size: 10px" colspan="1">CAPITAL</td>
                <td style="font-size: 10px" colspan="1">INTERES</td>
            </tr>
            <tr>
                <td style="font-size: 10px" colspan="1">{{ $fechatransaccion }}</td>
                <td style="font-size: 10px" colspan="1">{{ $fecharecibo }}</td>
                <td style="font-size: 10px" colspan="1">PRESTAMO EFECTIVO</td>
                <td style="font-size: 10px" colspan="1">Local FUCAF</td>
                <td style="font-size: 10px" colspan="1">PERU</td>
                <td style="font-size: 10px" colspan="1">{{ $numerocuota }}/{{ $periodo }}</td>
                <td style="font-size: 10px" colspan="1">{{ $parte_capital }}</td>
                <td style="font-size: 10px" colspan="1">{{ $interes }}</td>
                <td style="font-size: 10px" colspan="1">{{ $parte_capital + $interes }}</td>
            </tr>
            <tr>
                <td style="font-size: 10px" colspan="1">{{ $fecharecibo }}</td>
                <td style="font-size: 10px" colspan="1">{{ $fecharecibo }}</td>
                <td style="font-size: 10px" colspan="1">COMISION POR RECIBO DE PAGO</td>
                <td style="font-size: 10px" colspan="1"></td>
                <td style="font-size: 10px" colspan="1">PERU</td>
                <td style="font-size: 10px" colspan="1"></td>
                <td style="font-size: 10px" colspan="1"></td>
                <td style="font-size: 10px" colspan="1"></td>
                <td style="font-size: 10px" colspan="1">0.2</td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td></td><td></td><td>TEA CUOTA 30%</td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td></td><td></td><td>TEA MORATORIO 36%</td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <tr>
                <td style="font-size: 10px" colspan="3">COMO ESTA DISPUESTA SU DEUDA</td>
                <td style="font-size: 15px" colspan="4"></td>
                <td style="font-size: 10px" colspan="2">MENSAJE AL CLIENTE</td>
            </tr>
            <tr>
                <td style="font-size: 10px" colspan="3" rowspan="2">CUOTAS ATRASADAS AL {{ $fechaprogramadapago }}</td>
                <td style="font-size: 10px" colspan="3" rowspan="2">CUOTAS DEL MES </td>
                <td style="font-size: 10px" colspan="1" rowspan="3">TOTAL A PAGAR AL {{ $fechaprogramadapago }}</td>
                <td style="font-size: 10px" colspan="2" rowspan="2">Saldo de Capital</td>
            </tr>
            <tr>
                <td></td><td></td><td></td>
                <td style="font-size: 10px" colspan="1">CUOTAS</td>
                <td style="font-size: 10px" colspan="1">Comisiones</td>
                <td style="font-size: 10px" colspan="1">TOTAL</td>
                <td style="font-size: 10px" colspan="2">{{ $saldorestante }}</td>
            </tr>
            <tr>
                <td></td><td></td><td></td>
                <td style="font-size: 10px" colspan="1">{{ $parte_capital + $interes }}</td>
                <td style="font-size: 10px" colspan="1">0.2</td>
                <td style="font-size: 10px" colspan="1">{{ $parte_capital + $interes + 0.2 }}</td>
                <td style="font-size: 10px" colspan="2">{{  $parte_capital + $interes + 0.2  }}</td>
                <td></td><td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td style="font-size: 10px" colspan="3">CUOTAS DE LOS PROXIMOS 3 MESES</td>
                <td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td style="font-size: 10px" colspan="1">{{ $nombremes[$mes] }}-{{ $anio }}</td>
                <td style="font-size: 10px" colspan="1">{{ $nombremes[$mes+1] }}-{{ $anio1 }}</td>
                <td style="font-size: 10px" colspan="1">{{ $nombremes[$mes+2] }}-{{ $anio2 }}</td>
                <td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td style="font-size: 10px" colspan="1"> {{ $cuota }}</td>
                <td style="font-size: 10px" colspan="1">{{ $cuota1 }}</td>
                <td style="font-size: 10px" colspan="1">{{ $cuota2 }}</td>
                <td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td></td><td></td><td></td><td></td>
            </tr>

           

		</table>
	</div>
</body>
</html>