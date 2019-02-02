
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
				<td align="center" style="font-size: 15px" colspan="4">REPORTE DE CAJA</td>
			</tr>
			<tr >
				<td align="center" style="font-size: 10px" colspan="3"><b>LA FINANCIERA ÚNICA DE CRÉDITO Y AHORRO FAMILIAR – FUCAF</b></td>
				<td rowspan="6" align="right" ><img src="assets/images/users/fucaf.png" width="125" height="95" /></td>
			</tr>
			<tr>
				<td align="center" style="font-size: 10px" colspan="3">LAS BRISAS-CHICLAYO</td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="3">
					<b>Fecha y hora de apertura:</b> {{ Date::parse($caja->fecha_horaApert)->format('d/m/y') }} {{ Date::parse($caja->fecha_horaApert)->format('H:i:s') }}
				</td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="3">
					<b>Fecha y hora de cierre:</b> {{ Date::parse($caja->fecha_horaCierre)->format('d/m/y') }} {{ Date::parse($caja->fecha_horaCierre)->format('H:i:s') }}
				</td>
			</tr>
			<tr>
				<td align="left" style="font-size: 10px" colspan="1">
					<b>Monto inicio:</b> {{ number_format($caja->monto_iniciado,1) }} 
				</td>
				<td align="left" style="font-size: 10px" colspan="1">
					<b>Monto cierre:</b> {{ number_format($caja->monto_cierre,1) }}
				</td>
				<td align="left" style="font-size: 10px" colspan="1">
					<b>Cajero:</b> {{ $persona->nombres.' '.$persona->apellidos }}
				</td>
			</tr>
		</table>
	</div>

	<table width="100%" class="tabla3">
            <tr>
                <td width="4%" align="center" class="fondo"><strong>#</strong></td>
                <td width="8%" align="center" class="fondo"><strong>FECHA</strong></td>
				<td width="8%" align="center" class="fondo"><strong>IMPORTE</strong></td>
                <td width="20%" align="center" class="fondo"><strong>CONCEPTO</strong></td>
                <td width="6%" align="center" class="fondo"><strong>TIPO</strong></td>
                <td width="25%" align="center" class="fondo"><strong>USUARIO/CLIENTE</strong></td>
				<td width="30%" align="center" class="fondo"><strong>DESCRIPCION</strong></td>
            </tr>
            @foreach($lista as $value )
            <tr>
                <td width="4%" align="center"><span class="text">{{ $loop->iteration }}</span></td>
                <td width="8%" align="center"><span class="text">{{ Date::parse($value->fecha )->format('Y/m/d')}}</span></td>
                <td width="8%" align="center"><span class="text">{{ number_format($value->monto,1) }}</span></td>
                <td width="20%" align="left"><span class="text">{{ $value->concepto->titulo }}</span></td>
				@if ($value->concepto->tipo === 'I')
                <td width="6%" align="center"><span class="text" style="color:green;font-weight: bold;">Ingreso</span></td>
				@else
				<td width="6%" align="center"><span class="text" style="color:red;font-weight: bold;">Egreso</span></td>
				@endif
				@if ($value->persona_id !== null)
				<td width="25%" align="left"><span class="text">{{ $value->persona->nombres.' '.$value->persona->apellidos }}</span></td>
				@else
				<td width="25%" align="center"><span class="text">---</span></td>
				@endif
				<td width="30%" align="left"><span class="text">{{ $value->descripcion }}</span></td>
            </tr>
            @endforeach
    </table>

	<br>
	<br>
	RESUMEN DE CAJA
	<br>
	<table width="40%" class="table-bordered table-striped table-condensed tabla3" align="center">
					
					<tr>
						<td width="20%" align="center" class="fondo"><strong>Ingresos: </strong></td>
						<td width="20%" align="center" ><strong>{{ number_format($ingresos,1) }}</strong></td>
					</tr>
					<tr>
						<td width="20%" align="center" class="fondo"><strong>Egresos: </strong></td>
						<td width="20%" align="center" ><strong>{{ number_format($egresos,1) }}</strong></td>
					</tr>
					<tr>
						<td width="20%" align="center" class="fondo"><strong>Saldo: </strong></td>
						<td width="20%" align="center" ><strong>{{ number_format($diferencia,1) }}</strong></td>
					</tr>

				</table>
				<br/>
				<br/>
				<br/>
				<div class="contenedor">
					<table border="0" cellspacing="3" cellpadding="2" style="margin: 50px;" class="table table-striped">
						<tr>
						<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2"> </td>
						<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2"> </td>
						<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">TESORERO------------------------------------</td>
						<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">PRESIDENTE----------------------------------</td>
						</tr>
						<tr>
							<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2"> </td>
							<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2"> </td>
							<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">{{ $tesorero->apellidos.' '.$tesorero->nombres}}</td>
							<td style="text-transform: uppercase;" align="center" style="font-size: 10px" colspan="2">{{ $presidente->apellidos.' '.$presidente->nombres}}</td>
						</tr>
					</table>
				</div>

</body>
</html>