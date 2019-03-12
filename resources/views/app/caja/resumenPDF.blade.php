
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
                <td style="font-size: 10px" colspan="7" rowspan="" align="center"><strong>RESUMEN</strong></td>
            </tr>   
            <tr>
                <td cellspacing="9" cellpadding="2" colspan="2">{{ '     ' }}FECHA: </td>
                <td style="font-size: 10px" colspan="5" cellpadding="2">{{ $day.'  '.$arraymonth[$month].' del año '.$year }}</td>
            </tr>
            
		</table>

        <table width="100%" border="0px" class="">
            <tr>
                <td></td>
            </tr>
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ><strong>PAGO DE CUOTAS </strong></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <td align="center" style="font-size: 9px" colspan="3" class="linebordercenter"><strong>{{'  '}} NOMBRES</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>MONTO RECIBIDO</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>MONTO DE PAGO</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>VUELTO</strong></td>
                </tr>
            </thead>
            <tbody>
                @foreach($lista_cuotas  as $value)
                    @if($value->monto_recibido == '')
                    @else
                    <tr>
                        <td align="left" style="font-size: 9px" colspan="3" class="linebordercenter">{{ '  '.$value->persona_apellidos.' '.$value->persona_nombres}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_recibido),1)}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_pago),1)}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_recibido),1)-number_format(($value->monto_pago),1)}}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td align="center" style="font-size: 9px" colspan="3" class="linebordercenter"></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_m_r_cuotas,1)}}</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_m_p_cuotas,1)}}</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_v_cuotas,1)}}</strong></td>
                </tr>
            </tfoot>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td></td>
            </tr>
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ><strong>ACCIONES</strong></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <td align="center" style="font-size: 9px" colspan="3" class="linebordercenter"><strong>{{'  '}} NOMBRES</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>MONTO RECIBIDO</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>MONTO DE PAGO</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>VUELTO</strong></td>
                </tr>
            </thead>
            <tbody>
                @foreach($lista_acciones  as $value)
                    @if($value->monto_recibido == '')
                    @else
                    <tr>
                        <td align="left" style="font-size: 9px" colspan="3" class="linebordercenter">{{ '  '.$value->persona_apellidos.' '.$value->persona_nombres}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_recibido),1)}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_pago),1)}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_recibido),1)-number_format(($value->monto_pago),1)}}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td align="center" style="font-size: 9px" colspan="3" class="linebordercenter"></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_m_r_acciones,1)}}</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_m_p_acciones,1)}}</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_v_acciones,1)}}</strong></td>
                </tr>
            </tfoot>
        </table>

        <table width="100%" border="0px" class="">
            <tr>
                <td></td>
            </tr>
            <tr>
                <td align="left" style="font-size: 11px" colspan="9" ><strong>PAGO DE CUOTAS </strong></td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <td align="center" style="font-size: 9px" colspan="3" class="linebordercenter"><strong>{{'  '}} NOMBRES</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>MONTO RECIBIDO</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>MONTO DE PAGO</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>VUELTO</strong></td>
                </tr>
            </thead>
            <tbody>
                @foreach($lista_ahorros  as $value)
                    @if($value->monto_recibido == '')
                    @else
                    <tr>
                        <td align="left" style="font-size: 9px" colspan="3" class="linebordercenter">{{ '  '.$value->persona_apellidos.' '.$value->persona_nombres}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_recibido),1)}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_pago),1)}}</td>
                        <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter">{{ number_format(($value->monto_recibido),1)-number_format(($value->monto_pago),1)}}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td align="center" style="font-size: 9px" colspan="3" class="linebordercenter"></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_m_r_ahorros,1)}}</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_m_p_ahorros,1)}}</strong></td>
                    <td align="center" style="font-size: 9px" colspan="1" class="linebordercenter"><strong>{{ number_format($sum_v_ahorros,1)}}</strong></td>
                </tr>
            </tfoot>
        </table>

    </div>

</body>
</html>