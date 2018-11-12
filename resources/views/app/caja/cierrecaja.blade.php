<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open(array('route' => array('caja.cerrarcaja', $caja->id),'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
	{!! Form::label('titulo', 'Titulo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('titulo', $caja->titulo, array('class' => 'form-control input-xs', 'id' => 'titulo', 'placeholder' => 'Ingrese titulo', 'disabled')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('fecha_horaApert', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha_horaApert', null, array('class' => 'form-control input-xs', 'id' => 'fecha_horaApert')) !!}
	</div>
</div>


<div class="form-group">
	{!! Form::label('monto_iniciado', 'Monto inicio(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('monto_iniciado', $caja->monto_iniciado, array('class' => 'form-control input-xs', 'id' => 'monto_iniciado', 'placeholder' => 'S/.')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('monto_cierre', 'Monto Cierre(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('monto_cierre', $diferencia, array('class' => 'form-control input-xs', 'id' => 'monto_cierre', 'placeholder' => 'S/.')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('diferencia_monto', 'Diferencia:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('diferencia_monto', 0.0 , array('class' => 'form-control input-xs', 'id' => 'diferencia_monto', 'placeholder' => 'S/.')) !!}
	</div>
</div>


<div class="form-group">
	{!! Form::label('hora_cierre', 'Hora Cierre:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::time('hora_cierre', null, array('class' => 'form-control input-xs', 'id' => 'hora_cierre', 'placeholder' => '')) !!}
	</div>
</div>


<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripcion')) !!}
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
	{!! Form::button('Cerrar Caja', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}


<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('450');

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		$('#fecha_horaApert').val(fechai);
		
	});




</script>