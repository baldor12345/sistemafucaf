
<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
        .linebordercenter tr th, td{
            text-align : center;
            border: 0.9px solid #3f3f3f;
            
            font-size: 10px;
        }
        table thead {
        color: #000000;
        font-weight:bold;
        background-color: #dee1e5;
        font-size: 12px;
        
        }
        table th {
        color: #000000;
        font-weight:bold;
        background-color: #dee1e5;
        }

        </style>
</head>
<body>
    <h2 width="70%" style="text-align: center; margin: 20px;">{{ $distribucion->titulo }}</h2>





    <div>
        <table class="linebordercenter" width ="100%">
            <thead>
                <tr>
                    <th colspan="2" align="center">PASO 4: </th>
                    <th colspan="3" align="center">Se multiplica esta utilidad.</th>
                    <th colspan="2" align="center">{{ round(($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0, 1) }}</th>
                    <th colspan="9" align="center">por el N° de meses que ha trabajado cada accion. Los resultados son las diferentes utilidades de una accion en un año.</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="2"></td><td align='center' colspan="12">{{ $anio }}</td><td align='center'>{{ $anio_actual }}</td><td align='center' rowspan="2">TOTAL</td></tr>
                <tr>
                    <td colspan="2" align='center'>Meses</td>
                    <td>E</td><td>F</td><td>M</td><td>A</td><td>M</td><td>J</td><td>J</td><td>A</td><td>S</td><td>O</td><td>N</td><td>D</td><td>E</td>
                    
                </tr>
                <tr>
                    <td rowspan="2">Utilidad de una acción</td>
                    <td>En 1 mes</td>
                    <td align='center' colspan="14">{{ round(($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0, 1) }}</td>
                </tr>
                <tr>
                    <td>En el año</td>
                    <?php
                    $factores_mes=array();
                    $f=0;
                    $factor = ($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0;
                        for ($i=12; $i >0 ; $i--) { 
                            echo("<td align='center'>".round($i * $factor,1)."</td>");
                            $factores_mes[$f] = $i * $factor;
                            $f++;
                        }
                    ?>
                    <td align='center'>-</td>
                    <td align='center'>...</td>
                </tr>
            </tbody>
            <tfoot>

            </tfoot>
        </table>
    </div>
    <div>
        <table class="linebordercenter" width ="100%">
            <thead>
                <tr>
                    <th align="center">PASO 5:  Se multiplica cada una de estas utilidades anuales por el número de acciones de cada socio en el mes respectivo.  Los resultados son las utilidades del socio en cada uno de los  meses.</th>
                </tr>
            </thead>
        </table>
    </div>
    </div>
        <table class="linebordercenter" width ="100%">
            <thead class="linebordercenter">
                <tr><th colspan="4" align="center">PASO 6: Se sumasn estas utilidades mensuales y se obtiene  la UTILIDAD TOTAL del socio en el año (última columna de la derecha).</th></tr>
                <tr><th align="center">N°</th><th align="center">SOCIOS</th><th align='center'>Total Acciones</th><th align='center'>FIRMA</th></tr>
            </thead>
            <tbody>
                <?php
                echo($distrib_util);
                ?>
            </tbody>
            <tfoot class="linebordercenter">
                <tr>
                    <th colspan="2">TOTAL</th>
                    <th>-</th>
                    <?php
                        $total_acc_mensual  = 0;
                        $ind = 0;
                        
                        for($i=1; $i<=12; $i++){
                            if((($ind<count($acciones_mensual))?$acciones_mensual[$ind]->mes: "") == "".$i){
                                // echo("<th align='center'>".($acciones_mensual[$ind]->cantidad_mes > 0?$acciones_mensual[$ind]->cantidad_mes : "-" )."</th>");
                                $total_acc_mensual += $acciones_mensual[$ind]->cantidad_mes;
                                $ind ++;
                            }else{
                                // echo("<th align='center'>-</th>");
                            }
                        }

                    ?>
                    <th>{{ ($total_acc_mensual > 0?$total_acc_mensual: "-" ) }}</th>
                    
                
                        <?php
                        $j=12;
                        $indice=0;
                        $sumatotal_utilidades = 0;
                        
                        for($i=1; $i<=12; $i++){
                            if((($indice<count($acciones_mensual))?$acciones_mensual[$indice]->mes:"") == $i){
                                $sumatotal_utilidades += $acciones_mensual[$indice]->cantidad_mes * $factor*$j;
                                // echo("<th align='center'>".(round($acciones_mensual[$indice]->cantidad_mes * $factor *$j, 1) > 0?round($acciones_mensual[$indice]->cantidad_mes * $factor *$j, 1) : "-" )."</th>");
                                $j--;
                                $indice++;
                            }else{
                                // echo("<th align='center'>-</th>");
                            }
                        }
                        
                        ?>
                   <th>{{ round($sumatotal_utilidades, 1) }}</th>
                    <th></th>

                </tr>
               
            </tfoot>
        </table>
    </div>
</body>
</html>
