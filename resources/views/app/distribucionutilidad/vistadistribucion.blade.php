<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\DistribucionUtilidades;
use App\Persona;
?>

<style>
	.tablesimple tr th, td {
		text-align : center;
		border: 0.9px solid #b4bdc1;
		font-size: 13px;
		padding: 2px;
	}
	.radiobTleft{
		border-top-left-radius:10%;
	}
	.radiobTright{
		border-top-right-radius:10%;
	}
	.radiobBleft{
		border-bottom-left-radius:10%;
	}
	.radiobBright{
		border-bottom-right-radius:10%;
	}

	.borderond {
		border-radius: 6px;
	}
	.tablesimple thead {
	color: #000000;
	font-weight:bold;
	background-color: #f2f6f7;
	border: 0.9px solid #b4bdc1;
	}
	.tablesimple th {
	color: #000000;
	font-weight:bold;
	background-color: #f2f6f7;
	border: 0.9px solid #b4bdc1;
	}
	.textleft{
		text-align : left !important;
		padding: 0px 0px 0px 20px;
	}
	.num-acciones{
		background-color: #ceeddd;
	}
	
	
	
</style>

<div class="form-row">
<?php
	$id_temp = 0;
	$persona = null;
	$contador=0;
	$mes = 1;
	$total_acciones_persona = 0;
	$total_utilidades_persona = 0.0;
	$suma_total_utilidades = 0.0;
	$sum_totales_mes = array();
	$paso6 = "";
	$paso6_utilidades = "";
	$tbody_paso2 = "";
	$tbody_paso4 = "";
	$$tbody_paso4_factores = "";
	$factores_mes=array();
	$sum_utilidades_mes=array();

	for($i=1; $i<=12; $i++){
		$sum_totales_mes[$i]=0;
		$sum_utilidades_mes[$i]=0;
		
	}
	//PASO 6 **********************************************************************************************************************************************************************************
	foreach ($lista_num_acciones_paso6 as $key => $value) {
		if($id_temp !=  $value->persona_id){
			$id_temp = $value->persona_id;
			$persona = Persona::find($value->persona_id);
			$paso6 .= "<tr><td rowspan='2'>".(++ $contador)."</td><td class='textleft' rowspan='2' colspan='2'><p>".$persona->apellidos.' '.$persona->nombres.' -'.$persona->id."</p></td>";//' -'.$persona->id.
		}
		if($mes == 1){
			if($value->mes == 1){
				$value->cantidad += $lista_num_enero_paso6[$value->persona_id] != null?$lista_num_enero_paso6[$value->persona_id]:0;
			}
		}
		for($j=$mes; $j<$value->mes; $j ++){
			if($mes == 1){
				$sum_totales_mes[$mes] += $lista_num_enero_paso6[$value->persona_id] != null?$lista_num_enero_paso6[$value->persona_id]:0;
				$total_acciones_persona += $lista_num_enero_paso6[$value->persona_id] != null?$lista_num_enero_paso6[$value->persona_id]:0;
				$utilidad = ($lista_num_enero_paso6[$value->persona_id] != null?$lista_num_enero_paso6[$value->persona_id]:0) * round($factores_pormes[1],4);
				$sum_utilidades_mes[$value->mes] += $utilidad;
				$total_utilidades_persona += $utilidad;
				$suma_total_utilidades += $utilidad;

				$paso6_utilidades .= "<td>".round($utilidad,2)."</td>";

				$paso6 .="<td class='num-acciones' align='center'>".($lista_num_enero_paso6[$value->persona_id] != null?$lista_num_enero_paso6[$value->persona_id]:"-")."</td>";
			}else{
				$paso6 .="<td class='num-acciones' align='center'>-</td>";
				$paso6_utilidades .= "<td>0</td>";
			}
			$mes++;
		}
		// $mes = $value->mes;
		$paso6 .="<td class='num-acciones' align='center'>".($value->cantidad>0?$value->cantidad:"-")."</td>";

		$utilidad = $value->cantidad * round($factores_pormes[$value->mes],4);
		$sum_utilidades_mes[$value->mes] += $utilidad;
		$total_utilidades_persona += $utilidad;
		$suma_total_utilidades += $utilidad;
		$paso6_utilidades .= "<td> ".round($utilidad,1)." </td>";
		// $paso6_utilidades .=$mes ==1? "<td>tra- ".round($utilidad,1)." </td>":"<td> ".round($utilidad,1)." </td>" ;
		// $paso6_utilidades .= "<td> ".round($utilidad,2).":".$value->cantidad ."x". round($factores_pormes[$value->mes],4)." </td>";
		$total_acciones_persona += $value->cantidad>0?$value->cantidad: 0;
		$sum_totales_mes[$value->mes] +=  $value->cantidad>0?$value->cantidad: 0;
		if($mes == 12){
			$mes = 1;
			$paso6 .="<td>0</td><td>".$total_acciones_persona."</td></tr>";
			$total_acciones_persona = 0;
			$paso6 .= "<tr>".$paso6_utilidades;
			$paso6_utilidades = "";
			$paso6 .= "<td>0</td><td class='num-acciones'>".round($total_utilidades_persona,1)."</td></tr>";
			$total_utilidades_persona = 0.0;
		}else{
			$mes ++;
		}
	}

	//PASO 2 ************************************************************************************************************
	$tbody_paso2 .= '<tr><td colspan="2">Total mensual de Acc.</td>';
	for($m=1; $m<=12;$m++){
		$tbody_paso2 .= "<td align='center'>".$suma_acciones_porMes[$m]."</td>";
	}
	$tbody_paso2 .= '<td align="center">0</td><td>'.$suma_total_acciones.'</td></tr>';
	$tbody_paso2.= '<tr><td colspan="2" align="center">Meses "trabajados"</td>';
	for($mes=12; $mes>=1;$mes--){
		$tbody_paso2.='<td align="center">'.$mes.'</td>';
	}
	$tbody_paso2.= '<td align="center">0</td><td align="center">---</td></tr>';
	$tbody_paso2.= '<tr><td colspan="2" align="center">Acciones-mes</td>';
	for($mes=12; $mes>=1;$mes--){
		$sum_mult = $suma_acciones_porMes[12 - ($mes-1)] * $mes;
		$tbody_paso2.= '<td align="center">'.$sum_mult.'</td>';
	}
	$tbody_paso2 .= '<td align="center">-</td><td align="center">'.$suma_total_acciones_multiplicadas.'</td><tr>';
	
	//PASO 4 ************************************************************************************************************
	$tbody_paso4 .= '<tr><td colspan="2"></td><td colspan="12">'.$anio.'</td><td>'.$anio_actual.'</td><td rowspan="2">TOTAL</td></tr>';
	$tbody_paso4 .= '<tr><td colspan="2">Meses</td><td>E</td><td>F</td><td>M</td><td>A</td><td>M</td><td>J</td><td>J</td><td>A</td><td>S</td><td>O</td><td>N</td><td>D</td><td>E</td></tr>';
	$tbody_paso4 .= '<tr><td rowspan="2">Utilidad de una acción</td><td>En 1 mes</td><td colspan="14">'.round($factor, 4).'</td></tr>';
	$tbody_paso4 .= '<tr><td >En el año</td>';
	for ($i=1; $i <=12 ; $i++) { 
		$tbody_paso4 .= '<td align="center">'.round($factores_pormes[$i],4)."</td>";
	}
	$tbody_paso4 .= '<td>-</td><td>...</td></tr>';
