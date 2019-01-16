
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
        }
    </style>
</head>
<body>
	<div class="contenedor">
		<table width="100%" border="0px" class="">
            <tr>
                <td align="center" style="font-size: 13px" colspan="7">LA FINANCIERA ÚNICA DE CRÉDITO Y AHORRO FAMILIAR <br> FUCAF</td>
                <td rowspan="6" colspan="2" align="right" ><img src="assets/images/users/fucaf.png" width="140" height="125" /></td>
            </tr>
			<tr>
                <td style="font-size: 10px" colspan="7" rowspan="" align="center"><strong>ESTADO DE CUENTA</strong></td>
            </tr>   
            <tr>
                <td cellspacing="9" cellpadding="2" colspan="2">NOMBRES: </td>
                <td style="font-size: 10px" colspan="5" cellpadding="2">{{ $persona_ahorrista->nombres.' '.$persona_ahorrista->apellidos }}</td>
            </tr>
            <tr>
                <td colspan="2">DIRECCION: </td>
                <td style="font-size: 10px" colspan="5">{{ $persona_ahorrista->direccion }}</td>
            </tr>
            <tr>
                <td colspan="2">CODIGO: </td>
                <td style="font-size: 10px" colspan="5">{{ $persona_ahorrista->codigo }}</td>
            </tr>
            <tr>
                <td colspan="2">TELEFONO: </td>
                <td style="font-size: 10px" colspan="5">{{ $persona_ahorrista->telefono_fijo }}</td>
            </tr>
            <tr>
                <td align="center" style="font-size: 11px" colspan="4" class="linebordercenter">AHORROS</td>
                <td colspan="1" ></td>
                <td align="center" style="font-size: 11px" colspan="4" class="linebordercenter">ACCIONES</td>
            </tr>
            <tr>
                <td align="center" colspan="2" style="font-size: 8px" class="linebordercenter" >MONTO AHORRADO</td>
                <td align="center" style="font-size: 8px" class="linebordercenter" >FECHA</td>
                <td align="center" style="font-size: 8px" class="linebordercenter">INTERES</td>
                <td colspan="1" ></td>
                <td align="center" style="font-size: 8px" colspan="2" class="linebordercenter">COMPRADAS</td>
                <td align="center" style="font-size: 8px" colspan="2" class="linebordercenter">{{ $CantAccionesCompradas }}</td>
            </tr>
            <tr>
                <td align="center" colspan="2" style="font-size: 8px" class="linebordercenter">{{$capital_ahorrado}}</td>
                @if( $fecha_ahorro != 0)
                <td align="center" style="font-size: 8px" class="linebordercenter">{{Date::parse($fecha_ahorro )->format('Y/m/d')}}</td>
                @else
                <td align="center" style="font-size: 8px" class="linebordercenter">-</td>
                @endif
                <td align="center" style="font-size: 8px" class="linebordercenter">{{$interes_ahorro}}</td>
                <td colspan="1" ></td>
                <td align="center" style="font-size: 8px" colspan="2" class="linebordercenter">VENDIDAS</td>
                <td align="center" style="font-size: 8px" colspan="2" class="linebordercenter">{{ $CantAccionesVendidas }}</td>
            </tr>
            <tr>
            <td></td>
            </tr>
            <tr>
                <td align="center" style="font-size: 11px" colspan="9" >CREDITOS Y CUOTAS PENDIENTES</td>
            </tr>
            <tr>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter" >MONTO</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter" >PERIODO</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">INTERES</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">FECHA INICIAL</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">FECHA FIN</td>
                <td align="center" colspan="3" style="font-size: 8px" class="linebordercenter">AVAL</td>
                <td align="center" colspan="1" style="font-size: 6px" class="linebordercenter">C. PENDIENTES</td>
                
            </tr>
            @foreach($credito_pendiente as $value)
            <tr>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter" >{{ $value->valor_credito }}</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter" >{{ $value->periodo_credito }}</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">{{ $value->credito_interes }}</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">{{ Date::parse($value->fechai )->format('Y/m/d')}}</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">{{ Date::parse($value->fechaf )->format('Y/m/d') }}</td>
                <td align="center" colspan="3" style="font-size: 8px" class="linebordercenter">{{ $value->persona_aval_id }}</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">{{ $value->pedientes }}</td>
            </tr>
            @endforeach

            <tr><td align="center" style="font-size: 12px" colspan="5" ></td></tr>
            <tr>
                <td align="center" style="font-size: 10px" colspan="9" >ANALISIS DE MORAS DE CREDITOS GENERADAS HASTA LA FECHA </td>
            </tr>
            <tr>
                <td align="center" colspan="1" style="font-size: 8px"></td>
                <td align="center" colspan="1" style="font-size: 8px" ></td>
                <td align="center" colspan="2" style="font-size: 8px" class="linebordercenter" >CANTIDAD DE VECES</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter" >ESTADO</td>
            </tr>
            <tr>
                <td align="center" colspan="1" style="font-size: 8px"></td>
                <td align="center" colspan="1" style="font-size: 8px" ></td>
                @if(count($moras_acumuladas) != 0)
                    <td align="center" colspan="2" style="font-size: 8px" class="linebordercenter" >{{ $moras_acumuladas[0]->cant_mora }}</td>
                    @if($moras_acumuladas[0]->cant_mora <= 5)
                    <td align="center" colspan="1" style="font-size: 8px; background-color: yellow;" class="linebordercenter" ></td>
                    @elseif($moras_acumuladas[0]->cant_mora > 5)
                    <td align="center" colspan="1" style="font-size: 8px; background-color: red;" class="linebordercenter" ></td>
                    @elseif($moras_acumuladas[0]->cant_mora == '')
                    <td align="center" colspan="1" style="font-size: 8px; background-color: green;" class="linebordercenter" ></td>
                    @endif
                @else
                <td align="center" colspan="2" style="font-size: 8px" class="linebordercenter" >0</td>
                <td align="center" colspan="1" style="font-size: 8px; background-color: green;" class="linebordercenter" ></td>
                @endif
            </tr>

		</table>

        
	</div>
</body>
</html>