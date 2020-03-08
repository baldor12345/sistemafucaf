

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
        .textleft{
            text-align : left !important;
            padding: 0px 0px 0px 20px;
        }
        .num-acciones{
            background-color: #ceeddd;
        }
        .fsocial {
            color: #000000;
            font-weight:bold;
            background-color: #d5f0d6;
            
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
<?php
	$id_temp = 0;
	$persona = null;
	$contador=0;
	$mes = 1;
	$total_acciones_persona = 0;
	$total_utilidades_persona = 0.0;
	$suma_total_utilidades = 0.0;
	// $sum_totales_mes = array();
	$paso6 = "";
	$paso6_utilidades = "";
	$tbody_paso2 = "";
	$tbody_paso4 = "";
	$$tbody_paso4_factores = "";
	$factores_mes=array();
	$sum_utilidades_mes=array();
	$total_distr = 0;
?>
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
                <td  colspan="1" rowspan="1">{{ (round($gastadmacumulado, 1) == 0?"-":round($gastadmacumulado, 1)) }}</td>
                <td rowspan="5"></td>
                <td rowspan="5"></td>
                <td rowspan="5"></td>
                <td  colspan="1" rowspan="2">F Social 10%</td>
                <td  colspan="1" rowspan="2">{{ (round($utilidad_neta*0.1, 1) == 0?"-":(round($utilidad_neta*0.1, 1))) }}</td>
                <td rowspan="5"></td>
                <td rowspan="5"></td>
            </tr>
            <tr>
                <td>Intereses</td>
                <td>{{ (round($intereses, 1) == 0?"-":round($intereses, 1)) }}</td>
                <td  colspan="1" rowspan="1">I. Pag. Acum.</td>
                <td  colspan="1" rowspan="1">{{ (round($int_pag_acum, 1) == 0?"-":round($int_pag_acum, 1)) }}</td>
            </tr>
            <tr>
                <td>Otros</td>
                <td>{{ (round($otros, 1) == 0?"-":round($otros, 1)) }}</td>
                <td  colspan="1" rowspan="1">Otros Acum.</td>
                <td  colspan="1" rowspan="1">{{ (round($otros_acumulados, 1) == 0?"-":round($otros_acumulados, 1)) }}</td>
                <td  colspan="1" rowspan="3">R Legal 10%</td>
                <td  colspan="1" rowspan="3">{{ (round($utilidad_neta*0.1, 1) == 0?"-":round($utilidad_neta*0.1, 1)) }}</td>
            </tr>

            <tr>
                <td>Total acumulado</td>
                <td>{{ (round($intereses + $otros, 1) == 0?"-":round($intereses + $otros, 1)) }}</td>
                <td  rowspan="1" colspan="1">TOTAL ACUMULADO</td>
                <td  rowspan="1" colspan="1">{{ (round($gastadmacumulado + $int_pag_acum + $otros_acumulados, 1) == 0?"-":round($gastadmacumulado + $int_pag_acum + $otros_acumulados, 1) ) }}</td>
            </tr>
           
            <tr>
                <td>U.B DU Anterior</td>
                <td>{{ round($du_anterior, 1)==0?"-": round($du_anterior, 1)}}</td>
                <td  rowspan="1" colspan="1">Gast. DU Anterior</td>
                <td  rowspan="1" colspan="1">{{ round($gast_du_anterior, 1)==0?"-": round($gast_du_anterior, 1) }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td>Utilidad Bruta DU ACTUAL</td>
                <td>{{ (round(($intereses + $otros) -  $du_anterior, 1)==0?"-":round(($intereses + $otros) -  $du_anterior, 1)) }}</td>
                <td>menos</td>
                <td>Gast. DU ACTUAL</td>
                <td>{{ round(($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior, 1) }}</td>
                <td>=</td>
                <td>{{ round((($intereses + $otros) -  $du_anterior) - (($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior), 1) }}</td>
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
				<tr><td colspan="2"></td><td colspan="12">{{ $anio }}</td><td>{{ $anio_actual+1 }}</td><td></td></tr>
				<tr>
					<td colspan="2">Meses</td>
					<td>E</td><td>F</td><td>M</td><td>A</td><td>M</td><td>J</td><td>J</td><td>A</td><td>S</td><td>O</td><td>N</td><td>D</td><td>E</td>
					<td>TOTAL</td>
				</tr>
				<tr>
					<td colspan="2">Total mensual de Acc.</td>
					<?php
					for($i=1; $i<=12; $i++){
						echo('<td align="center">'.$suma_acciones_porMes[$i]."</td>");
					}
					?>
					<td>-</td>
					<td>{{ ($suma_total_acciones) }}</td>
				</tr>
				<tr>
					<td colspan="2">Meses "trabajados"</td>
					<?php
					for($m=12; $m>=1;$m--){
						echo('<td align="center">'.$m."</td>");
					}
					?>
					<td>-</td><td>---</td>
				</tr>
				<tr>
					<td colspan="2">Acciones-mes</td>
					<?php
					$j=12;
					$indice=0;
					$sumatotal_acc_mes = 0;
					
					for($i=1; $i<=12; $i++){
						echo('<td align="center">'.$sum_acc_mes_multiplicadas[$i]."</td>");
					}
					
					?>
					<td>-</td><td>{{ $suma_total_acciones_multiplicadas }}</td>
				</tr>
	
				<tr><td colspan="17"></td></tr>
			</tbody>
    </table>
</div>


<div>
    <table class="linebordercenter" width ="100%">
        <thead>
            <tr>
                <th align='center'>PASO 3:</th>
                <th colspan="2" align='center'>Se divide la utilidad Distribuible: </th>
                <th colspan="2" align='center'>{{ round($utilidad_dist, 1) }}</th>
                <th colspan="2" align='center'>entre el total de Acciones-Mes: </th>
                <th colspan="2" align='center'>{{ $suma_total_acciones_multiplicadas }}</th>
                <th colspan="5" align='center'>El resultado es la UTILIDAD DE UNA ACCION EN UN MES: </th>
                <th>{{ round($factor, 4) }}</th>
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
                <th colspan="2" align="center">{{ round($factor, 4) }}</th>
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
                <td align='center' colspan="14">{{ round($factor, 1) }}</td>
            </tr>
            <tr>
                <td>En el año</td>
                <?php
                    for ($i=1; $i <=12 ; $i++) { 
                        echo('<td align="center">'.round($factores_pormes[$i],4)."</td>");
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
            <tr><th colspan="18" >PASO 6: Se suman estas utilidades mensuales y se obtiene  la UTILIDAD TOTAL del socio en el año (última columna de la derecha).</th></tr>
            <tr><th rowspan="2">N°</th><th rowspan="2" colspan="2">SOCIOS</th><th colspan="12" >{{ $anio }}</th><th>{{ $anio +1 }}</th><th rowspan="2">TOTAL</th><th rowspan="1">DISTR.</th></tr>
            <tr>
                <th>E</th><th>F</th><th>M</th><th>A</th><th>M</th><th>J</th><th>J</th><th>A</th><th>S</th><th>O</th><th>N</th><th>D</th><th>E</th>
                <th>{{ $porcentaje_ditribuible."%" }}</th>
            </tr>
        </thead>
        
        <tbody class="linebordercenter">
            <?php
            
            /************************************************************************************************************** */
            
            
            foreach ($lista_num_acciones_paso6 as $key => $value) {
                if($id_temp !=  $value->persona_id){
                    
                    if($mes > 1){
                        for($j=$mes; $j<=12; $j ++){
                            $paso6_utilidades .= "<td>0.0</td>";
                            echo("<td class='num-acciones' align='center'>-</td>");
                        }
                        $mes =1;
                        echo("<td>0</td><td>".$total_acciones_persona."</td><td>-</td></tr>");
                        $total_acciones_persona = 0;
                        $distr = round(($porcentaje_ditribuible/100)*$total_utilidades_persona, 1);
                        $total_distr += $distr;
                        echo("<tr>".$paso6_utilidades);
                        $paso6_utilidades = "";
                        echo("<td>0</td><td class='num-acciones'>".round($total_utilidades_persona,1)."</td><td>".$distr."</td>");
                     
                        echo("</tr>");
                        $total_utilidades_persona = 0.0;
                    }
                    $id_temp = $value->persona_id;
                    $persona = $personas[$value->persona_id];
                    echo('<tr><td rowspan="2">'.(++ $contador).'</td><td class="textleft" rowspan="2" colspan="2"><p>'.$persona->apellidos.' '.$persona->nombres.'</p></td>');
                }
                if($mes == 1){
                    if($value->mes == 1){
                        $value->cantidad += $lista_num_enero_paso6[$value->persona_id] != null?$lista_num_enero_paso6[$value->persona_id]:0;
                    }
                }
                for($j=$mes; $j<$value->mes; $j ++){
                    if($mes == 1){
                        $total_acciones_persona += $lista_num_enero_paso6[$value->persona_id] != null?$lista_num_enero_paso6[$value->persona_id]:0;
                        $utilidad = ($lista_num_enero_paso6[$value->persona_id] != null?$lista_num_enero_paso6[$value->persona_id]:0) * round($factores_pormes[1],4);
                        $sum_utilidades_mes[$value->mes] += $utilidad;
                        $total_utilidades_persona += $utilidad;
                        $suma_total_utilidades += $utilidad;
                        $paso6_utilidades .= "<td>".round($utilidad,2)."</td>";
                        echo("<td class='num-acciones' align='center'>".($lista_num_enero_paso6[$value->persona_id])."</td>");
                    }else{
                        echo("<td class='num-acciones' align='center'>-</td>");
                        $paso6_utilidades .= "<td>0</td>";
                    }
                }
                $mes = $value->mes;
                echo("<td class='num-acciones' align='center'>".($value->cantidad>0?$value->cantidad:"-")."</td>");
        
                $utilidad = $value->cantidad * round($factores_pormes[$value->mes],4);
                $sum_utilidades_mes[$value->mes] += $utilidad;
                $total_utilidades_persona += $utilidad;
                $suma_total_utilidades += $utilidad;
                $paso6_utilidades .= "<td> ".round($utilidad,1)." </td>";
                $total_acciones_persona += $value->cantidad>0?$value->cantidad: 0;
                if($mes == 12){
                    $mes = 1;
                    echo("<td>0</td><td>".$total_acciones_persona."</td><td>-</td></tr>");
                    $total_acciones_persona = 0;
                    $distr = round(($porcentaje_ditribuible/100)*$total_utilidades_persona, 1);
                    $total_distr += $distr;
                    echo("<tr>".$paso6_utilidades);
                    $paso6_utilidades = "";
                    echo("<td>0</td><td class='num-acciones'>".round($total_utilidades_persona,1)."</td><td>".$distr."</td>");
                    
                    echo("</tr>");
                    $total_utilidades_persona = 0.0;
                }else{
                    $mes ++;
                }
            }
            ?>
                <tr>
					<td class="fsocial">{{++ $contador}}</td>
					<td class=" fsocial" colspan="2">
						F. SOCIAL
					</td>
					<td class="fsocial" colspan="13"></td>
					<td class="fsocial">{{round($utilidad_neta*0.1,1)}}</td>
					<td class="fsocial">{{round(($porcentaje_ditribuible/100)*$utilidad_neta*0.1,1)}}</td>
				
				</tr>
				<tr>
					<td class="fsocial">{{++ $contador}}</td>
					<td class=" fsocial" colspan="2">
						R. LEGAL
					</td>
					<td class="fsocial" colspan="13"></td>
					<td class="fsocial">{{round($utilidad_neta*0.1,1)}}</td>
					<td class="fsocial">{{round(($porcentaje_ditribuible/100)*$utilidad_neta*0.1,1)}}</td>
					
				</tr>
        </tbody>
    
        <tfoot class="linebordercenter">
            <tr>
            <th rowspan="2" colspan="2">TOTAL</th>
                <th>Acciones</th>
                <?php
                    for($i=1; $i<=12; $i++){
                        echo("<th align='center'>".($suma_acciones_porMes[$i])."</th>");
                    }
                ?>
                <th>0</th>
                <th>{{ $suma_total_acciones }}</th>
                <th>-</th>
            </tr>
            <tr>
                <th>Utilidades</th>
                    <?php
                    for($i=1; $i<=12; $i++){
                        echo("<th align='center'>".round($sum_utilidades_mes[$i], 1)."</th>");
                    }
                    $total_distr += (($porcentaje_ditribuible/100)*$utilidad_neta*0.1)*2;
                    ?>
                <th>0</th><th>{{ round($suma_total_utilidades, 2) }}</th>
                <th>{{ round($total_distr, 2) }}</th>
            </tr>
            <tr>
                <th colspan="18"> PASO 7: Se efectúa la distribución y se decide que parte de las utilidades se capitaliza y que parte se entrega.
                    (FUNDERPERU recomienda capitalizar el mayor monto posible). Se efectua todo y se consigna en los libros de Actas y de Caja. !FELICITACION
                </th>
            </tr>
        </tfoot>
    </table>
</div>


</body>
</html>



