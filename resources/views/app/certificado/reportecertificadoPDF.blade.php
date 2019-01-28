
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
        .contenedor{
            border-collapse: collapse;
            border:4px solid #808080;
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
	<div class="contenedor">
		<table width="100%" border="0px" class="">
            <tr>
            <td colspan="9"></td>
            </tr>
            <tr>
                <td rowspan="1" colspan="2"></td>
                <td rowspan="7" colspan="5" align="right" ><img src="assets/images/users/certificado.png" height="100px" /></td>
                <td rowspan="1" align="rigth" style="font-size: 11px" colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td colspan="2" align="rigth" style="font-size: 11px" >Certificado</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td rowspan="1" align="rigth" style="font-size: 11px" colspan="2">N° {{ $certificado->codigo }}</td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td align="rigth" style="font-size: 11px" colspan="2"></td>
            </tr>

            <tr>
                <td colspan="2"></td>
                <td align="rigth" style="font-size: 11px" colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td align="rigth" style="font-size: 11px" colspan="2"></td>
            </tr>
            <tr>
                <td colspan="2"></td>
                <td align="rigth" style="font-size: 11px" colspan="2"></td>
            </tr>
            <tr>
                <td rowspan="1" colspan="2"></td>
                <td rowspan="1" colspan="5" align="center" style="font-size: 14px" >Capital: S/. {{ $certificado->capital }}</td>
                <td rowspan="1" align="rigth"  colspan="2"></td>
            </tr>
            <tr>
                <td colspan="9" style="font-size: 12px" align="center">
                    <p style="font-style: italic;  font-weight:bold; font-color:#ffffff; font-family:'Helvetica','Verdana','Monaco',sans-serif;">
                    Certificamos que @if($persona->sexo == 'F') Doña {{ $persona->apellidos.' '.$persona->nombres }} @else Don {{ $persona->apellidos.' '.$persona->nombres }} @endif 
                    es propietario de {{ $certificado->num_acciones }} acciones de la “Financiera Única de Crédito y Ahorro Familiar”, de valor pagado de S/. 10.00 (Diez con 00/100 Nuevos Soles) cada una.  
                    Las mismas que están inscritas en el registro de accionistas desde la acción Nº {{$certificado->inicio}}  hasta la acción Nº {{$certificado->fin}}, 
                    correspondiente al @if($certificado->semestre <= 6) Primer @else Segundo @endif Semestre del año {{Date::parse($certificado->fechai )->format('Y')}} ().
                    </p>
                </td>
            </tr>
            <tr>
                <td colspan="9"></td>
            </tr>
            <tr>
                <td rowspan="1" colspan="2"></td>
                <td rowspan="1" colspan="4" align="center" style="font-size: 14px" ></td>
                <td rowspan="1" align="center"  colspan="3">Chiclayo, {{ Date::parse($certificado->fechaf )->format('d') }} de {{ $month[intval(date('m', strtotime($certificado->fechaf)))]}} del {{Date::parse($certificado->fechaf )->format('Y')}} </td>
            </tr>
            <tr>
                <td colspan="9"></td>
            </tr>
            <tr>
                <td colspan="9"></td>
            </tr>
            <tr>
                <td rowspan="1" colspan="3" align="center">-----------------------------------------</td>
                <td rowspan="1" colspan="3" align="center" >-----------------------------------------</td>
                <td rowspan="1" align="rigth"  colspan="3"></td>
            </tr>
            <tr>
                <td rowspan="1" colspan="3" align="center">Rocio del Pilar Castillo Rojas</td>
                <td rowspan="1" colspan="3" align="center" >Harold Helbert Lopez Osorio</td>
                <td rowspan="1" align="rigth"  colspan="3"></td>
            </tr>
            <tr>
                <td rowspan="1" colspan="3" align="center">TESORERO</td>
                <td rowspan="1" colspan="3" align="center" >PRESIDENTE</td>
                <td rowspan="1" align="rigth"  colspan="3"></td>
            </tr>
            
		</table>

        
	</div>
</body>
</html>