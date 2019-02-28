
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
	table{
        border-collapse: collapse;
    }
    td{
        font-size: 8px;
    }
    h1{
        font-size: 8px;
        text-align:center;
        font-weight: bold;
    }
    .tabla2 {
        margin-bottom: 10px;
    }

    .tabla3 td{
        border: 0.9px solid #000;
        text-align : left;;
    }
    .emisor{
        color: red;
    }
    .linea{
        border-bottom: 1px dotted #000;
    }
    .border{
        border: 1px solid #000;
    }
    .fondo{
        background-color: #dfdfdf;
    }
    .fisico{
        color: #fff;
    }
    .fisico td{
        color: #fff;
    }
    .fisico .border{
        border: 1px solid #fff;
    }
    .fisico .tabla3 td{
        border: 1px solid #fff;
    }
    .fisico .linea{
        border-bottom: 1px dotted #fff;
    }
</style>

</head>
<body>
	<div class="contenedor">
		<table border="0" cellspacing="3" cellpadding="2" style="margin: 50px;" class="table table-striped">
			<tr>
			<td style="text-transform: uppercase;" align="center" style="font-size: 15px" colspan="4">Bitarora del mes de {{ $cboMonth[$month] }} del {{ $anio }}</td>
			</tr>
		</table>
	</div>

	<table width="100%" class="tabla3">
            <thead>
                <tr>
                    <td cellspacing="1" width="5%" align="center" class="fondo"><strong>#</strong></td>
                    <td cellspacing="1" width="25%" align="center" class="fondo"><strong>NOMBRES</strong></td>
                    <td cellspacing="1" width="10%" align="center" class="fondo"><strong>CODIGO</strong></td>
                    <td cellspacing="1" width="10%" align="center" class="fondo"><strong>ACCION</strong></td>
                    <td cellspacing="1" width="10%" align="center" class="fondo"><strong>FORMULARIO</strong></td>
                    <td cellspacing="1" width="15%" align="center" class="fondo"><strong>FECHA</strong></td>
                    <td cellspacing="1" width="25%" align="center" class="fondo"><strong>DETALLE</strong></td>
                </tr>
            </thead>
            <tbody>
                @foreach($lista1 as $value )
                <tr>
                    <td width="5%" align="center"><span class="text">{{ $loop->iteration }}</span></td>
                    <td width="25%" align="left"><span class="text">{{ $value->persona_nombres.' '.$value->persona_apellidos }}</span></td>
                    <td width="10%" align="center"><span class="text">{{ $value->persona_codigo }}</span></td>
                    @if($value->accion == 'I')
                    <td width="10%" align="center"><span class="text">Insertar</span></td>
                    @elseif($value->accion == 'U')
                    <td width="10%" align="center"><span class="text">Modificar</span></td>
                    @else
                    <td width="10%" align="center"><span class="text">Eliminar</span></td>
                    @endif
                    <td width="10%" align="center"><span>{{$value->tabla}}</span></td>
                    <td width="15%" align="center"><span>{{$value->fecha_hora}}</span></td>

                    @if($value->accion == 'I')
                    <td width="25%" align="center"><span class="text" style="color:green;font-weight: bold;">se hizo un nuevo registro en {{ $value->tabla }}</span></td>
                    @elseif($value->accion == 'U')
                    <td width="25%" align="center"><span class="text" style="color:wite;font-weight: bold;">ha modificado un registro en {{ $value->tabla }}</span></td>
                    @else
                    <td width="25%" align="center"><span class="text" style="color:red;font-weight: bold;">ha eliminado un regitro en {{ $value->tabla }}</span></td>
                    @endif
                </tr>
                @endforeach
            </tbody>

	</table>
</body>
</html>