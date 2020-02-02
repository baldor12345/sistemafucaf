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
	
	
	
</style>
<div class="form-row">
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
				{{-- <tr>
					<td></td>
					<td>Utilidad Bruta DU ACTUAL</td>
					<td>{{ ($intereses + $otros) -  $du_anterior }}</td>
					<td>menos</td>
					<td>Gast. DU ACTUAL</td>
					<td>{{ ($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior }}</td>
					<td>=</td>
					<td>{{ (($intereses + $otros) -  $du_anterior) - (($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior ) }}</td>
					<td>menos</td>
					<td>TOTAL</td>
					<td>{{ 2*$utilidad_neta*0.1 }}</td>
					<td>=</td>
					<td>{{ $utilidad_dist }}</td>
				</tr> --}}
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
				<tr><th colspan="15">PASO 2: Se multiplica el N° de Acciones de cada mes por los meses que cada acción ha trabajado. Se obtiene las ACCIONES-MES y su total</th><th>{{ $acciones_mes }}</th></tr>
				<tr><th colspan="2"></th><th colspan="12">{{ $anio }}</th><th>{{ $anio_actual }}</th><th rowspan="2">TOTAL</th></tr>
				<tr>
					<th colspan="2">Meses</th>
					<th>E</th><th>F</th><th>M</th><th>A</th><th>M</th><th>J</th><th>J</th><th>A</th><th>S</th><th>O</th><th>N</th><th>D</th><th>E</th>
					
				</tr>
			</thead>
			<tbody>
				
				<tr>
					<td colspan="2">Total mensual de Acc.</td>
					<?php
					$suma_total_utilidades = 0;
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
					<td align="center">0</td>
					{{-- <td align="center">{{ $total_acc_mensual }}</td> --}}
					<td>{{ ($total_acc_mensual + $numero_acciones_hasta_enero[0]->cantidad_total) }}</td>
				</tr>
				<tr>
					<td colspan="2" align="center">Meses "trabajados"</td>
					<?php
					for($mes=12; $mes>=1;$mes--){
						echo('<td align="center">'.$mes.'</td>');
					}
					?>
					<td align="center">0</td><td align="center">---</td>
				</tr>
				<tr>
					<td colspan="2" align="center">Acciones-mes</td>
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
					<th colspan="2" align="center">{{ $sumatotal_acc_mes }}</th>
					<th colspan="5" align="center">El resultado es la UTILIDAD DE UNA ACCION EN UN MES: </th>
					<th align="center">{{ round((($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0), 1) }}</th>
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
					<th colspan="2">{{ round(($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0, 1) }}</th>
					<th colspan="9">por el N° de meses que ha trabajado cada accion. Los resultados son las diferentes utilidades de una accion en un año.</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="2"></td><td colspan="12">{{ $anio }}</td><td>{{ $anio_actual }}</td><td rowspan="2">TOTAL</td></tr>
				<tr>
					<td colspan="2">Meses</td>
					<td>E</td><td>F</td><td>M</td><td>A</td><td>M</td><td>J</td><td>J</td><td>A</td><td>S</td><td>O</td><td>N</td><td>D</td><td>E</td>
					
				</tr>
				<tr>
					<td rowspan="2">Utilidad de una acción</td>
					<td>En 1 mes</td>
					<td colspan="14">{{ round(($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0, 1) }}</td>
				</tr>
				<tr>
					<td >En el año</td>
					<?php
			
						$factores_mes=array();
					$f=0;
					$factor = ($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0;
						for ($i=12; $i >0 ; $i--) { 
							echo('<td align="center">'.round($i * $factor,4)."</td>");
							$factores_mes[$f] = round($i * $factor,4);
							$f++;
						}
					?>
					<td>-</td>
					<td>...</td>
				</tr>
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
				$socios = Persona::where('tipo','=','SC')->orwhere('tipo','=','S')->get();
			
				for($i=0; $i< count($socios); $i++){
					
					$listaAcciones = DistribucionUtilidades::list_por_persona($socios[$i]->id, $anio)->get();
					$num_accionesenero = DistribucionUtilidades::list_enero($socios[$i]->id, ($anio-1))->get();
					
					$utilidades = array();
					if((count($listaAcciones) + count($num_accionesenero))>0){
						echo('<tr><td rowspan="2">'.($i+1).'</td><td rowspan="2" colspan="2">'.$socios[$i]->nombres.' '.$socios[$i]->apellidos.'</td>');
						$l=0;
						$sumtotalAcciones =0;
						for($j=1; $j<=12; $j++){
							$numaccciones = 0;
							if($j == 1){
								$numaccciones = count($num_accionesenero)>0?$num_accionesenero[0]->cantidad_total:0;
							}
							if(((($l)< (count($listaAcciones)))?$listaAcciones[$l]->mes:"") == $j){
								$numaccciones += (count($listaAcciones)>0)?$listaAcciones[$l]->cantidad_mes:0;
							}

							if($numaccciones>0){
								$utilidades[$j-1] = $factores_mes[$j-1] * $numaccciones;
								$sumtotalAcciones += $numaccciones;
								$l++;
								echo("<td align='center'>".($numaccciones>0?$numaccciones:"-")."</td>");
							}else{
								echo("<td align='center'>-</td>");
								$utilidades[$j-1] = 0;
							}


						}
						echo('<td>0</td><td >'.round($sumtotalAcciones,1).'</td><tr>');
							$sumtotal_util = 0;
						for($j=1; $j<=12; $j++){
							echo('<td>'.(round($utilidades[$j-1],1)==0?"-":round($utilidades[$j-1],1)).'</td>');
							$sumtotal_util += round($utilidades[$j-1], 2);
						}
						$suma_total_utilidades += $sumtotal_util;
						echo('<td >0</td><td >'.round($sumtotal_util,1).'</td>');
						echo('</tr>');
					}
				}
				?>

			</tbody>
			<tfoot>
				<tr>
					<th rowspan="2" colspan="2" >TOTAL</th>
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
								echo("<th align='center'>0</th>");
							}
						}
					?>
					<th>0</th>
					<th>{{ $total_acc_mensual }}</th>
				</tr>
				<tr>
					<th >Utilidades</th>
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
					<!-- <th>0</th><th>{{ round($sumatotal_utilidades, 1) }}</th> -->
					<th>0</th><th>{{ round($suma_total_utilidades, 1) }}</th>

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