<?php
//namespace Resources\views\app\distribucionutilidad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use App\DistribucionUtilidades;
use App\Persona;
?>
@if($existe === 0)
<div id="divMensajeError{!! $entidad !!}"></div>

{!! Form::model(null, $formData) !!}
{!! Form::hidden('anio', $anio, array('id' => 'anio')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('utilidad_distr', $utilidad_dist, array('id' => 'utilidad_distr')) !!}
{!! Form::hidden('intereses', $intereses, array('id' => 'intereses')) !!}
{!! Form::hidden('otros', $otros, array('id' => 'otros')) !!}
{!! Form::hidden('ub_duactual', (($intereses + $otros) -  $du_anterior), array('id' => 'ub_duactual')) !!}
{!! Form::hidden('gast_ad_acum', $gastadmacumulado, array('id' => 'gast_ad_acum')) !!}
{!! Form::hidden('int_pag_acum', $int_pag_acum, array('id' => 'int_pag_acum')) !!}
{!! Form::hidden('otros_acum', $otros_acumulados, array('id' => 'otros_acum')) !!}
{!! Form::hidden('gast_duactual', (($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior), array('id' => 'gast_duactual')) !!}
{!! Form::hidden('fsocial', round(($porcentaje_ditribuible/100)*$utilidad_neta*0.1,1), array('id' => 'fsocial')) !!}
{!! Form::hidden('rlegal', round(($porcentaje_ditribuible/100)*$utilidad_neta*0.1,1), array('id' => 'rlegal')) !!}
{!! Form::hidden('porcentaje_dist', $porcentaje_ditribuible, array('id' => 'porcentaje_dist')) !!}
{!! Form::hidden('porcentaje_dist_faltante', $porcentaje_ditr_faltante, array('id' => 'porcentaje_dist_faltante')) !!}
<style>
	.tablesimple tr th, td {
		text-align : center;
		border: 0.9px solid #b4bdc1;
		font-size: 13px;
		padding: 2px;
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
	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
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
					<td  colspan="2" rowspan="1">U. B. Acumulada</td>
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

	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
			<thead>
				<tr><th colspan="15">PASO 2: Se multiplica el N° de Acciones de cada me s por los meses que cada accion ha trabajado. Se obtiene las ACCIONES-MES y su total</th><th>{{ $suma_total_acciones_multiplicadas }}</th></tr>
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

	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
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

	<div class="table-responsive card-box">
		<table width="100%" class="table-hover tablesimple">
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
				<tr><th colspan="20" >PASO 6: Se suman estas utilidades mensuales y se obtiene  la UTILIDAD TOTAL del socio en el año (última columna de la derecha).</th></tr>
				<tr><th rowspan="2">N°</th><th rowspan="2" colspan="2">SOCIOS</th><th colspan="12" >{{ $anio }}</th><th>{{ $anio +1 }}</th><th rowspan="2">TOTAL</th><th rowspan="1">DISTR.</th><th colspan="2">OPPERACION</th></tr>
				<tr>
					<th>E</th><th>F</th><th>M</th><th>A</th><th>M</th><th>J</th><th>J</th><th>A</th><th>S</th><th>O</th><th>N</th><th>D</th><th>E</th>
					<th>{{ $porcentaje_ditribuible."%" }}</th><th>REITRAR</th><th>AHORRAR</th>
				</tr>
			</thead>
			
			<tbody>
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
							echo("<td>0</td><td>".$total_acciones_persona."</td><td>-</td><td></td><td></td></tr>");
							$total_acciones_persona = 0;
							$distr = round(($porcentaje_ditribuible/100)*$total_utilidades_persona, 1);
							$total_distr += $distr;
							echo("<tr>".$paso6_utilidades);
							$paso6_utilidades = "";
							echo("<td>0</td><td class='num-acciones'>".round($total_utilidades_persona,1)."</td><td>".$distr."</td>");
							?>
							<td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-primary btn-xs btnretirar ','vr'=>'1','num'=>''.$contador ,'id' => 'btn'.$contador, 'onclick' => 'btnclieck(this)',  'persona_id' => ''.$persona->id , 'utilidad'=> ''.$distr)) !!}</td>
							<td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-light btn-xs btnahorrar','vr'=>'0', 'num'=>''.$contador , 'id' => 'btna'.$contador , 'onclick' => 'btncli(this)',  'persona_id' => ''.$persona->id , 'utilidad'=> ''.$distr)) !!}</td>

							<?php
							echo("</tr>");
							$total_utilidades_persona = 0.0;
						}
						$id_temp = $value->persona_id;
						$persona = Persona::find($value->persona_id);
						echo("<tr><td rowspan='2'>".(++ $contador)."</td><td class='textleft' rowspan='2' colspan='2'><p>".$persona->apellidos.' '.$persona->nombres.' -'.$persona->id."</p></td>");
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
						echo("<td>0</td><td>".$total_acciones_persona."</td><td>-</td><td></td><td></td></tr>");
						$total_acciones_persona = 0;
						$distr = round(($porcentaje_ditribuible/100)*$total_utilidades_persona, 1);
						$total_distr += $distr;
						echo("<tr>".$paso6_utilidades);
						$paso6_utilidades = "";
						echo("<td>0</td><td class='num-acciones'>".round($total_utilidades_persona,1)."</td><td>".$distr."</td>");
						?>
						<td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-primary btn-xs btnretirar ','vr'=>'1','num'=>''.$contador ,'id' => 'btn'.$contador, 'onclick' => 'btnclieck(this)',  'persona_id' => ''.$persona->id , 'utilidad'=> ''.$distr)) !!}</td>
						<td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-light btn-xs btnahorrar','vr'=>'0', 'num'=>''.$contador , 'id' => 'btna'.$contador , 'onclick' => 'btncli(this)',  'persona_id' => ''.$persona->id , 'utilidad'=> ''.$distr)) !!}</td>

						<?php
						echo("</tr>");
						$total_utilidades_persona = 0.0;
					}else{
						$mes ++;
					}
				}
				?>

			</tbody>
		
			<tfoot>
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
					<th colspan="2">{!! Form::button('<i class="fa fa-check fa-lg"></i> RETIRAR TODO', array('class' => 'btn btn-warning btn-xs', 'accion'=>'retirar',  'id' => 'btnrecibo', 'onclick' => 'marcartodo(this)')) !!}</th>
				</tr>
				<tr>
					<th>Utilidades</th>
						<?php
						for($i=1; $i<=12; $i++){
							echo("<th align='center'>".round($sum_utilidades_mes[$i], 1)."</th>");
						}
						
						?>
					<th>0</th><th>{{ round($suma_total_utilidades, 2) }}</th>
					<th>{{ round($total_distr, 2) }}</th>
					<th colspan="2">{!! Form::button('<i class="fa fa-check fa-lg"></i> AHORRAR TODO', array('class' => 'btn btn-success btn-xs','accion'=>'ahorrar', 'id' => 'btnrecibo', 'onclick' => 'marcartodo(this)')) !!}</th>

				</tr>
				<tr>
					<th colspan="20"> PASO 7: Se efectúa la distribución y se decide que parte de las utilidades se capitaliza y que parte se entrega.
						(FUNDERPERU recomienda capitalizar el mayor monto posible). Se efectua todo y se consigna en los libros de Actas y de Caja. !FELICITACION
					</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<div class="form-row">
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarDist', 'onclick' => 'guardar_distribucion(\''.$entidad.'\', this)')) !!}
			&nbsp;
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
</div>

