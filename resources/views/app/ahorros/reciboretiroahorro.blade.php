
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
	<div>
		<table width="50%" class="table contenedor">
                <thead></thead>
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
                <td style="font-size: 10px" >{{ Date::parse($fecharetiro)->format('d/m/y') }}</td>
                <td style="font-size: 10px" >HORA: </td>
                <td style="font-size: 10px" >{{ Date::parse($fecharetiro)->format('H:i:s') }} </td>
			</tr>
            <tr >
                <td style="font-size: 10px" ><strong>N° OPE.: </strong></td>
                <td style="font-size: 10px" >{{$numoperacion}}</td>
                <td style="font-size: 10px" ><strong>COD. CLI.: </strong></td>
                <td style="font-size: 10px" >{{$codcliente}} </td>
			</tr>
            <tr >
                <td style="font-size: 15px" colspan="4"></td>
			</tr>
            <tr >
                <td align="center"  style="font-size: 10px" colspan="4">
                        <strong>---------------------------RETIRO--------------------------- </strong>
                </td>
			</tr>
            <tr >
                <td style="font-size: 10px"><strong>CLIENTE: </strong></td>
                <td style="font-size: 10px" colspan="3">{{$nombrecliente}} </td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="4"></td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="2"><strong>MONTO RETIRADO S/.: </strong></td>
                <td style="font-size: 10px">{{$montoretirado}}</td>
                <td style="font-size: 10px"></td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="4"></td>
            </tr>
            <tr >
                <td style="font-size: 10px; text-align: center" colspan="4">----------------------------------------------------------</td>
            </tr>
		</table>
	</div>
</body>
</html>