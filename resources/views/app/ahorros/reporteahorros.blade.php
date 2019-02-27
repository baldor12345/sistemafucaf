<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style type="text/css">
        .linebordercenter tr{
            text-align : center;
            border: 0.9px solid #3f3f3f;
            font-size: 10px;
        }
       
        table{
            padding: 0px 4px;
            border-collapse: collapse;
        }
        td{
            text-align : left;
            border: 0.9px solid #3f3f3f;
          
        }
        table thead {
            color: #000000;
            font-weight:bold;
            background-color: #cbd3d6;
            font-size: 10px;
        }
        th {
            color: #000000;
            font-weight:bold;
            border: 0.9px solid #3f3f3f;
            background-color: #cbd3d6;
            text-align : center;
        }
        

        </style>
</head>
    <body>
        <h2 width="70%" style="text-align: center; margin: 20px;">{{ $titulo }}</h2>
        <div>
            <table class="linebordercenter" width ="100%">
                <thead class="">
                    <tr><th  colspan="13">{{ $anio }}</th></tr>
                    <tr>
                        <th rowspan="2" width="28%">Nombres</th>
                        <th colspan="2" width="12%">Ene</th>
                        <th colspan="2" width="12%">Feb</th>
                        <th colspan="2" width="12%">Mar</th>
                        <th colspan="2" width="12%">Abr</th>
                        <th colspan="2" width="12%">May</th>
                        <th colspan="2" width="12%">Jun</th>
                    </tr>
                    <tr>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                            for ($indice= 0; $indice<count($personas) ; $indice++) {
                            $fila = "<tr>";
                            $contador=0;
                            $fila = $fila.'<td width="28%">'.$personas[$indice]->apellidos.' '.$personas[$indice]->nombres.'</td>';
                            for($mes=1; $mes<=6; $mes++){
                                $cont2 = 0;
                                for ($indice2=0; $indice2<count($listaahorros) ; $indice2++) {
                                    if($listaahorros[$indice2]->mes == $mes & $listaahorros[$indice2]->persona_id == $personas[$indice]->id){
                                        $fila = $fila.'<td width="6%">'.$listaahorros[$indice2]->capital.'</td><td width="6%">'.$listaahorros[$indice2]->interes.'</td>';
                                        $contador++;
                                        $cont2++;
                                        break;
                                    }
                                }
                                if($cont2== 0){
                                    $fila = $fila.'<td width="6%">-</td><td width="6%">-</td>';  
                                }
                            }
                            $fila = $fila."</tr>";
                            if($contador> 0){
                                echo($fila);
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
        <div>
            <table class="linebordercenter" width ="100%">
                <thead class="linebordercenter">
                    <tr><th  colspan="13" text-align="center">{{ $anio }}</th></tr> 
                    <tr>
                        <th rowspan="2" width="28%">Nombres</th>
                        <th colspan="2" width="12%">Jul</th>
                        <th colspan="2" width="12%">Ago</th>
                        <th colspan="2" width="12%">Set</th>
                        <th colspan="2" width="12%">Oct</th>
                        <th colspan="2" width="12%">Nov</th>
                        <th colspan="2" width="12%">Dic</th>
                    </tr>
                    <tr>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                        <th width="6%">C</th><th width="6%">I</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        for ($indice= 0; $indice<count($personas) ; $indice++) {
                            $fila = '<tr>';
                                $contador = 0;
                            $fila = $fila.'<td width="28%">'.$personas[$indice]->apellidos.' '.$personas[$indice]->nombres.'</td>';
                            for($mes=7; $mes<=12; $mes++){
                                $cont2=0;
                                for ($indice2=0; $indice2<count($listaahorros) ; $indice2++) {
                                    if($listaahorros[$indice2]->mes == $mes & $listaahorros[$indice2]->persona_id == $personas[$indice]->id){
                                        $fila = $fila.'<td width="6%">'.$listaahorros[$indice2]->capital.'</td><td width="6%">'.$listaahorros[$indice2]->interes.'</td>';
                                        $contador++;
                                        $cont2++;
                                        break;
                                    }
                                }
                                if($cont2== 0){
                                    $fila = $fila.'<td width="6%">-</td><td width="6%">-</td>';  
                                }
                            }
                            $fila = $fila.'</tr>';
                            if($contador> 0){
                                echo($fila);
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
