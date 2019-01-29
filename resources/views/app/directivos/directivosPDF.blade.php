
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
        .contenedor{
            border-collapse: collapse;
            border:1px solid #808080;
        }
        td{
            font-size: 10px;
        }
        
        .lineborderleft{
            border: 0.9px solid #3f3f3f;
            text-align : left;;
        }
        .linebordercenter{
            border: 0.9px solid #3f3f3f;
            text-align : center;
        }
        .line_h_b{
            border-bottom: 0.9px solid #3f3f3f;
        }
        .line_h_b2{
            border-bottom: 0.2px solid #afafaf;
        }
        .line_h_t{
            border-top: 0.9px solid #3f3f3f;
        }
        .line_v_id{
            border-left: 0.9px solid #3f3f3f;
            border-right: 0.9px solid #3f3f3f;
        }
    </style>
</head>
<body>
	<div class="">
		<table width="100%" border="0px" class="">
            <tr>
                <td align="center" style="font-size: 13px" colspan="7">LA FINANCIERA ÚNICA DE CRÉDITO Y AHORRO FAMILIAR <br> FUCAF</td>
                <td rowspan="6" colspan="2" align="right" ><img src="assets/images/users/fucaf.png" width="140" height="125" /></td>
            </tr>
			<tr>
                <td style="font-size: 11px" colspan="7" rowspan="" align="center"><strong>DIRECTIVOS</strong></td>
            </tr>   
            <tr>
                <td cellspacing="9" cellpadding="2" colspan="2" style="font-size: 11px" align="center" >PERIODO</td>
                <td style="font-size: 11px" colspan="5" cellpadding="2"></td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 11px">{{ '  ' }} INICIO: </td>
                <td style="font-size: 11px" colspan="5">{{ date('d', strtotime($directivos->periodoi)).' '.$month[intval(date('m', strtotime($directivos->periodoi)))].' del '.date('Y', strtotime($directivos->periodoi)) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 11px">{{ '  ' }} FIN: </td>
                <td style="font-size: 11px" colspan="5">{{ date('d', strtotime($directivos->periodof)).' '.$month[intval(date('m', strtotime($directivos->periodof)))].' del '.date('Y', strtotime($directivos->periodof)) }}</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size: 11px"> {{ '  ' }}ESTADO:</td>
                <td style="font-size: 11px" colspan="5">@if($directivos->estado == 'A') Activos @else Inactivos @endif </td>
            </tr>

            <tr>
                <td align="center" style="font-size: 11px" colspan="9" >RELACION DE DIRECTIVOS ACTIVOS</td>
            </tr>
            <tr>
                <td align="center" colspan="3" style="font-size: 11px" class="linebordercenter" >PREDIDEMTE: </td>
                <td align="left" colspan="6" style="font-size: 11px" class="linebordercenter" >{{ '  '.$presidente->apellidos.'   '.$presidente->apellidos}}</td>
            </tr>
            <tr>
                <td align="center" colspan="3" style="font-size: 11px" class="linebordercenter" >SECRETARIO: </td>
                <td align="left" colspan="6" style="font-size: 11px" class="linebordercenter" >{{ '  '.$secretario->apellidos.'   '.$secretario->apellidos}}</td>
            </tr>
            <tr>
                <td align="center" colspan="3" style="font-size: 11px" class="linebordercenter" >TESORERO: </td>
                <td align="left" colspan="6" style="font-size: 11px" class="linebordercenter" >{{ '  '.$tesorero->apellidos.'   '.$tesorero->apellidos}}</td>
            </tr>
            <tr>
                <td align="center" colspan="3" style="font-size: 11px" class="linebordercenter" >VOCAL: </td>
                <td align="left" colspan="6" style="font-size: 11px" class="linebordercenter" >{{ '  '.$vocal->apellidos.'   '.$vocal->apellidos}}</td>
            </tr>
            

		</table>

        
	</div>
</body>
</html>