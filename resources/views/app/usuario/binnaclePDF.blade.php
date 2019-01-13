
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
                <td align="center" style="font-size: 11px" colspan="9" >BITACORA DESDE {{ $desde }} hasta {{ $hasta }}</td>
            </tr>
            <tr>
                <td align="center" colspan="2" style="font-size: 8px" class="linebordercenter" >NOMBRES</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter" >CODIGO</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">ACCION</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">FORMULARIO</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">FECHA</td>
                <td align="center" colspan="3" style="font-size: 6px" class="linebordercenter">DETALLE</td>
                
            </tr>
            @foreach($lista as $value)
            <tr>
                <td align="center" colspan="2" style="font-size: 8px" class="linebordercenter" >{{ $value->persona_nombres.' '.$value->persona_apellidos }}</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter" >{{ $value->persona_codigo }}</td>
                @if($value->accion == 'I')
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">Insertar</td>
                @elseif($value->accion == 'U')
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">Modificar</td>
                @else
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">Eliminar</td>
                @endif
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">{{ $value->tabla }}</td>
                <td align="center" colspan="1" style="font-size: 8px" class="linebordercenter">{{ $value->fecha_hora }}</td>
                <td align="center" colspan="3" style="font-size: 8px" class="linebordercenter">{{ $value->detalle }}</td>
            </tr>
            @endforeach

		</table>

        
	</div>
</body>
</html>