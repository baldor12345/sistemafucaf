
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
	<div >
		<table width="100%" border="0px" class="">
            <tr>
                <td align="center" style="font-size: 12px" colspan="5" >CONTROL DE ASISTENCIA TARDANZAS HASTA {{ $fecha }}</td>
            </tr>
            <tr><td align="center" style="font-size: 12px" colspan="5" ></td></tr>
            <tr>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter" >CODIGO</td>
                <td align="center" colspan="2" style="font-size: 10px" class="linebordercenter" >NOMBRES</td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter">TOTAL TARDANZAS</td>
                
            </tr>
            @foreach($listaT as $value)
            <tr>
                <td align="left" colspan="1" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->persona_codigo }}</td>
                <td align="left" colspan="2" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->persona_nombres.' '.$value->persona_apellidos }}</td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->control_tardanzas }}</td>
            </tr>
            @endforeach
            <tr>
                <td align="center" style="font-size: 12px" colspan="5" ></td>
            </tr>

            <tr>
                <td align="center" style="font-size: 12px" colspan="5" >CONTROL DE ASISTENCIA FALTAS HASTA {{ $fecha }}</td>
            </tr>
            <tr>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter" >CODIGO</td>
                <td align="center" colspan="2" style="font-size: 10px" class="linebordercenter" >NOMBRES</td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter">TOTAL FALTAS</td>
                
            </tr>

            @foreach($listaF as $value)
            <tr>
                <td align="left" colspan="1" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->persona_codigo }}</td>
                <td align="left" colspan="2" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->persona_nombres.' '.$value->persona_apellidos }}</td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->control_faltas }}</td>
            </tr>
            @endforeach


		</table>

        
	</div>
</body>
</html>