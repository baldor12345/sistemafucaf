
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
                <td style="font-size: 15px" >{{$fechaahorro}}</td>
                <td style="font-size: 15px" >HORA: </td>
                <td style="font-size: 15px" >{{$horaahorro}} </td>
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
                <td style="font-size: 15px">{{$ahorroactual}}</td>
			</tr>
            <tr >
                <td style="font-size: 15px" colspan="2">A CUANTOS MESES</td>
                <td style="font-size: 15px" colspan="2">POR DEFINIR</td>
			</tr>
		</table>
        
	</div>

</body>
</html>