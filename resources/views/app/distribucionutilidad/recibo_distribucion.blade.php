
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
		<table width ="50%" class="table table-striped contenedor">
			<tr>
				<td style="font-size: 10px" colspan="4"><strong>FUCAF</strong></td>
			</tr>
			<tr >
                <td style="font-size: 10px" colspan="4"><strong>PARA MAYOR INFORMACIÓN:</strong></td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="4">BANCA POR TELEFONO: (074)-6613785</td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="4"></td>
			</tr>
            <tr>
                <td style="font-size: 10px" ><strong>FECHA: </strong></td>
                <td style="font-size: 10px" >{{ Date::parse($credito->fechai)->format('d/m/y') }}</td>
                <td style="font-size: 10px" ><strong>HORA: </strong></td>
                <td style="font-size: 10px" >{{ Date::parse($credito->fechai)->format('H:i:s') }} </td>
			</tr>
            <tr >
                <td style="font-size: 10px" ><strong>N° OPE.: </strong></td>
                <td style="font-size: 10px" >{{  $numoperacion }}</td>
                <td style="font-size: 10px" ><strong>COD. CLI.: </strong></td>
                <td style="font-size: 10px" >{{$persona->codigo}} </td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="4"></td>
			</tr>
            <tr >
                <td align="center"  style="font-size: 10px" colspan="4">
                        <strong>---------------------------------CREDITO---------------------------------</strong>
                </td>
			</tr>
            <tr >
                <td style="font-size: 10px"><strong>CLIENTE: </strong></td>
                <td style="font-size: 10px" colspan="3">{{$persona->nombres." ".$persona->apellidos}} </td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="3"></td>
                <td style="font-size: 10px"></td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="2"><strong>MONTO CREDITO S/.:</strong></td>
                <td style="font-size: 10px" colspan="2">{{  round($credito->valor_credito,1)}}</td>
			</tr>
            <tr >
                <td style="font-size: 10px" colspan="2"><strong>PERIODO: </strong></td>
                <td style="font-size: 10px" colspan="2">{{ $credito->periodo }}</td>
            </tr>
            <tr >
                <td align="center"  style="font-size: 10px" colspan="4">
                        -------------------------------------------------------------------------------
                </td>
            </tr>
		</table>
	</div>
</body>
</html>