<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>


<script>
function cargarselect2(entidad){
		var select = $('#tipo_id').val();

		if(select == ''){
			$('#concepto_id').html('<option value="" selected="selected">Seleccione</option>');
			return false;
		}

		route = 'caja/cargarselect/' + select + '?entidad=' + entidad + '&t=si';

		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			beforeSend: function() {
				$('#concepto_id').html('<option value="" selected="selected">Seleccione</option>');
			},
	        success: function(res){
	        	$('#concepto_id').html(res);
	        }
		});
}



</script>


<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open(array('route' => array('caja.registrarmovimiento', $id),'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
	{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => '')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('tipo', 'Tipo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('tipo_id', $cboTipo, null, array('class' => 'form-control input-xs', 'id' => 'tipo_id', 'onchange' => 'cargarselect2("concepto")')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('concepto_id', 'Concepto:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('concepto_id', $cboConceptos, null, array('class' => 'form-control input-xs', 'id' => 'concepto_id')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('dni', 'Dni:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('dni', null, array('class' => 'form-control input-xs', 'id' => 'dni', 'placeholder' => 'asegurese de que el dni ya este registrado...' )) !!}
		<p id="nombresCompletos" class="" ></p>
		<input type="hidden" id="persona_id", name="persona_id" value="">
	</div>
</div>

<div class="form-group">
	{!! Form::label('total', 'Total(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('total', null, array('class' => 'form-control input-xs', 'id' => 'total', 'placeholder' => 'S/.')) !!}
	</div>
</div>


<div class="form-group">
	{!! Form::label('comentario', 'Comentario:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('comentario', null, array('class' => 'form-control input-xs', 'id' => 'comentario', 'placeholder' => '')) !!}
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
		var fecha = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		$('#fecha').val(fecha);


		$("input[name=dni]").change(function(event){
        	$.get("personas/"+event.target.value+"",function(response, facultad){
				console.log("datos de la persona");
				console.log(response);
				$('#nombres').val('');
				$('#persona_id').val('');
				for(i=0; i<response.length; i++){
					document.getElementById("nombresCompletos").innerHTML = response[i].nombres +" "+ response[i].apellidos;
					document.getElementById("persona_id").value = response[i].id;
				}
			});
    	});

	}); 
</script>