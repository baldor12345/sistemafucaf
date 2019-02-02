
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
        .alinear{
            display: inline-block;
            width: 100px;
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
        }

        .contenedor2 {
        float: left;
        width: 804px;
        }

        .tabla1, .tabla2 {
        float: left;
        width: 400px;
        }

        .tabla1 table, .tabla2 table {
        text-align: center;
        }

        .tabla1 table tr td, .tabla2 table tr td {
        width: 100px
        }
        
    </style>
</head>
<body>
	<div >
		<table width="100%" border="0px" class="">
            <tr>
                <td align="center" style="font-size: 13px" colspan="7">LA FINANCIERA ÚNICA DE CRÉDITO Y AHORRO FAMILIAR <br> FUCAF</td>
                <td rowspan="3" colspan="2" align="right" ><img src="assets/images/users/fucaf.png" width="140" height="100" /></td>
            </tr>
			<tr>
                <td style="font-size: 10px" colspan="7" rowspan="" align="center"><strong>RESUMEN FINANCIERO </strong></td>
            </tr>   
            <tr>
                <td cellspacing="9" cellpadding="2" colspan="2">{{ '     ' }}MES: </td>
                <td style="font-size: 10px" colspan="5" cellpadding="2">{{ $arraymonth[intval($month)].' del año '.$anio }}</td>
            </tr>
            
		</table>

        <table width="100%" border="0px" class="">
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ><strong>INGRESOS </strong></td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td align="left" style="font-size: 11px" colspan="4" >{{ ' '}}A. CUOTAS PRÉSTAMOS</td>
                        </tr>
                        @foreach($listacoutas  as $value)
                            @if($value->pagos_de_capital == '' and $value->intereces_recibidos == '' and $value->cuota_mora == '' and $value->comision_voucher == '')
                            @else
                            <tr>
                                <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">{{ '  '.$value->persona_apellidos.' '.$value->persona_nombres}}</td>
                                <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format(($value->pagos_de_capital+$value->intereces_recibidos+$value->cuota_mora+$value->comision_voucher),1)}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>CUOTA TOTAL PRESTAMOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_cuotas,1)}}</strong></td>
                        </tr>
                    </table>
                </td>
                
                <td>
                    <table>
                        <tr>
                            <td align="left" style="font-size: 11px" colspan="4" >{{ ' '}}B. ACCIONES</td>
                        </tr>
                        @foreach($listacciones  as $value)
                            @if($value->acciones == '')
                            @else
                            <tr>
                                <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">{{ '  '.$value->persona_apellidos.' '.$value->persona_nombres}}</td>
                                <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($value->acciones,1)}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL ACCIONES</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_acciones,1)}}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td align="center" style="font-size: 11px" colspan="9" ></td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td align="left" style="font-size: 11px" colspan="4" >{{ ' '}}C. AHORROS</td>
                        </tr>
                        @foreach($listahorros  as $value)
                            @if($value->deposito_ahorros == '')
                            @else
                            <tr>
                                <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">{{ '  '.$value->persona_apellidos.' '.$value->persona_nombres}}</td>
                                <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($value->deposito_ahorros,1)}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL AHORROS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_ahorros,1)}}</strong></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td align="left" style="font-size: 11px" colspan="4" >{{ ' '}}D. OTROS INGRESOS</td>
                        </tr>
                        @foreach($listaotrosingresos  as $value)
                            @if($value->transaccion_monto == '')
                            @else
                            <tr>
                                <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">{{ '  '.$value->concepto_titulo.' - '.$value->transaccion_descrpcion}}</td>
                                <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($value->transaccion_monto,1)}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL OTROS INGRESOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_otrosingresos,1)}}</strong></td>
                        </tr>
                    </table>
                    
                </td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ></td>
            </tr>
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ><strong>EGRESOS</strong></td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td align="left" style="font-size: 11px" colspan="4" >{{ ' '}}A. PRÉSTAMOS</td>
                        </tr>
                        @foreach($listprestamos  as $value)
                            @if($value->monto_credito == '')
                            @else
                            <tr>
                                <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">{{ '  '.$value->persona_apellidos.' '.$value->persona_nombres}}</td>
                                <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($value->monto_credito,1)}}</td>
                            </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL PRESTAMOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_prestamos,1)}}</strong></td>
                        </tr>
                    </table>
                </td>
                
                <td>
                    <table>
                        <tr>
                            <td align="left" style="font-size: 11px" colspan="4" >{{ ' '}}B. OTROS EGRESOS</td>
                        </tr>
                        @foreach($listaotrosegresosadmin  as $value)
                            @if($value->transaccion_monto == '')
                            @else
                            <tr>
                                <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">{{ '  '.$value->concepto_titulo.' - '.$value->comentario}}</td>
                                <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($value->transaccion_monto,1)}}</td>
                            </tr>
                            @endif
                        @endforeach
                        @foreach($listaotrosegresosotros  as $value)
                            @if($value->transaccion_monto == '')
                            @else
                            <tr>
                                <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">{{ '  '.$value->concepto_titulo.' - '.$value->comentario}}</td>
                                <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($value->transaccion_monto,1)}}</td>
                            </tr>
                            @endif
                        @endforeach

                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL OTROS EGRESOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format(($sum_otrosingresosadmin+$sum_otrosingresootros),1)}}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ></td>
            </tr>
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ><strong>RESUMEN</strong></td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>INGRESOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>TOTAL</strong></td>
                        </tr>
                        <tr>
                            <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">Cuotas Préstamos</td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($sum_cuotas,1) }}</td>
                        </tr>
                        <tr>
                            <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">Compra de Acciones</td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($sum_acciones,1) }}</td>
                        </tr>
                        <tr>
                            <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">Ahorros</td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($sum_ahorros,1) }}</td>
                        </tr>
                        <tr>
                            <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">Otros Ingresos</td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($sum_otrosingresos,1) }}</td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL INGRESOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_total_ingresos,1)}}</strong></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table>
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>EGRESOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>TOTAL</strong></td>
                        </tr>
                        <tr>
                            <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">Otros Egresos</td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format(($sum_otrosingresosadmin+$sum_otrosingresootros),1) }}</td>
                        </tr>
                        <tr>
                            <td align="left" style="font-size: 10px" colspan="3" class="linebordercenter">Préstamos</td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter">{{ number_format($sum_prestamos,1) }}</td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL EGRESOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_total_egresos,1)}}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ></td>
            </tr>
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ></td>
            </tr>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td>
                    <table>
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL INGRESOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_total_ingresos,1)}}</strong></td>
                        </tr>
                        <tr>
                            <td align="center" style="font-size: 10px" colspan="3" class="linebordercenter"><strong>TOTAL EGRESOS</strong></td>
                            <td align="center" style="font-size: 10px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_total_egresos,1)}}</strong></td>
                        </tr>
                    </table>
                </td>
                
                <td>
                    
                </td>
            </tr>
        </table>

	</div>

</body>
</html>