
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
		.contenedor{
				border-collapse: collapse;
				border:1px solid #808080;
				padding-left: 4px;
			}
	</style>
</head>
<body>
    <table width="50%" class="table contenedor">
            
        <tr>
            <td style="font-size: 10px" colspan="4"><strong>FUCAF</strong></td>
        </tr>
        <tr >
            <td style="font-size: 10px" colspan="4"><strong>LAS BRISAS-CHICLAYO</strong></td>
        </tr>
        <tr >
            <td style="font-size: 10px" colspan="4"></td>
        </tr>
        <tr >
            <td style="font-size: 10px" colspan="4"></td>
        </tr>
        <tr >
            <td style="font-size: 10px" ><strong>FECHA: </strong></td>
            <td style="font-size: 10px" >{{ Date::parse($fechaahorro)->format('d/m/y') }}</td>
            <td style="font-size: 10px" ><strong>HORA: </strong></td>
            <td style="font-size: 10px" >{{ Date::parse($fechaahorro)->format('H:i:s') }} </td>
        </tr>
        <tr >
            <td style="font-size: 10px" ><strong>N° OPE.: </strong></td>
            <td style="font-size: 10px" >{{$numoperacion}}</td>
            <td style="font-size: 10px" ><strong>COD. CLI.: </strong></td>
            <td style="font-size: 10px" >{{$codcliente}} </td>
        </tr>
        <tr >
            <td style="font-size: 10px" colspan="4"></td>
        </tr>
        <tr >
            <td align="center"  style="font-size: 10px" colspan="4">
                <strong>---------------AHORRO---------------</strong>
            </td>
        </tr>
        <tr >
            <td style="font-size: 10px"><strong>CLIENTE: </strong></td>
            <td style="font-size: 10px" colspan="3">{{$nombrecliente}} </td>
        </tr>
        <tr >
            <td style="font-size: 10px" colspan="3"></td>
            <td style="font-size: 10px"><strong>Ahorro Actual</strong></td>
        </tr>
        <tr >
            <td style="font-size: 10px" colspan="2"><strong>MONTO AHORRADO S/.</strong></td>
            <td style="font-size: 10px">{{ round($montoahorrado,1)}}</td>
            <td style="font-size: 10px">{{ round($ahorroactual,1) }}</td>
        </tr>
        <tr >
            <td style="font-size: 10px" colspan="2"><strong>A CUANTOS MESES</strong></td>
            <td style="font-size: 10px" colspan="2">POR DEFINIR</td>
        </tr>
        <tr >
            <td style="font-size: 10px; text-align: center" colspan="4">----------------------------------------------------------</td>
        </tr>
    </table>
</body>
</html>