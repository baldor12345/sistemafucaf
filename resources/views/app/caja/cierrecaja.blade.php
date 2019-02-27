<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($caja, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
	{!! Form::label('titulo', 'Titulo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('titulo', $caja->titulo, array('class' => 'form-control input-xs', 'id' => 'titulo', 'placeholder' => 'Ingrese titulo','readonly')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('fecha_horaApert', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha_horaApert', null, array('class' => 'form-control input-xs', 'id' => 'fecha_horaApert')) !!}
	</div>
</div>


<div class="form-group">
	{!! Form::label('monto_inic', 'Monto inicio(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('monto_inic', number_format($caja->monto_iniciado,1), array('class' => 'form-control input-xs', 'id' => 'monto_inic', 'placeholder' => 'S/.','readonly')) !!}
		{!! Form::hidden('monto_iniciado', $caja->monto_iniciado, array('id' => 'monto_iniciado')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('monto_c', 'Monto Cierre(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('monto_c', number_format($diferencia,1), array('class' => 'form-control input-xs', 'id' => 'monto_c', 'placeholder' => 'S/.','readonly')) !!}
		{!! Form::hidden('monto_cierre', $diferencia, array('id' => 'monto_cierre')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('diferencia_mont', 'Diferencia:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('diferencia_mont', number_format($monto_cierre,1) , array('class' => 'form-control input-xs', 'id' => 'diferencia_mont', 'placeholder' => 'S/.','readonly')) !!}
		{!! Form::hidden('diferencia_monto', $monto_cierre, array('id' => 'diferencia_monto')) !!}
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
	{!! Form::button('Cerrar Caja', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarCerrar', 'onclick' => 'CerrarCaja(\''.$entidad.'\', this)')) !!}
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
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		if(fechaActual.getHours()===1 || fechaActual.getHours()===2 || fechaActual.getHours()===3 ||fechaActual.getHours()===4 || fechaActual.getHours()===5 || fechaActual.getHours()===6 || fechaActual.getHours()===7 || fechaActual.getHours()===8 || fechaActual.getHours()===9){
					var horaC ="0"+fechaActual.getHours()+":"+fechaActual.getMinutes();
				if(fechaActual.getMinutes()===1 || fechaActual.getMinutes()===2 || fechaActual.getMinutes()===3 || fechaActual.getMinutes()===4 || fechaActual.getMinutes()===5 || fechaActual.getMinutes()===6 || fechaActual.getMinutes()===7 || fechaActual.getMinutes()===8 || fechaActual.getMinutes()===9){
						var horaC ="0"+fechaActual.getHours()+":0"+fechaActual.getMinutes();
				}
		}else if(fechaActual.getMinutes()===1 || fechaActual.getMinutes()===2 || fechaActual.getMinutes()===3 || fechaActual.getMinutes()===4 || fechaActual.getMinutes()===5 || fechaActual.getMinutes()===6 || fechaActual.getMinutes()===7 || fechaActual.getMinutes()===8 || fechaActual.getMinutes()===9){
			var horaC = fechaActual.getHours()+":0"+fechaActual.getMinutes();
		}else{
			var horaC =fechaActual.getHours()+":"+fechaActual.getMinutes();
		}
		var fecha_caja = '{{ $fecha_caja }}';
		$('#hora_cierre').val(horaC);
		$('#fecha_horaApert').val(fecha_caja);
		
	});
	
	function CerrarCaja(entidad){
		var fechacierre = '{{ $fecha_caja }}';
		var last_day = '{{$last_day}}';

		var fecharecibida = $('#fecha_horaApert').val();

		if(fecharecibida >= fechacierre){
			if(fecharecibida<=last_day){
				guardar(entidad);
			}else{
				document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-danger' role='alert'><span >La fecha cierre no puede ser mayor que la fecha "+last_day+" </span></div>";
					$('#divMensajeError{{ $entidad }}').show();
			}
		}else{
			document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-danger' role='alert'><span >La fecha cierre no puede ser menor que la fecha de apertura  "+fechacierre+"</span></div>";
					$('#divMensajeError{{ $entidad }}').show();
		}

	}


</script>