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

<div class="form-row">
	<div class="table-responsive">
		<table class="table table-bordered table-sm table-condensed table-hover">
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
					<td  colspan="2" rowspan="1" align="center">U. B. Acumulada</td>
					<td rowspan="5"></td>
					<td  colspan="1" rowspan="1">G. Adm. Acum.</td>
					<td  colspan="1" rowspan="1">{{ $gastadmacumulado }}</td>
					<td rowspan="5"></td>
					<td rowspan="5"></td>
					<td rowspan="5"></td>
					<td  colspan="1" rowspan="2">F Social 10%</td>
					<td  colspan="1" rowspan="2">{{ $utilidad_neta*0.1 }}</td>
					<td rowspan="5"></td>
					<td rowspan="5"></td>
				</tr>
				<tr>
						
					<td>Intereses</td>
					<td>{{ $intereses }}</td>
					<td  colspan="1" rowspan="1">I. Pag. Acum.</td>
					<td  colspan="1" rowspan="1">{{ $int_pag_acum }}</td>
				</tr>
				<tr>
						
					<td>Otros</td>
					<td>{{ $otros }}</td>
					<td  colspan="1" rowspan="1">Otros Acum.</td>
					<td  colspan="1" rowspan="1">{{ $otros_acumulados }}</td>
					<td  colspan="1" rowspan="3">R Legal 10%</td>
					<td  colspan="1" rowspan="3">{{ $utilidad_neta*0.1 }}</td>
				</tr>
				<tr>
						
					<td>Total acumulado</td>
					<td>{{ $intereses + $otros }}</td>
					<td  rowspan="1" colspan="1">TOTAL ACUMULADO</td>
					<td  rowspan="1" colspan="1">{{ ($gastadmacumulado + $int_pag_acum + $otros_acumulados) }}</td>
				</tr>
				<tr>
						
					<td>U.B DU Anterior</td>
					<td>{{ $du_anterior }}</td>
					<td  rowspan="1" colspan="1">Gast. DU Anterior</td>
					<td  rowspan="1" colspan="1">{{ $gast_du_anterior }}</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
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
				</tr>
			</tfoot>
		</table>
	</div>
	<div class="table-responsive">
		<table class="table table-sm table-bordered table-condensed table-hover table-responsive">
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
							echo("<td align='center'>".$acciones_mensual[$ind]->cantidad_mes."</td>");
							$total_acc_mensual += $acciones_mensual[$ind]->cantidad_mes;
							$ind ++;
						}else{
							echo("<td align='center'>0</td>");
						}
					}
					?>
					<td>0</td>
					<td>{{ $total_acc_mensual }}</td>
				</tr>
				<tr>
					<td colspan="2" align='center'>Meses "trabajados"</td>
					<?php
					for($mes=12; $mes>=1;$mes--){
						echo("<td align='center'>".$mes."</td>");
					}
					?>
					<td align='center'>0</td><td align='center'>---</td>
				</tr>
				<tr>
					<td colspan="2" align='center'>Acciones-mes</td>
					<?php
					$j=12;
					$indice=0;
					$sumatotal_acc_mes = 0;
					
					for($i=1; $i<=12; $i++){
						if((($indice<count($acciones_mensual))?$acciones_mensual[$indice]->mes:"") == $i){
							$sumatotal_acc_mes += $acciones_mensual[$indice]->cantidad_mes * $j;
							echo("<td align='center'>".round($acciones_mensual[$indice]->cantidad_mes * $j, 1)."</td>");
							$j--;
							$indice++;
						}else{
							echo("<td align='center'>0</td>");
						}
					}
					
					?>
					<td>0</td><td>{{ $sumatotal_acc_mes }}</td>
				</tr>
	
				<tr><td colspan="17"></td></tr>
			</tbody>
		</table>
	</div>
	<div class="table-responsive">
		<table class="table table-sm table-bordered table-condensed table-hover table-responsive">
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
	<div class="table-responsive">
		<table class="table table-sm table-bordered table-condensed table-hover table-responsive">
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
					<td align='center'>0</td>
					<td align='center'>...</td>
				</tr>
			</tbody>
			<tfoot>

			</tfoot>
		</table>
	</div>
	<div class="table-responsive">
		<table class="table table-sm table-bordered table-condensed table-hover table-responsive">
			<thead>
			
				<tr>
					<th align="center">PASO 5:  Se multiplica cada una de estas utilidades anuales por el número de acciones de cada socio en el mes respectivo.  Los resultados son las utilidades del socio en cada uno de los  meses.</th>
				</tr>
			</thead>
		</table>
	</div>
	<style>
		.custom-control-input:checked~.custom-control-indicator{
		color:white;
		background-color:red;
		}
	</style>
	<div class="table-responsive">
		<table class="table table-sm table-bordered  table-condensed table-hover table-responsive">
			<thead>
				<tr  class="table-active"><th scope="row" class="table-active" colspan="19" align="center">PASO 6: Se sumasn estas utilidades mensuales y se obtiene  la UTILIDAD TOTAL del socio en el año (última columna de la derecha).</th></tr>
				<tr  class="table-active"><th scope="row" class="table-active" rowspan="2" align="center">N°</th><th rowspan="2" colspan="2" align="center">SOCIOS</th><th colspan="12" align='center'>{{ $anio }}</th><th align='center'>{{ $anio +1 }}</th><th rowspan="2" align='center'>TOTAL</th><th colspan="2" align='center'>OPPERACION</th></tr>
				<tr  class="table-active">
					<th align='center'>E</th align='center'><th align='center'>F</th><th align='center'>M</th><th align='center'>A</th><th align='center'>M</th><th align='center'>J</th><th align='center'>J</th><th align='center'>A</th><th align='center'>S</th><th align='center'>O</th><th align='center'>N</th><th align='center'>D</th><th align='center'>E</th>
					<th align='center'>REITRAR</th><th align='center'>AHORRAR</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$socios = Persona::where('tipo','=','SC')->orwhere('tipo','=','S')->get();
			
				for($i=0; $i< count($socios); $i++){
					
					$listaAcciones = DistribucionUtilidades::list_por_persona($socios[$i]->id, $anio)->get();
					$num_accionesenero = DistribucionUtilidades::list_enero($socios[$i]->id, ($anio-1))->get();
					
					$utilidades = array();
					if(count($listaAcciones)>0){
						echo("<tr><td rowspan='2'  align='center'>".($i+1)."</td><th rowspan='2' colspan='2' align='center'>".$socios[$i]->nombres." ".$socios[$i]->apellidos."</th>");
						$l=0;
						$sumtotalAcciones =0;
						for($j=1; $j<=12; $j++){
							$numaccciones = 0;
							if($j == 1){
								$numaccciones = count($num_accionesenero)>0?$num_accionesenero[0]->cantidad_total:0;
							}
								
							if(((($l)<count($listaAcciones))?$listaAcciones[$l]->mes:"") == $j){
								$numaccciones += $listaAcciones[$l]->cantidad_mes;
								echo("<td align='center'>".$numaccciones."</td>");
								$utilidades[$j-1] = $factores_mes[$j-1] * $numaccciones;
								$sumtotalAcciones += $numaccciones;
								$l++;
							}else{
								echo("<td align='center'>0</td>");
								$utilidades[$j-1] = 0;
							}
						}
						echo("<td align='center'>0</td><td>".round($sumtotalAcciones,1)."</td><td></td><td></td><tr>");
							$sumtotal_util = 0;
						for($j=1; $j<=12; $j++){
							echo("<td align='center'>".round($utilidades[$j-1],1)."</td>");
							$sumtotal_util += $utilidades[$j-1];
						}
						echo("<td align='center'>0</td><td>".round($sumtotal_util,1)."</td>");
						?>
						
						<td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-primary btn-sm btnretirar ','vr'=>'1','num'=>''.$i ,'id' => 'btn'.$i, 'onclick' => 'btnclieck(this)',  'persona_id' => ''.$socios[$i]->id , 'utilidad'=> ''.round($sumtotal_util,1))) !!}</td>
						<td>{!! Form::button('<i class="fa fa-check fa-lg" style="color:white"></i>', array('class' => 'btn btn-light btn-sm btnahorrar','vr'=>'0', 'num'=>''.$i , 'id' => 'btna'.$i , 'onclick' => 'btncli(this)',  'persona_id' => ''.$socios[$i]->id , 'utilidad'=> ''.round($sumtotal_util,1))) !!}</td>

						<?php
						echo("</tr>");
					}
				}
				?>

			</tbody>
			<tfoot>
				<tr>
					<th rowspan="2" colspan="2">TOTAL</th>
					<th>Acciones</th>
					<?php
						$total_acc_mensual  = 0;
						$ind = 0;
						
						for($i=1; $i<=12; $i++){
							if((($ind<count($acciones_mensual))?$acciones_mensual[$ind]->mes: "") == "".$i){
								echo("<th align='center'>".$acciones_mensual[$ind]->cantidad_mes."</th>");
								$total_acc_mensual += $acciones_mensual[$ind]->cantidad_mes;
								$ind ++;
							}else{
								echo("<th align='center'>0</th>");
							}
						}
					?>
					<th>0</th>
					<th>{{ $total_acc_mensual }}</th>
					<th colspan="2">{!! Form::button('<i class="fa fa-check fa-lg"></i> RETIRAR TODO', array('class' => 'btn btn-warning btn-xs', 'accion'=>'retirar',  'id' => 'btnrecibo', 'onclick' => 'marcartodo(this)')) !!}</th>
				</tr>
				<tr>
					<th>Utilidades</th>
						<?php
						$j=12;
						$indice=0;
						$sumatotal_utilidades = 0;
						
						for($i=1; $i<=12; $i++){
							if((($indice<count($acciones_mensual))?$acciones_mensual[$indice]->mes:"") == $i){
								$sumatotal_utilidades += $acciones_mensual[$indice]->cantidad_mes * $factor*$j;
								echo("<th align='center'>".round($acciones_mensual[$indice]->cantidad_mes * $factor *$j, 1)."</th>");
								$j--;
								$indice++;
							}else{
								echo("<th align='center'>0</th>");
							}
						}
						
						?>
					<th>0</th><th>{{ round($sumatotal_utilidades, 1) }}</th>
					<th colspan="2">{!! Form::button('<i class="fa fa-check fa-lg"></i> AHORRAR TODO', array('class' => 'btn btn-success btn-xs','accion'=>'ahorrar', 'id' => 'btnrecibo', 'onclick' => 'marcartodo(this)')) !!}</th>

				</tr>
				<tr>
					<th colspan="17" align="center"> PASO 7: Se efectúa la distribución y se decide que parte de las utilidades se capitaliza y que parte se entrega.
						(FUNDERPERU recomienda capitalizar el mayor monto posible). Se efectua todo y se consigna en los libros de Actas y de Caja. !FELICITACION
					</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarDist', 'onclick' => 'guardar_distribucion(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>


{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('1500');
	}); 

	function guardar_distribucion(entidad,rutarecibo) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var data         = submitFormDistr(idformulario);
		var respuesta    = '';
		var listar       = 'NO';
		if ($(idformulario + ' :input[id = "listar"]').length) {
			var listar = $(idformulario + ' :input[id = "listar"]').val()
		};

		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			
			if(respuesta === 'ERROR'){
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
					bootbox.alert("<div class='alert alert-danger'><strong>¡Error!</strong> Ya existe una distribución para el año indicado por lo que solo puede ver el detalle.!</div>", function(){ 
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
		console.log('Accion: form: '+accion+'   param: '+parametros);
		var metodo     = $(idformulario).attr('method').toLowerCase();
		console.log('Metodo: '+metodo);
		var respuesta  = $.ajax({
			url : accion,
			type: metodo,
			data: parametros
		});
		console.log('Respuesta: '+respuesta);
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
		<div class='alert alert-danger'><strong>¡Error!</strong> Ya existe una distribucion para la fecha seleccionada.!</div>
	</div>
	<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
@endif