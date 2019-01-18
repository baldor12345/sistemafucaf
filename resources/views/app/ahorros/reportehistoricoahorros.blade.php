
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
        .linebordercenter tr th, td{
            border: 0.9px solid #3f3f3f;
            text-align : center;
            
        }
        .linebordercenter{
            padding: 2px;
            margin: 1px;
        }

        .lineborderleft tr td{
            border: 0.9px solid #3f3f3f;
            text-align : left;;
        }
    
    </style>

</head>
<body>
	<div class="">
            <?php
            $nombremes = array('1'=>'Ene',
            '1'=>'Enero',
            '01'=>'Enero',
            '2'=>'Febrero',
            '02'=>'Febrero',
            '3'=>'Marzo',
            '03'=>'Marzo',
            '4'=>'Abril',
            '04'=>'Abril',
            '5'=>'Mayo',
            '05'=>'Mayo',
            '6'=>'Junio',
            '06'=>'Junio',
            '7'=>'Julio',
            '07'=>'Julio',
            '8'=>'Agosto',
            '08'=>'Agosto',
            '9'=>'Septiembre',
            '09'=>'Septiembre',
            '10'=>'Octubre',
            '11'=>'Noviembre',
            '12'=>'Diciembre');
            ?>
        <table class="lineborderleft" width ="40%">
                <tr><td><strong>Nombre del Socio o Cliente</strong> </td></tr>
                <tr><td> {{ $persona->nombres." ".$persona->apellidos }}</td></tr>
        </table>
        <h4>HISTORICO DE AHORROS EN EL AÑO {{ $anio }}</h4>
		<table class ="linebordercenter">
            <thead>
                <tr>
                    <th style="font-size: 8px" colspan="1"><strong>CAPITAL S/.</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>INTERES S/.</strong></th>
                    <th style="font-size: 8px" colspan="1"><strong>MES</strong></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista as $value)
                    <tr >
                        <td style="font-size: 8px" colspan="1">{{ round($value->capital,1) }}</td>
                        <td style="font-size: 8px" colspan="1">{{ round($value->interes,1) }}</td>
                        <td style="font-size: 8px" colspan="1">{{ $nombremes[$value->mes] }}</td>
                    </tr>
                @endforeach
            <tbody>
		</table>
	</div>
</body>
</html>