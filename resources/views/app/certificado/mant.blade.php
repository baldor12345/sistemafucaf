@if( $day >= $fechaf)
<div id="infocertidicado"></div>
<div id="info2"></div>
{!! Form::model($certificado, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	
	<div class="form-row">
		<div class="form-group">
			{!! Form::label('month1', 'Seleccione Periodo:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
			<div class="col-sm-8 col-xs-12">
				{!! Form::select('month1', $cboMonth, $month_first , array('class' => 'form-control input-xs', 'id' => 'month1')) !!}
			</div>
			<div class="col-sm-8 col-xs-12">
				{!! Form::select('month2', $cboMonth, $month_last, array('class' => 'form-control input-xs', 'id' => 'month2')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('anio', 'Año:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
			<div class="col-sm-8 col-xs-12">
				{!! Form::select('anio', $cboAnios, null, array('class' => 'form-control input-xs', 'id' => 'anio')) !!}
			</div>
		</div>

	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarcertificado', 'onclick' => 'guardarcertificado(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		
		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		
		$('#fechag').val(fechai);
		configurarAnchoModal('350');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

		document.getElementById("info2").innerHTML = "<div class='alert alert-info' role='info'><span >certificado, se puede generar trimestral o semestral, asegurese de que ya exista compra de acciones en el rango de meses seleccionadas!</span></div>";
						$('#info2').show();
	}); 

	function guardarcertificado(entidad){
		var month1_select = $('#month1').val();
		var month2_select = $('#month2').val();
		var year_select = $('#anio').val();
		var year_now = new Date();
		var year = year_now.getFullYear();
		var date_last = '{{ $date_last }}';
		var data =date_last.split('-');
		var month_now = '{{$month_now}}';

		if(year_select >= year ){
			if(month1_select>=parseInt(data[1])){
				if(month2_select <= parseInt(month_now)){
					if(month2_select >= (parseInt(data[1])+2)){
						guardar(entidad);
					}else{
						document.getElementById("infocertidicado").innerHTML = "<div class='alert alert-warning' role='warning'><span >el certificado a generar debe ser de un minimo de tres meses</span></div>";
						$('#infocertidicado').show();
					}
				}else{
					document.getElementById("infocertidicado").innerHTML = "<div class='alert alert-danger' role='danger'><span >mes hasta seleccionado incorrecto, debe ser no mayor al mes actual</span></div>";
								$('#infocertidicado').show();
				}
				
			}else{
				document.getElementById("infocertidicado").innerHTML = "<div class='alert alert-danger' role='danger'><span >el mes inicio seleccionado debe ser mayor de del periodo anterior</span></div>";
				$('#infocertidicado').show();
			}
			
			
		}else{
			document.getElementById("infocertidicado").innerHTML = "<div class='alert alert-danger' role='danger'><span >año seleccionado incorrecto!</span></div>";
				$('#infocertidicado').show();
		}
		

	}
</script>
@else
<h3 class="text-warning">Ya existe una lista de certificado, Gracias!.</h3>
@endif