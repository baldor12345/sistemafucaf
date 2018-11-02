<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($gastos, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	
	<div class="form-row">
		<div class="form-group col-md-12">
			{!! Form::label('monto', 'Monto S/.:', array('class' => '')) !!}
			{!! Form::text('monto', null, array('class' => 'form-control input-xs', 'id' => 'monto', 'placeholder' => 'S/.')) !!}
		</div>
		<div class="form-group col-md-12">
			{!! Form::label('fechag', 'Fecha:', array('class' => '')) !!}
			{!! Form::date('fechag', null, array('class' => 'form-control input-xs', 'id' => 'fechag')) !!}
		</div>
		<div class="form-group col-md-12">
			{!! Form::label('concepto', 'Concepto:', array('class' => '')) !!}
			{!! Form::textarea('concepto', null, array('class' => 'form-control input-sm','rows' => 4, 'id' => 'concepto', 'placeholder' => 'Ingrese concepto')) !!}
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
		configurarAnchoModal('450');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	}); 
</script>