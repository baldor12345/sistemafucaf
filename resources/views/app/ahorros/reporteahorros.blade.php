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
            border-t
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
            <?php
                $tbody1 ="";
                $tbody2 ="";
                for ($indice= 0; $indice<count($personas) ; $indice++) {
                        $fila1 = "<tr>";
                        $fila2 = "<tr>";
                        $contador=0;
                        $interesPagado1 = 0;
                        $interesPagado2 = 0;
                        $fila1 = $fila1.'<td width="20%">'.$personas[$indice]->apellidos.' '.$personas[$indice]->nombres.'</td>';
                        $fila2 = $fila2.'<td width="20%">'.$personas[$indice]->apellidos.' '.$personas[$indice]->nombres.'</td>';
                        for($mes=1; $mes<=12; $mes++){
                            $cont1 = 0;
                            $cont2 = 0;
                           
                            for ($indice2=0; $indice2<count($listaahorros) ; $indice2++) {
                                if($listaahorros[$indice2]->mes == $mes & $listaahorros[$indice2]->persona_id == $personas[$indice]->id){
                                    if($mes<=6){
                                        $fila1 = $fila1.'<td width="6%">'.round($listaahorros[$indice2]->capital,1).'</td><td width="6%">'.round($listaahorros[$indice2]->interes, 1).'</td>';
                                        $interesPagado1 += round($listaahorros[$indice2]->interes, 1);
                                        $contador++;
                                        $cont1++;
                                    }else{
                                        $fila2 = $fila2.'<td width="6%">'.round($listaahorros[$indice2]->capital,1).'</td><td width="6%">'.round($listaahorros[$indice2]->interes, 1).'</td>';
                                        $interesPagado2 += round($listaahorros[$indice2]->interes, 1);
                                        $contador++;
                                        $cont2++;
                                    }
                                    break;
                                }
                            }
                            if($cont1== 0 & $mes<=6){
                                $fila1 = $fila1.'<td width="6%">-</td><td width="6%">-</td>';  
                            }
                            if($cont2== 0 & $mes >6){
                                $fila2 = $fila2.'<td width="6%">-</td><td width="6%">-</td>';  
                            }
                        }

                        $fila1 = $fila1.'<td width="8%">'.round($interesPagado1,1).'</td></tr>';
                        $fila2 = $fila2.'<td width="8%">'.round($interesPagado2,1).'</td></tr>';
                       
                        if($contador> 0){
                            $tbody1 = $tbody1.$fila1;
                            $tbody2 = $tbody2.$fila2;
                        }
                    }
            ?>

            <table class="linebordercenter" width ="100%">
                <thead class="">
                    <tr><th  colspan="14">{{ $anio }}</th></tr>
                    <tr>
                        <th rowspan="2" width="20%">Nombres</th>
                        <th colspan="2" width="12%">Ene</th>
                        <th colspan="2" width="12%">Feb</th>
                        <th colspan="2" width="12%">Mar</th>
                        <th colspan="2" width="12%">Abr</th>
                        <th colspan="2" width="12%">May</th>
                        <th colspan="2" width="12%">Jun</th>
                        <th rowspan="2" width="8%">Total Int. Pag</th>
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
                    echo($tbody1);
                    ?>
                </tbody>
            </table>
        </div>
        <div>
            <table class="linebordercenter" width ="100%">
                <thead class="linebordercenter">
                    <tr><th  colspan="14" text-align="center">{{ $anio }}</th></tr> 
                    <tr>
                        <th rowspan="2" width="20%">Nombres</th>
                        <th colspan="2" width="12%">Jul</th>
                        <th colspan="2" width="12%">Ago</th>
                        <th colspan="2" width="12%">Set</th>
                        <th colspan="2" width="12%">Oct</th>
                        <th colspan="2" width="12%">Nov</th>
                        <th colspan="2" width="12%">Dic</th>
                        <th rowspan="2" width="8%">Total Int. Pag</th>
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
                    echo($tbody2);
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
