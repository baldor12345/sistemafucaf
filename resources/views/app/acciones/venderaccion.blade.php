<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open(array('route' => array('acciones.guardarventa', $persona->id),'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('idpropietario', $persona->id, array('id' => 'idpropietario')) !!}
<div class="form-group">
<p id="info" class="" ></p>
</div>

<div class="form-group">
	{!! Form::label('dni', 'DNI del comprador:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('dni', null, array('class' => 'form-control input-xs input-number', 'id' => 'dni', 'placeholder' => 'asegurese de que el dni ya este registrado...', 'maxlength' => '8')) !!}
		<p id="nombresCompletos" class="" ></p>
		<input type="hidden" id="idcomprador", name="idcomprador" value="">
	</div>
</div>

<div class="form-group">
	{!! Form::label('cantidad_accion', 'Cantidad a vender:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('cantidad_accion', null, array('class' => 'form-control input-xs input-number', 'id' => 'cantidad_accion', 'placeholder' => 'Ingrese cantidad')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('configuraciones_id', 'Precio de accion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('configuraciones_id', $cboConfiguraciones, null, array('class' => 'form-control input-xs', 'id' => 'configuraciones_id')) !!}
	</div>
</div>

<div class="form-group ">
	{!! Form::label('fechai', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('dni', 'Propietario:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		<p id="nombresCompletos" class="" >{{ '  dni: '.$persona->dni.'   nom: '.$persona->nombres.' '.$persona->apellidos}}</p>
	</div>
</div>

<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'descripcion')) !!}
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
	{!! Form::button('Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}


<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('460');

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		$('#fechai').val(fechai);




		//funcion para los datos de la persona
		$("input[name=dni]").change(function(event){
        	$.get("personas/"+event.target.value+"",function(response, facultad){
				console.log("datos de la persona");
				console.log(response);
				$('#nombres').val('');
				$('#idcomprador').val('');
				for(i=0; i<response.length; i++){
					document.getElementById("nombresCompletos").innerHTML = response[i].nombres +" "+ response[i].apellidos;
					document.getElementById("idcomprador").value = response[i].id;
				}
			});
    	});

		$("input[name=dni]").change(function(event){
        	$.get("acciones/"+event.target.value+"",function(response, facultad){
				console.log("datos de la cantidad acumulada de acciones");
				console.log(response);
				
				var cantAcciones=0;
				for(i=0; i<response.length; i++){
					cantAcciones+=  parseInt(response[i].cantidad_accion_acumulada);
				}
				console.log(cantAcciones);
				
				var limite_accionPor= response[0].limite_acciones;
				var cantidad_limite = parseInt(cantAcciones*limite_accionPor);
				var result="Estimado usuario, por reglas establecidas de la empresa usted solo puede adquirir el 20% de la "+
							"cantidad total de las acciones por el cual usted puede adquirir solo: "+ cantidad_limite+" acciones GRACIAS!";
				document.getElementById("info").innerHTML= result;
			});
    	});
		
	});

	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});


	/*
	function realizarventa(){
		route = 'acciones/updateventa';
		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			data: $('#formMantenimientoAcciones').serialize(),
			beforeSend: function(){
	        },
	        success: function(res){
				$('#idcaja').val('');
				$('#titulo').val('');
				$('#fecha').val('');
				$('#monto_iniciado').val('');
				$('#hora_cierre').val('');
				$('#monto_cierre').val('');
				$('#diferencia_monto').val('');
				$('#descripcion').val('');
	        }
		}).fail(function(){
		});
	}*/ 

</script>