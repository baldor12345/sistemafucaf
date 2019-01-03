
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
        font-size: 10px;
    }
    h1{
        font-size: 21px;
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
			<td style="text-transform: uppercase;" align="center" style="font-size: 15px" colspan="4">NORMA DEL 20% MAXIMO DE ACCIONES</td>
			</tr>
		</table>
	</div>

	<table width="100%" class="tabla3">
            <tr>
                <td cellspacing="1" width="6%" align="center" class="fondo"><strong>#</strong></td>
                <td cellspacing="1" width="40%" align="center" class="fondo"><strong>Nombre del Socio</strong></td>
				<td cellspacing="1" width="12%" align="center" class="fondo"><strong>N° de Acciones</strong></td>
                <td cellspacing="1" width="12%" align="center" class="fondo"><strong>% de Acciones</strong></td>
                <td cellspacing="1" width="25%" align="center" class="fondo"><strong>Cumple</strong></td>
            </tr>
            
            @foreach($lista as $value )
            <tr>
				<td width="6%" align="center"><span class="text">{{ $loop->iteration }}</span></td>
				<td width="40%" align="left"><span class="text">{{$value->persona_nombres.' '.$value->persona_apellidos}}</span></td>
                <td width="12%" align="center"><span class="text">{{ $value->cantidad_accion }}</span></td>
                <td width="12%" align="center"><span class="text">{{ number_format((($value->cantidad_accion*100)/$cant),2) }}%</span></td>
                @if(number_format((($value->cantidad_accion*100)/$cant),2)<= 0.20)
                <td width="25%" align="center"><span class="text" style="color:green;font-weight: bold;">POSITIVO</span></td>
                @else
                <td width="25%" align="center"><span class="text" style="color:red;font-weight: bold;">NEGATICO</span></td>
                @endIf
            </tr>
            @endforeach
            <tr>
                <td cellspacing="1" width="46%" align="right" ><strong>TOTAL</strong></td>
                <td cellspacing="1" width="12%" align="center" ><strong>{{ $cant }}</strong></td>
                <td cellspacing="1" width="12%" align="center" ><strong>100%</strong></td>
                <td cellspacing="1" width="25%" align="center" ><strong></strong></td>
            </tr>

	</table>

	<br/>
	<div class="contenedor">
        <table >
            <tr>
            <td cellspacing="6" width="100%" align="center" ><strong>CUADRO CALCULADO AL {{ $day }} {{ $month }}  {{ $year }}</strong></td>
            </tr>
        </table>
    </div>
	

</body>
</html>