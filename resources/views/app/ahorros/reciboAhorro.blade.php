
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
	.contenedor{
			position: absolute;
			top:50%;
			left:50%;
			width:200px;
			margin-left:-200px;
			/*determinamos una altura*/
			height:300px;
			/*indicamos que el margen superior, es la mitad de la altura*/
			margin-top:-150px;
			border:1px solid #808080;
			padding:5px;
		}
</style>

</head>
<body>
	<div class="contenedor">
		<table border="0" cellspacing="3" cellpadding="2" style="margin: 50px;" class="table table-striped">
			<tr>
				<td style="font-size: 15px" colspan="4">FUCAF</td>
			</tr>
			<tr >
                <td style="font-size: 15px" colspan="4">LAS BRISAS-CHICLAYO</td>
			</tr>
            <tr >
                <td style="font-size: 15px" colspan="4"></td>
			</tr>
            <tr >
                <td style="font-size: 15px" colspan="4"></td>
			</tr>
            <tr >
                <td style="font-size: 15px" >FECHA: </td>
                <td style="font-size: 15px" >{{ Date::parse($fechaahorro)->format('d/m/y') }}</td>
                <td style="font-size: 15px" >HORA: </td>
                <td style="font-size: 15px" >{{ Date::parse($fechacreate)->format('H:i:s') }} </td>
			</tr>
            <tr >
                <td style="font-size: 15px" >N° OPE.: </td>
                <td style="font-size: 15px" >{{$numoperacion}}</td>
                <td style="font-size: 15px" >COD. CLI.: </td>
                <td style="font-size: 15px" >{{$codcliente}} </td>
			</tr>
            <tr >
                <td style="font-size: 15px" colspan="4"></td>
			</tr>
            <tr >
                <td align="center"  style="font-size: 15px" colspan="4">
                ---------------AHORRO---------------
                </td>
			</tr>
            <tr >
                <td style="font-size: 15px">CLIENTE: </td>
                <td style="font-size: 15px" colspan="3">{{$nombrecliente}} </td>
			</tr>
            <tr >
                <td style="font-size: 15px" colspan="3"></td>
                <td style="font-size: 15px">Ahorro Actual</td>
			</tr>
            <tr >
                <td style="font-size: 15px" colspan="2">MONTO AHORRADO S/.</td>
                <td style="font-size: 15px">{{$montoahorrado}}</td>
                <td style="font-size: 15px">{{ $ahorroactual }}</td>
			</tr>
            <tr >
                <td style="font-size: 15px" colspan="2">A CUANTOS MESES</td>
                <td style="font-size: 15px" colspan="2">POR DEFINIR</td>
			</tr>
		</table>
	</div>
</body>
</html>