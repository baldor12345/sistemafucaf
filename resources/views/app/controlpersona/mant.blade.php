<div id="divMensajeError{!! $entidad !!}"></div>
<?php 
	$inicio =0;
?>
{!! Form::model($controlpersona, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	{!! Form::hidden('fecha', $fecha, array('id' => 'fecha')) !!}
	<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
		<thead>
			<tr>
				@foreach($cabecera as $key => $value)
					<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
				@endforeach
			</tr>
		</thead>
		<tbody>
			<?php
				$contador = $inicio + 1;
			?>
			@foreach ($lista as $key => $value)
			<tr>
				<td>{{ $contador }}</td>
				<td>{{ $value->codigo}}</td>
				<td>{{ $value->nombres.' '.$value->apellidos }} </td>
				<td>{!! Form::select('asistencia', $cboAsistencia,'A', array('class' => 'form-control input-xs select_asist', 'id' => 'asistencia','persona_id'=>''.$value->id,'asist'=>'A', 'onchange' => 'cambiartardanza(this);')) !!}</td>
			</tr>
			<?php
				$contador = $contador + 1;
			?>
			@endforeach
		</tbody>
	</table>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar_control(\''.$entidad.'\', this)')) !!}
			&nbsp;
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('650');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
}); 

function cambiartardanza(elemento) {
	$(elemento).attr('asist', $(elemento).val());
}
function guardar_control(entidad, idboton) {
	var idformulario = IDFORMMANTENIMIENTO + entidad;
	var data         = submitForm_control(idformulario);
	var respuesta    = '';
	var listar       = 'NO';
	if ($(idformulario + ' :input[id = "listar"]').length) {
		var listar = $(idformulario + ' :input[id = "listar"]').val()
	};
	var btn = $(idboton);
	btn.button('loading');
	data.done(function(msg) {
		respuesta = msg;
	}).fail(function(xhr, textStatus, errorThrown) {
		respuesta = 'ERROR';
	}).always(function() {
		btn.button('reset');
		if(respuesta === 'ERROR'){
		}else{
			if (respuesta === 'OK') {
				cerrarModal();
				if (listar === 'SI') {
					
					buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
					
				}        
			} else {
				mostrarErrores(respuesta, idformulario, entidad);
			}
		}
	});
}
function submitForm_control (idformulario) {
	var i=0;
	var datos="";
	$('.select_asist').each(function() {
		if($(this).attr("asist") != "A"){
			console.log(i);
			datos += "&persona_id"+i+"="+$(this).attr("persona_id")+"&asist"+i+"="+$(this).attr("asist");
			i++;
		}
	});
	datos += "&cantidad="+i;
	var parametros = $(idformulario).serialize();
	parametros += datos;
	var accion     = $(idformulario).attr('action').toLowerCase();
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


</script>