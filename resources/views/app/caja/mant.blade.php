<style> 
textarea {
    width: 100%;
    height: 50px;
    padding: 12px 20px;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    background-color: #f8f8f8;
    font-size: 16px;
    resize: none;
}
</style>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($caja, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
	{!! Form::label('titulo', 'Titulo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('titulo', null, array('class' => 'form-control input-xs', 'id' => 'titulo', 'placeholder' => 'Ingrese titulo')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('fecha_horaApert', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha_horaApert', null, array('class' => 'form-control input-xs', 'id' => 'fecha_horaApert', 'placeholder' => '')) !!}
	</div>
</div>
<?php
	if($caja != null){
		echo "<input type='hidden' id='fechaTemp' value='".Date::parse($caja->fecha_horaApert )->format('d/m/Y')."'>";
		echo "<input type='hidden' id='horaAp' value='".$caja->fecha_horaApert."'>";
	}else{
		echo "<input type='hidden' id='fechaTemp' value=''>";
	}
?>

<div class="form-group">
	{!! Form::label('hora_apertura', 'Hora Apertura:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::time('hora_apertura', null, array('class' => 'form-control input-xs', 'id' => 'hora_apertura', 'placeholder' => '')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('monto_iniciado', 'Monto Inicio(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('monto_iniciado', $ingresos, array('class' => 'form-control input-xs', 'id' => 'monto_iniciado', 'placeholder' => 'S/.')) !!}
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
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('400');

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fecha_horaApert = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		if(fechaActual.getMinutes()===1 || fechaActual.getMinutes()===2 || fechaActual.getMinutes()===3 ||
			fechaActual.getMinutes()===4 || fechaActual.getMinutes()===5 || fechaActual.getMinutes()===6 ||
			fechaActual.getMinutes()===7 || fechaActual.getMinutes()===8 || fechaActual.getMinutes()===9 ||
			fechaActual.getHours()===1 || fechaActual.getHours()===2 || fechaActual.getHours()===3 ||
			fechaActual.getHours()===4 || fechaActual.getHours()===5 || fechaActual.getHours()===6 ||
			fechaActual.getHours()===7 || fechaActual.getHours()===8 || fechaActual.getHours()===9){

				var horaAp ="0"+fechaActual.getHours()+":0"+fechaActual.getMinutes();
		}else{
				var horaAp =fechaActual.getHours()+":"+fechaActual.getMinutes();
		}
		console.log(horaAp);


		if($('#fechaTemp').val() !== ""){
			// DD/MM/YYYY
			var valoresFecha = $('#fechaTemp').val().split('/');
			//yyy/MM/DD
			var fecha = valoresFecha[2] + "-" + valoresFecha[1] + "-" + valoresFecha[0];
			$('#fecha_horaApert').val(fecha);
			$('#hora_apertura').val($('#horaAp').val());
		}else{
			$('#fecha_horaApert').val(fecha_horaApert);
			$('#hora_apertura').val(horaAp);
		}

		
		
	}); 
</script>