{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('1200');
	}); 

	function guardar_distribucion(entidad,btn) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var data         = submitFormDistr(idformulario);
		var respuesta    = '';
		var listar       = 'NO';
		if ($(idformulario + ' :input[id = "listar"]').length) {
			var listar = $(idformulario + ' :input[id = "listar"]').val()
		};
		$(btn).button("loading...");
		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
			$(btn).removeClass('disabled');
			$(btn).removeAttr('disabled');
			$(btn).html('Guardar');
		}).always(function() {
			
			if(respuesta === 'ERROR'){
				$(btn).removeClass('disabled');
				$(btn).removeAttr('disabled');
				$(btn).html('Guardar');
			}else{
				if (respuesta === 'OK') {
					cerrarModal();
					//imprimirpdf(rutarecibo);
					
					if (listar === 'SI') {
						if(typeof entidad2 != 'undefined' && entidad2 !== ''){
							entidad = entidad2;
						}
						buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
					}        
				} else {
					bootbox.alert("<div class='alert alert-danger'><strong>¡Error!</strong> "+respuesta+"</div>", function(){ 
						$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"}); 
					});	
				}
			}
		});
	}

	function submitFormDistr(idformulario) {
		var parametros = $(idformulario).serialize();
		var i=0;
		$('.btnahorrar').each(function() {
			parametros += "&persona_id"+i+"="+$(this).attr('persona_id')+"&monto"+i+"="+$(this).attr('utilidad')+"&ahorrar"+i+"="+$(this).attr('vr');
			i++;
		});
		parametros += "&numerosocios="+i;
		var accion = $(idformulario).attr('action').toLowerCase();
		// console.log('Accion: form: '+accion+'   param: '+parametros);
		var metodo     = $(idformulario).attr('method').toLowerCase();
		// console.log('Metodo: '+metodo);
		var respuesta  = $.ajax({
			url : accion,
			type: metodo,
			data: parametros
		});
		// console.log('Respuesta: '+respuesta);
		return respuesta;
	}
	function btnclieck(btn){
		var num = $(btn).attr('num');
		if( $(btn).attr("vr") == '0'){
			bootbox.confirm("¿Seguro que desea Retirar?", function(result){ 
				if(result){
					$(btn).attr("vr",'1');
					$(btn).removeClass( "btn-light" ).addClass("btn-primary");

					$('#btna'+num).attr("vr",'0');
					$('#btna'+num).removeClass('btn-primary').addClass('btn-light');
				}
				$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});   
			});
			
		}
	}
	function btncli(btn){
		var num = $(btn).attr('num');
		if( $(btn).attr("vr") == '0'){
			bootbox.confirm("¿Seguro que desea Ahorrar?", function(result){ 
				if(result){
					$(btn).attr("vr",'1');
					$(btn).removeClass( "btn-light" ).addClass("btn-primary");

					$('#btn'+num).attr("vr",'0');
					$('#btn'+num).removeClass('btn-primary').addClass('btn-light');
				}
				$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});   
			});
		}
	}
	function marcartodo(btn){
		if($(btn).attr('accion')=='retirar'){
			bootbox.confirm("¿Retirar todos?", function(result){ 
				$('.btnretirar').each(function() {
					var num = $(this).attr('num');
					$(this).attr("vr",'1');
					$(this).removeClass( "btn-light" ).addClass("btn-primary");
					$('#btna'+num).attr("vr",'0');
					$('#btna'+num).removeClass('btn-primary').addClass('btn-light');
				});
			});
			$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});
		}else{
			bootbox.confirm("¿Ahorrar todos?", function(result){ 
				$('.btnahorrar').each(function() {
					var num = $(this).attr('num');
					$(this).attr("vr",'1');
					$(this).removeClass( "btn-light" ).addClass("btn-primary");
					$('#btn'+num).attr("vr",'0');
					$('#btn'+num).removeClass('btn-primary').addClass('btn-light');
				});
			});
			$('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});
		}
	}
</script>

@else
	<div class="form-group">
		<div class='alert alert-danger'><strong>{{ $mensaje }}</strong></div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
	
@endif