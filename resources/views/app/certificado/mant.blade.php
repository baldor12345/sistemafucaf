<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($certificado, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	
	<div class="form-row">
		<div class="form-group">
			{!! Form::label('month1', 'Seleccione Periodo:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
			<div class="col-sm-8 col-xs-12">
				{!! Form::select('month1', $cboMonth, 1, array('class' => 'form-control input-xs', 'id' => 'month1')) !!}
			</div>
			<div class="col-sm-8 col-xs-12">
				{!! Form::select('month2', $cboMonth, 6, array('class' => 'form-control input-xs', 'id' => 'month2')) !!}
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
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
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
	}); 
</script>