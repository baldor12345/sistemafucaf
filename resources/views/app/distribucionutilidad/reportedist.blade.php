
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
        <thead class="linebordercenter">
            <tr>
                <th rowspan="8" colspan="1">PASO 1: Se calcula las utilidades</th>
                <th colspan="2" rowspan="2">UTILIDAD BRUTA</th>
                <th colspan="1" rowspan="7"></th>
                <th colspan="2" rowspan="1">GASTOS</th>
                
                <th colspan="1" rowspan="7"></th>
                <th colspan="1" rowspan="2">UTILIDAD NETA</th>
                <th colspan="1" rowspan="7"></th>
                <th colspan="2" rowspan="2">Reservas</th>
                <th colspan="1" rowspan="7"></th>
                <th colspan="1" rowspan="2">UTILIDAD Distribuible</th>

            </tr>
            <tr>
                <th colspan="2" rowspan="1">Gastos Acumulados</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="5"></td>
                <td  colspan="2" rowspan="1" align="center">U. B. Acumulada</td>
                <td rowspan="5"></td>
                <td  colspan="1" rowspan="1">G. Adm. Acum.</td>
                <td  colspan="1" rowspan="1">{{ round($gastadmacumulado,1) }}</td>
                <td rowspan="5"></td>
                <td rowspan="5"></td>
                <td rowspan="5"></td>
                <td  colspan="1" rowspan="2">F Social 10%</td>
                <td  colspan="1" rowspan="2">{{ round($utilidad_neta*0.1,1) }}</td>
                <td rowspan="5"></td>
                <td rowspan="5"></td>
            </tr>
            <tr>
                    
                <td>Intereses</td>
                <td>{{ round($intereses, 1) }}</td>
                <td  colspan="1" rowspan="1">I. Pag. Acum.</td>
                <td  colspan="1" rowspan="1">{{ round($int_pag_acum,1) }}</td>
            </tr>
            <tr>
                    
                <td>Otros</td>
                <td>{{ round($otros, 1) }}</td>
                <td  colspan="1" rowspan="1">Otros Acum.</td>
                <td  colspan="1" rowspan="1">{{ round($otros_acumulados,1) }}</td>
                <td  colspan="1" rowspan="3">R Legal 10%</td>
                <td  colspan="1" rowspan="3">{{ round($utilidad_neta*0.1, 1) }}</td>
            </tr>
            <tr>
                    
                <td>Total acumulado</td>
                <td>{{ round($intereses + $otros, 1) }}</td>
                <td  rowspan="1" colspan="1">TOTAL ACUMULADO</td>
                <td  rowspan="1" colspan="1">{{ round($gastadmacumulado + $int_pag_acum + $otros_acumulados, 1) }}</td>
            </tr>
            <tr>
                    
                <td>U.B DU Anterior</td>
                <td>{{ round($du_anterior, 1) }}</td>
                <td  rowspan="1" colspan="1">Gast. DU Anterior</td>
                <td  rowspan="1" colspan="1">{{ round($gast_du_anterior,1) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td>Utilidad Bruta DU ACTUAL</td>
                <td>{{ round(($intereses + $otros) -  $du_anterior, 1) }}</td>
                <td>menos</td>
                <td>Gast. DU ACTUAL</td>
                <td>{{ round(($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior,1) }}</td>
                <td>=</td>
                <td>{{ round((($intereses + $otros) -  $du_anterior) - (($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior),1) }}</td>
                <td>menos</td>
                <td>TOTAL</td>
                <td>{{ round(2*$utilidad_neta*0.1, 1) }}</td>
                <td>=</td>
                <td>{{ round($utilidad_dist,1) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
<div>
    <table class="linebordercenter" width ="100%">
        <thead>
            <tr><th colspan="15" align='center'>PASO 2: Se multiplica el N° de Acciones de cada me s por los meses que cada accion ha trabajado. Se obtiene las ACCIONES-MES y su total</th><th>{{ $acciones_mes }}</th></tr>
        </thead>
        <tbody>
            <tr><td colspan="2"></td><td colspan="12" align='center'>{{ $anio }}</td><td align='center'>{{ $anio_actual }}</td><td></td></tr>
            <tr>
                <td align='center' colspan="2">Meses</td>
                <td>E</td><td>F</td><td>M</td><td>A</td><td>M</td><td>J</td><td>J</td><td>A</td><td>S</td><td>O</td><td>N</td><td>D</td><td>E</td>
                <td align='center'>TOTAL</td>
            </tr>
            <tr>
                <td colspan="2">Total mensual de Acc.</td>
                <?php
                $total_acc_mensual  = 0;
                $ind = 0;
                
                for($i=1; $i<=12; $i++){
                    if((($ind<count($acciones_mensual))?$acciones_mensual[$ind]->mes: "") == "".$i){
                        if($ind == 0){
                            echo('<td align="center">'.($acciones_mensual[$ind]->cantidad_mes + $numero_acciones_hasta_enero[0]->cantidad_total)."</td>");
                        }else{
                            echo('<td align="center">'.$acciones_mensual[$ind]->cantidad_mes."</td>");
                        }
                        
                        $total_acc_mensual += $acciones_mensual[$ind]->cantidad_mes;
                        $ind ++;
                    }else{
                        echo('<td align="center">-</td>');
                    }
                }
                ?>
                <td>-</td>
                <td>{{ ($total_acc_mensual + $numero_acciones_hasta_enero[0]->cantidad_total) }}</td>
            </tr>
            <tr>
                <td colspan="2" align='center'>Meses "trabajados"</td>
                <?php
                for($mes=12; $mes>=1;$mes--){
                    echo("<td align='center'>".$mes."</td>");
                }
                ?>
                <td align='center'>-</td><td align='center'>---</td>
            </tr>
            <tr>
                <td colspan="2" align='center'>Acciones-mes</td>
                <?php
                $j=12;
                $indice=0;
                $sumatotal_acc_mes = 0;
                
                for($i=1; $i<=12; $i++){
                    if((($indice<count($acciones_mensual))?$acciones_mensual[$indice]->mes:"") == $i){
                        if($indice == 0){
                            $sumatotal_acc_mes += ($numero_acciones_hasta_enero[0]->cantidad_total + $acciones_mensual[$indice]->cantidad_mes) * $j;
                            echo('<td align="center">'.round(( $numero_acciones_hasta_enero[0]->cantidad_total + $acciones_mensual[$indice]->cantidad_mes) * $j, 1)."</td>");
                        }else{
                            $sumatotal_acc_mes += $acciones_mensual[$indice]->cantidad_mes * $j;
                            echo('<td align="center">'.round($acciones_mensual[$indice]->cantidad_mes * $j, 1)."</td>");
                        }
                        
                        $j--;
                        $indice++;
                    }else{
                        echo('<td align="center">-</td>');
                    }
                }
                
                ?>
               <td align="center">-</td><td align="center">{{ $sumatotal_acc_mes }}</td>
            </tr>

            {{-- <tr><td colspan="17"></td></tr> --}}
        </tbody>
    </table>
</div>

<div>
    <table class="linebordercenter" width ="100%">
        <thead>
            <tr>
                <th align='center'>PASO 3:</th>
                <th colspan="2" align='center'>Se divide la utilidad Distribuible: </th>
                <th colspan="2" align='center'>{{ $utilidad_dist }}</th>
                <th colspan="2" align='center'>entre el total de Acciones-Mes: </th>
                <th colspan="2" align='center'>{{ $sumatotal_acc_mes }}</th>
                <th colspan="5" align='center'>El resultado es la UTILIDAD DE UNA ACCION EN UN MES: </th>
                <th>{{ round((($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0), 1) }}</th>
            </tr>
        </thead>
    </table>
</div>

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
                        $factores_mes[$f] = round(($i * $factor),4);
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
            <tr><th colspan="17" align="center">PASO 6: Se sumasn estas utilidades mensuales y se obtiene  la UTILIDAD TOTAL del socio en el año (última columna de la derecha).</th></tr>
            <tr><th rowspan="2" colspan="1" align="center">N°</th><th rowspan="2" colspan="2" align="center">SOCIOS</th><th colspan="12" align='center'>{{ $anio }}</th><th align='center'>{{ $anio +1 }}</th><th rowspan="2" align='center'>TOTAL</th></tr>
            <tr>
                <th align='center'>E</th align='center'><th align='center'>F</th><th align='center'>M</th><th align='center'>A</th><th align='center'>M</th><th align='center'>J</th><th align='center'>J</th><th align='center'>A</th><th align='center'>S</th><th align='center'>O</th><th align='center'>N</th><th align='center'>D</th><th align='center'>E</th>
            </tr>
        </thead>
        <tbody>
            <?php
            echo($distrib_util);
            ?>
        </tbody>
        <tfoot class="linebordercenter">
            <tr>
                <th rowspan="2" colspan="2">TOTAL</th>
                <th>Acciones</th>
                <?php
                    $total_acc_mensual  = 0;
                    $ind = 0;
                    for($i=1; $i<=12; $i++){
                        if((($ind<count($acciones_mensual))?$acciones_mensual[$ind]->mes: "") == "".$i){
                            if($ind == 0){
                                
                                echo("<th align='center'>".($acciones_mensual[$ind]->cantidad_mes + $numero_acciones_hasta_enero[0]->cantidad_total)."</th>");
                                $total_acc_mensual += ($acciones_mensual[$ind]->cantidad_mes +  $numero_acciones_hasta_enero[0]->cantidad_total);
                            }else{
                                echo("<th align='center'>".($acciones_mensual[$ind]->cantidad_mes)."</th>");
                                $total_acc_mensual += ($acciones_mensual[$ind]->cantidad_mes);
                            }
                            
                            $ind ++;
                        }else{
                            echo("<th align='center'>-</th>");
                        }
                    }
                ?>
                <th>-</th>
                <th>{{ ($total_acc_mensual > 0?$total_acc_mensual: "-" ) }}</th>
            </tr>
            <tr>
                <th>Utilidades</th>
                    <?php
                    $j=12;
                    $indice=0;
                    $sumatotal_utilidades = 0;
                    
                    for($i=1; $i<=12; $i++){
                        if((($indice<count($acciones_mensual))?$acciones_mensual[$indice]->mes:"") == $i){
                            if($indice == 0){
                                $sumatotal_utilidades += ($acciones_mensual[$indice]->cantidad_mes+$numero_acciones_hasta_enero[0]->cantidad_total ) * $factor*$j;
                                echo("<th align='center'>".round(($acciones_mensual[$indice]->cantidad_mes + $numero_acciones_hasta_enero[0]->cantidad_total) * $factor *$j, 1)."</th>");
                            }else{
                                $sumatotal_utilidades += $acciones_mensual[$indice]->cantidad_mes * $factor*$j;
                            echo("<th align='center'>".round($acciones_mensual[$indice]->cantidad_mes * $factor *$j, 1)."</th>");
                            }
                            

                            $j--;
                            $indice++;
                        }else{
                            echo("<th align='center'>0</th>");
                        }
                    }
                    
                    ?>
                <th>-</th><th>{{ round($sumatotal_utilidades, 1) }}</th>

            </tr>
            <tr>
                <th colspan="17" align="center"> PASO 7: Se efectúa la distribución y se decide que parte de las utilidades se capitaliza y que parte se entrega.
                    (FUNDERPERU recomienda capitalizar el mayor monto posible). Se efectua todo y se consigna en los libros de Actas y de Caja. !FELICITACION
                </th>
            </tr>
        </tfoot>
    </table>
</div>
</body>
</html>
