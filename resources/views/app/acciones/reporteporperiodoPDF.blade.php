
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
            font-size: 11px;
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
    </style>
</head>
<body>
	<div class="">
		<table width="100%" border="0px" class="">
            <tr>
                <td align="center" style="font-size: 13px" colspan="7">LA FINANCIERA ÚNICA DE CRÉDITO Y AHORRO FAMILIAR <br> FUCAF</td>
                <td rowspan="7" colspan="2" align="right" ><img src="assets/images/users/fucaf.png" width="140" height="135" /></td>
            </tr>
			<tr>
                <td style="font-size: 11px" colspan="7" rowspan="" align="center"><strong>LISTA DE ACCIONES COMPRADAS Y VENDIDAD POR MES DEL AÑO {{ $anio }}</strong></td>
            </tr>   
			<tr>
                <td style="font-size: 11px" colspan="7" rowspan="" align="center"><strong>--DATOS DEL SOCIO--</strong></td>
            </tr>   
            <tr>
                <td cellspacing="9" cellpadding="2" colspan="2">NOMBRES: </td>
                <td style="font-size: 11px" colspan="5" cellpadding="2">{{ $datos_persona->nombres.' '.$datos_persona->apellidos }}</td>
            </tr>
            <tr>
                <td colspan="2">DIRECCION: </td>
                <td style="font-size: 11px" colspan="5">{{ $datos_persona->direccion }}</td>
            </tr>
            <tr>
                <td colspan="2">CODIGO: </td>
                <td style="font-size: 11px" colspan="5">{{ $datos_persona->codigo }}</td>
            </tr>
            <tr>
                <td colspan="2">TELEFONO: </td>
                <td style="font-size: 11px" colspan="5">{{ $datos_persona->telefono_fijo }}</td>
            </tr>
            
		</table>
        
        
        <table>
            <tr><td></td></tr>
            <tr><td></td></tr>
        </table>
        
        
        <table>
            <tr>
                <td>
                    <table>
                        <thead>
                            <tr>
                                <th align="center" style="font-size: 11px" colspan="4" class="linebordercenter">COMPRADAS</th>
                            </tr>
                            <tr>
                                <th align="center" style="font-size: 11px" colspan="1" class="linebordercenter">#</th>
                                <th align="center" style="font-size: 11px" colspan="2" class="linebordercenter">MES</th>
                                <th align="center" style="font-size: 11px" colspan="1" class="linebordercenter">CANT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $contador = $inicio + 1;
                            ?>
                            @foreach ($listaC as $key => $value)
                            <tr>
                                <td align="center" colspan="1" style="font-size: 11px" class="linebordercenter">{{ $contador }}</td>
                                <td align="center" colspan="2" style="font-size: 11px" class="linebordercenter" >{{ $meses[$value->mes] }}</td>
                                <td align="center" colspan="1" style="font-size: 11px" class="linebordercenter" >{{ $value->cantidad_accion}}</td>
                            </tr>
                            <?php
                            $contador = $contador + 1;
                            ?>
                            @endforeach
                        </tbody>
                    </table> 
                </td>
                <td></td>
                <td>
                    <table>
                        <thead>
                            <tr>
                                <th align="center" style="font-size: 11px" colspan="4" class="linebordercenter">VENDIDAS</th>
                            </tr>
                            <tr>
                                <th align="center" style="font-size: 11px" colspan="1" class="linebordercenter">#</th>
                                <th align="center" style="font-size: 11px" colspan="2" class="linebordercenter">MES</th>
                                <th align="center" style="font-size: 11px" colspan="1" class="linebordercenter">CANT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $contador = $inicio + 1;
                            ?>
                            @foreach ($listaV as $key => $value)
                            <tr>
                                <td align="center" colspan="1" style="font-size: 11px" class="linebordercenter">{{ $contador }}</td>
                                <td align="center" colspan="2" style="font-size: 11px" class="linebordercenter" >{{ $meses[$value->mes] }}</td>
                                <td align="center" colspan="1" style="font-size: 11px" class="linebordercenter" >{{ -$value->cantidad_accion }}</td>
                            </tr>
                            <?php
                            $contador = $contador + 1;
                            ?>
                            @endforeach
                        </tbody>
                    </table> 
                </td>

            </tr>
        </table>
	</div>
</body>
</html>