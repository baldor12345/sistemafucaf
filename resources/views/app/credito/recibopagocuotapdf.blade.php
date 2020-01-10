
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
        .contenedor{
            border-collapse: collapse;
            border:1px solid #808080;
        }
        td{
            font-size: 10px;
           
        }
        
        .lineborderleft{
            border: 0.9px solid #3f3f3f;
            text-align : left;;
        }
        .linebordercenter{
            border: 0.9px solid #3f3f3f;
            text-align : center;
        }
        .line_h_b{
            border-bottom: 0.9px solid #3f3f3f;
        }
        .line_h_b2{
            border-bottom: 0.2px solid #afafaf;
        }
        .line_h_t{
            border-top: 0.9px solid #3f3f3f;
        }
        .line_v_id{
            border-left: 0.9px solid #3f3f3f;
            border-right: 0.9px solid #3f3f3f;
            text-align: center;
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

		<table width="100%" class="">
			<tr>
                <td style="font-size: 8px" colspan="2"><strong>RECIBO PAGO DE CUOTA FUCAF</strong></td>
                <td style="font-size: 8px" colspan="7"></td>
                
            </tr>
            
            <tr>
                <td style="font-size: 8px" colspan="5">{{ $persona->nombres.' '.$persona->apellidos }}</td>
                <td style="font-size: 8px" colspan="2" class="lineborderleft"><strong>Cod. Cliente FUCAF: </strong></td>
                <td style="font-size: 8px" colspan="2" class="lineborderleft">{{ $persona->codigo }}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="5">{{ $persona->direccion }}</td>
                <td style="font-size: 8px" colspan="2" class="lineborderleft"><strong>Último día de pago: </strong></td>
                <td style="font-size: 8px" colspan="2" class="lineborderleft">{{ date('d/m/Y',strtotime($cuota->fecha_programada_pago))}}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="5"></td>
                <td style="font-size: 8px" colspan="2" class="lineborderleft"><strong>Periodo: </strong></td>
                <td style="font-size: 8px" colspan="2" class="lineborderleft">{{ $periodocredito }}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="2">LINEA DE CREDITO</td>
                <td style="font-size: 8px" colspan="3">° Incluye capital, intereses, gastos de cuotas atrasadas</td>
                <td style="font-size: 8px" colspan="2" class="lineborderleft">Mes: </td>
                <td style="font-size: 8px" colspan="2" class="lineborderleft">{{ $nombremes[date('m', strtotime($cuota->fecha_programada_pago))]."-".date('Y', strtotime($cuota->fecha_programada_pago)) }}</td>
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
                <td style="font-size: 8px" rowspan="2" class="linebordercenter"><strong>FECHA DE TRANSACCION</strong></td>
                <td style="font-size: 8px" rowspan="2" class="linebordercenter"><strong>FECHA DE PROCESO</strong></td>
                <td style="font-size: 8px" rowspan="2" class="linebordercenter"><strong>DESCRIPCION</strong></td>
                <td style="font-size: 8px" rowspan="2" class="linebordercenter"><strong>ESTABLECIMIENTO</strong></td>
                <td style="font-size: 8px" rowspan="2" class="linebordercenter"><strong>PAIS</strong></td>
                <td style="font-size: 8px" rowspan="2" class="linebordercenter"><strong>NRO CUOTA CARGADA</strong></td>
                <td style="font-size: 8px" colspan="2" class="linebordercenter"><strong>VALOR CUOTA (S/.)</strong></td>
                <td style="font-size: 8px" rowspan="2" class="linebordercenter"><strong>CARGO / ABONO (S/.)</strong></td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="1" class="linebordercenter"><strong>CAPITAL</strong></td>
                <td style="font-size: 8px" colspan="1" class="linebordercenter"><strong>INTERES</strong></td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ date('d/m/Y',strtotime( $transaccion->fecha)) }}</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ date('d/m/Y',strtotime( $transaccion->fecha)) }}</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">PRESTAMO EFECTIVO</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">Local FUCAF</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">PERU</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ $cuota->numero_cuota }}/{{ $periodocredito }}</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ round($transaccion->cuota_parte_capital, 1) }}</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ round($transaccion->cuota_interes, 1) }}</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ round($transaccion->cuota_parte_capital + $transaccion->cuota_interes, 1) }}</td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ $transaccion->fecha}}</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ $transaccion->fecha }}</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">COMISION POR RECIBO DE PAGO</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2"></td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">PERU</td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2"></td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2"></td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2"></td>
                <td style="font-size: 8px" colspan="1" class="line_v_id line_h_b2">{{ $configuraciones->valor_recibo }}</td>
            </tr>
            <tr>
                <td class="line_v_id line_h_b2"></td>
                <td class="line_v_id line_h_b2"></td>
                <td class="line_v_id line_h_b2" style="font-size: 8px">TEA CUOTA 30%</td>
                <td class="line_v_id line_h_b2"></td>
                <td class="line_v_id line_h_b2"></td>
                <td class="line_v_id line_h_b2"></td>
                <td class="line_v_id line_h_b2"></td>
                <td class="line_v_id line_h_b2"></td>
                <td class="line_v_id line_h_b2"></td>
            </tr>
            <tr>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b" style="font-size: 8px">TEA MORATORIO 36%</td>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b" style="font-size: 8px">{{ round($transaccion->cuota_mora, 1) }}</td>
            </tr>
            <tr><td colspan="9"></td></tr>
            <tr>
                <td style="font-size: 8px" colspan="3"><strong>COMO ESTA DISPUESTA SU DEUDA</strong></td>
                <td style="font-size: 8px" colspan="4"></td>
                <td style="font-size: 8px" colspan="2"><strong>MENSAJE AL CLIENTE</strong></td>
            </tr>
            <tr>
                <td style="font-size: 8px" colspan="3" rowspan="1" class="linebordercenter"><strong>CUOTAS ATRASADAS AL {{ date('d/m/Y',strtotime($cuota->fecha_programada_pago))  }}</strong></td>
                <td style="font-size: 8px" colspan="3" rowspan="1" class="linebordercenter"><strong>CUOTAS DEL MES </strong></td>
                <td style="font-size: 8px" colspan="1" rowspan="2" class="linebordercenter"><strong>TOTAL A PAGAR AL {{ date('d/m/Y',strtotime($cuota->fecha_programada_pago))  }}</strong></td>
                <td style="font-size: 8px" colspan="2" rowspan="1" class="linebordercenter"><strong>Saldo de Capital</strong></td>
            </tr>
            
            <tr>
                <td class="line_v_id"></td>
                <td class="line_v_id"></td>
                <td class="line_v_id"></td>
                <td style="font-size: 8px" colspan="1" class="linebordercenter"><strong>CUOTAS</strong></td>
                <td style="font-size: 8px" colspan="1" class="linebordercenter"><strong>Comisiones</strong></td>
                <td style="font-size: 8px" colspan="1" class="linebordercenter"><strong>TOTAL</strong></td>
                <td style="font-size: 8px" colspan="2" class="linebordercenter">{{ round($cuota->saldo_restante, 1) }}</td>
            </tr>
            <tr>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b"></td>
                <td class="line_v_id line_h_b" style="font-size: 8px" colspan="1" class="linebordercenter">{{ round($transaccion->cuota_parte_capital + $transaccion->cuota_interes, 1) }}</td>
                <td class="line_v_id line_h_b" style="font-size: 8px" colspan="1" class="linebordercenter">{{ $configuraciones->valor_recibo }}</td>
                <td class="line_v_id line_h_b" style="font-size: 12px" colspan="1" class="linebordercenter"><strong>{{ round($transaccion->cuota_parte_capital + $transaccion->cuota_interes+ $transaccion->cuota_mora + $configuraciones->valor_recibo,1) }}</strong></td>
                <td class="line_v_id line_h_b" style="font-size: 12px" colspan="1" class="linebordercenter"><strong>{{ round($transaccion->cuota_parte_capital + $transaccion->cuota_interes+ $transaccion->cuota_mora + $configuraciones->valor_recibo, 1)   }}</strong></td>
                <td class=""></td>
                <td class=""></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="font-size: 8px; text-align: center" colspan="3"><strong>CUOTAS DE LOS PROXIMOS 3 MESES</strong></td>
                <td></td>
            </tr>
            <?php
                $numero_mes = explode('-',date('Y-m-d', strtotime($cuota->fecha_programada_pago)))[1];
                $anio_cuota = explode('-',date('Y-m-d', strtotime($cuota->fecha_programada_pago)))[0];
            ?>
            <tr>
                <?php $numero_mes = ($numero_mes == 12)? 1: $numero_mes +1 ;
                    $anio_cuota = ($numero_mes == 12)? $anio_cuota + 1: $anio_cuota;
                ?>
                <td></td><td></td><td></td><td></td><td></td>
                <td style="font-size: 8px" colspan="1" class="linebordercenter">{{ $nombremes[$numero_mes] }}-{{ $anio_cuota }}</td>
                <?php $numero_mes = ($numero_mes == 12)? 1: $numero_mes +1 ;
                    $anio_cuota = ($numero_mes == 12)? $anio_cuota + 1: $anio_cuota;
                ?>
                <td style="font-size: 8px" colspan="1" class="linebordercenter">{{ $nombremes[$numero_mes] }}-{{ $anio_cuota }}</td>
                <?php $numero_mes = ($numero_mes == 12)? 1: $numero_mes +1 ;
                    $anio_cuota = ($numero_mes == 12)? $anio_cuota + 1: $anio_cuota;
                ?>
                <td style="font-size: 8px" colspan="1" class="linebordercenter">{{  $nombremes[$numero_mes] }}-{{ $anio_cuota }}</td>
                <td></td>
            </tr>
            <tr>
                <td></td><td></td><td></td><td></td><td></td>
                <td style="font-size: 8px" colspan="1" class="linebordercenter"> {{ ($cuota_s==null)?"0.00":  $cuota_s->parte_capital + $cuota_s->interes + $cuota_s->interes_mora }}</td>
                <td style="font-size: 8px" colspan="1" class="linebordercenter">{{ ($cuota_s1==null)?"0.00": $cuota_s1->parte_capital + $cuota_s1->interes + $cuota_s1->interes_mora }}</td>
                <td style="font-size: 8px" colspan="1" class="linebordercenter">{{ ($cuota_s2==null)?"0.00": $cuota_s2->parte_capital + $cuota_s2->interes + $cuota_s2->interes_mora }}</td>
                <td></td>
            </tr>
		</table>
	</div>
</body>
</html>