
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style type="text/css">
		
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
			<tr >
				<td align="left" style="font-size: 10px" colspan="2">FUCAF</td><td rowspan="3" align="center" ><img src="assets/images/users/fucaf.png" width="60" height="60" /></td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="2">LAS BRISAS-CHICLAYO</td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="2"></td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="2">
					<b>Fecha:</b> {{ Date::parse($fecha)->format('d/m/y') }} <br>
				</td>
				<td align="left" style="font-size: 10px" colspan="2">
					<b>Hora:</b> {{ Date::parse($fecha)->format('H:i:s') }} <br>
				</td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="1">
					<b>N° OPE.:</b>  <br>
				</td>
				<td align="left" style="font-size: 10px" colspan="3">
					<b>COD. CLI. COMPRADOR:</b> {{ $comprador->codigo }} <br>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size: 15px" colspan="4">
				VENTA ACCION
				</td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="4">
					<b>SOCIO VENDEDOR:</b> {{ $vendedor->nombres }} {{ $vendedor->apellidos }} <br>
				</td>
            </tr>
            <tr>
				<td align="left" style="font-size: 10px" colspan="4">
					<b>SOCIO COMPRADOR:</b> {{ $comprador->nombres }} {{ $comprador->apellidos }} <br>
				</td>
			</tr>
			<tr>
				<td align="center" style="font-size: 15px" colspan="2">TOTAL DE ACCIONES</td>
				<td align="center" style="font-size: 15px" colspan="2">{{ $CantAccioneComprador }}</td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="1">
					<b>CANTIDAD DE ACCIONES:</b>
				</td>
				<td align="left" style="font-size: 10px" colspan="1">
					<b> {{ $cant }}</b>
				</td>
				<td  align="center" >Ahorro Total: <br><b> {{ $monto_ahorroComprador }}</b></td>
			</tr>
		</table>
	</div>

</body>
</html>