?>
	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
				<tr>
					<th class="radiobTleft" rowspan="8" colspan="1">PASO 1: Se calcula las utilidades</th>
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
					<td  colspan="2" rowspan="1">U. B. Acumulada</td>
					<td rowspan="5"></td>
					<td  colspan="1" rowspan="1">G. Adm. Acum.</td>
					<td  colspan="1" rowspan="1">{{ round($gastadmacumulado, 1) }}</td>
					<td rowspan="5"></td>
					<td rowspan="5"></td>
					<td rowspan="5"></td>
					<td  colspan="1" rowspan="2">F Social 10%</td>
					<td  colspan="1" rowspan="2">{{ round($utilidad_neta*0.1, 1) }}</td>
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
					<td>{{ round($du_anterior, 1)==0?"-": round($du_anterior, 1) }}</td>
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

	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
				<tr><th colspan="15">PASO 2: Se multiplica el N° de Acciones de cada mes por los meses que cada acción ha trabajado. Se obtiene las ACCIONES-MES y su total</th><th>{{ $suma_accMes_multiplicadas_porMes }}</th></tr>
				<tr><th colspan="2"></th><th colspan="12">{{ $anio }}</th><th>{{ $anio_actual }}</th><th rowspan="2">TOTAL</th></tr>
				<tr>
					<th colspan="2">Meses</th>
					<th>E</th><th>F</th><th>M</th><th>A</th><th>M</th><th>J</th><th>J</th><th>A</th><th>S</th><th>O</th><th>N</th><th>D</th><th>E</th>
				</tr>
			</thead>
			<tbody>
				<?php
				echo($tbody_paso2);
				?>
			</tbody>
		</table>
	</div>


	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
				<tr>
					<th align='center'>PASO 3:</th>
					<th colspan="2" align="center">Se divide la utilidad Distribuible: </th>
					<th colspan="2" align="center">{{ $utilidad_dist }}</th>
					<th colspan="2" align="center">entre el total de Acciones-Mes: </th>
					<th colspan="2" align="center">{{ $suma_total_acciones_multiplicadas }}</th>
					<th colspan="5" align="center">El resultado es la UTILIDAD DE UNA ACCION EN UN MES: </th>
					<th align="center">{{ round($factor, 4) }}</th>
				</tr>
			</thead>
		</table>
	</div>

	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
				<tr>
					<th colspan="2">PASO 4: </th>
					<th colspan="3">Se multiplica esta utilidad.</th>
					<th colspan="2">{{ round($factor, 4) }}</th>
					<th colspan="9">por el N° de meses que ha trabajado cada accion. Los resultados son las diferentes utilidades de una accion en un año.</th>
				</tr>
			</thead>
			<tbody>
				<?php
				echo($tbody_paso4);
				?>
			
			</tbody>
			<tfoot>

			</tfoot>
		</table>
	</div>
	
	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
				<tr>
					<th>PASO 5:  Se multiplica cada una de estas utilidades anuales por el número de acciones de cada socio en el mes respectivo.  Los resultados son las utilidades del socio en cada uno de los  meses.</th>
				</tr>
			</thead>
		</table>
	</div>

	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
				<tr ><th colspan="17" >PASO 6: Se suman estas utilidades mensuales y se obtiene  la UTILIDAD TOTAL del socio en el año (última columna de la derecha).</th></tr>
				<tr ><th rowspan="2" >N°</th><th rowspan="2" colspan="2">SOCIOS</th><th colspan="12">{{ $anio }}</th><th>{{ $anio +1 }}</th><th rowspan="2">TOTAL</th></tr>
				<tr >
					<th>E</th><th>F</th><th>M</th><th>A</th><th>M</th><th>J</th><th>J</th><th>A</th><th>S</th><th>O</th><th>N</th><th>D</th><th>E</th>
				</tr>
			</thead>
			<tbody>
				<?php
				echo($paso6);
				?>

			</tbody>
			<tfoot>
				<tr>
					<th rowspan="2" colspan="2" >TOTAL</th>
					<th>Acciones</th>
					<?php
						
						for($i=1; $i<=12; $i++){
							echo("<th align='center'>".$suma_acciones_porMes[$i]."</th>");
						}
						
					?>
					<th>0</th>
					<th>{{ $suma_total_acciones }}</th>
				</tr>
				<tr>
					<th >Utilidades</th>
						<?php
						$sum_utilidades = 0.0;
						for($j=1;$j<=12; $j++){
							$sum_utilidades += $sum_utilidades_mes[$j];
							echo("<th align='center'>".round($sum_utilidades_mes[$j],2)."</th>");
						}
						 echo("<th>0</th><th>".round($suma_total_utilidades, 2)." - ".round($sum_utilidades,2)."</th>");
						?>
				</tr>
				<tr>
					<th colspan="17"> PASO 7: Se efectúa la distribución y se decide que parte de las utilidades se capitaliza y que parte se entrega.
						(FUNDERPERU recomienda capitalizar el mayor monto posible). Se efectua todo y se consigna en los libros de Actas y de Caja. !FELICITACION
					</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
@if($reporte !=1)
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
            {!! Form::button('<i class="fa fa-lg"></i> Imprimir PDF', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnreporte'.$entidad, 'onclick' => 'imprimirpdf(\''.URL::route($ruta["reportedistribucionPDF"], array($distribucion->id)).'\')')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Ok', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('1020');
	}); 
    function imprimirpdf(url_pdf) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}
</script>
@endif