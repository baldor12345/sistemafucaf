
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
    <table>
        <tr>
            <td align="center" style="font-size: 12px" width="100%" ><strong>Reporte de control de asistencia desde {{$Month[intval(Date::parse($fechai)->format('m'))]}} de {{Date::parse($fechai)->format('Y')}}  hasta {{$Month[intval(Date::parse($fechaf)->format('m'))]}} {{Date::parse($fechaf)->format('Y') }}</strong></td>
        </tr>
        <tr>
            <td align="center" style="font-size: 12px" width="100%"></td>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <td align="left" style="font-size: 12px" colspan="7" >TARDANZAS</td>
            </tr>
            <tr>
                <td colspan="1"></td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter" ><strong>CODIGO</strong></td>
                <td align="center" colspan="2" style="font-size: 10px" class="linebordercenter" ><strong>NOMBRES</strong></td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter"><strong>TOTAL</strong></td>
                <td colspan="1" ></td>
                
            </tr>
        </thead>
        <tbody>
            @foreach($listaT as $value)
            <tr>
                <td colspan="1"></td>
                <td align="left" colspan="1" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->persona_codigo }}</td>
                <td align="left" colspan="2" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->persona_nombres.' '.$value->persona_apellidos }}</td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->control_tardanzas }}</td>
                <td colspan="1"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <td align="left" style="font-size: 12px" colspan="7" >FALTAS</td>
            </tr>
            <tr>
                <td colspan="1"></td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter" ><strong>CODIGO</strong></td>
                <td align="center" colspan="2" style="font-size: 10px" class="linebordercenter" ><strong>NOMBRES</strong></td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter"><strong>TOTAL</strong></td>
                <td colspan="1"></td>
            </tr>
        </thead>
        <tbody>
            @foreach($listaF as $value)
            <tr>
             <td colspan="1"></td>
                <td align="left" colspan="1" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->persona_codigo }}</td>
                <td align="left" colspan="2" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->persona_nombres.' '.$value->persona_apellidos }}</td>
                <td align="center" colspan="1" style="font-size: 10px" class="linebordercenter" >{{ ' '.$value->control_faltas }}</td>
                <td colspan="1"></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>