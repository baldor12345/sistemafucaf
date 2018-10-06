<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>

<script>


$('#btnAgregar').click(function(){
	if($('#cantidad_accion').val()!==''&& $('#estado').val()!=='' && $('#fecha').val()!==''){

		//evaluar el dato estado
		var estadoC="";
		if($('#estado').val()==='C'){
			estadoC="Compra";
		}else if($('#estado').val()==='V'){
			estadoC="Venta";
		}
		//insertando datos a la tabla detalleAcciones
		$('#tablaAcciones').append("<tr cant='"+$('#cantidad_accion').val()+"' estadoT='"+$('#estado').val()+"' fechaT='"+$('#fecha').val()+"' class='acciones'>"+
		"<td>"+$("#tablaDir tr").length+"</td>"+
		"<td>"+($('#cantidad_accion').val())+"</td>"+
		"<td>"+(estadoC)+"</td>"+
		"<td>"+($('#fecha').val())+"</td>"+
		"<td><button class='btn btn-warning btn-xs'><div class='glyphicon glyphicon-pencil'></div>Editar</button></td></tr>");
		$('#cantidad_accion').val('');
		$('#fecha').val('');
		$('#estado').empty();
		$('#estado').append("<option value=''>Seleccione</option><option value='C'>Compra</option><option value='V'>Venta</option>");
		// $("#tablaDir tr").length;
	}
	$('#cadenaAcciones').val(getCadenaAcciones);
});

function getCadenaAcciones() {
	var cadenaDir = "";
	var botones = document.getElementsByClassName("acciones");
	if(botones.length !== 0){
		for (var i = 0; i < botones.length; i++) {
			if (i === botones.length - 1) {
				cadenaDir += ($(botones[i]).attr("cant"))+":"+($(botones[i]).attr("estadoT"))+":"+($(botones[i]).attr("fechaT"));
			}else{
				cadenaDir += ($(botones[i]).attr("cant"))+":"+($(botones[i]).attr("estadoT"))+":"+($(botones[i]).attr("fechaT"))+",";
			}
		}
	}else{
		cadenaDir = "";
	}
	console.log(cadenaDir);
	return cadenaDir;
}


</script>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($acciones, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
<p id="info" class="" ></p>
</div>

<div class="form-group">
	{!! Form::label('dni', 'Dni:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('dni', null, array('class' => 'form-control input-xs', 'id' => 'dni', 'placeholder' => 'Ingrese dni' )) !!}
		<p id="nombresCompletos" class="" ></p>
		<input type="hidden" id="persona_id", name="persona_id" value="">
	</div>
</div>

<div class="form-group">
	{!! Form::label('cantidad_accion', 'Cantidad Accion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('cantidad_accion', null, array('class' => 'form-control input-xs', 'id' => 'cantidad_accion', 'placeholder' => 'Ingrese cantidad')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('configuraciones_id', 'Precio de accion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('configuraciones_id', $cboConfiguraciones, null, array('class' => 'form-control input-xs', 'id' => 'configuraciones_id')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('estado', 'Estado:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('estado', $cboEstado, null, array('class' => 'form-control input-xs', 'id' => 'estado')) !!}
	</div>
</div>

<div class="form-group ">
	{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
	</div>
</div>


	<div class="form-group ">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="glyphicon glyphicon-check"></i> ¡Correcto!', array('class' => 'correcto btn btn-success waves-effect waves-light m-l-10 btn-md hidden input-sm', 'onclick' => '#')) !!}
				{!! Form::button('<i class="glyphicon glyphicon-remove-circle"></i> ¡Incorrecto!', array('class' => 'incorrecto btn btn-danger waves-effect waves-light m-l-10 btn-md hidden input-sm', 'onclick' => '#')) !!}
				{!! Form::button('<i class="glyphicon glyphicon-plus visualisar"></i>', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md btnAnadir input-sm visualisar','id' => 'btnAgregar')) !!}
			</div>
	</div>

		<table id="tablaDir" class="table table-bordered table-striped table-condensed table-hover">
			<thead>
				<tr>
					<th width='7%'>#</th>
					<th>CANT</th>
					<th>ESTADO</th>
					<th>FECHA</th>
					<th width='20%'>Operacion</th>
				</tr>
			</thead>

			<tbody id="tablaAcciones">
				@if ($boton == "Modificar")
					<?php
					$cont = 1;
					?>
					@foreach ($listaDet as $key => $value)
					
					<tr>
						<td >{{$cont}}</td>
						<td idfacultad='{{ $value->id_facultad }}' idescuela='{{ $value->id_escuela }}' idespecialidad='{{ $value->id_especialidad }}' class='direciones'>{{ $value->nombre_facultad}} {{$value->nombre_escuela}} {{$value->nombre_especialidad }}</td>
						<td><button class='btn btn-warning btn-xs borrar'><div class='glyphicon  glyphicon-pencil'></div>Editar</button></td>
					</tr>
					<?php
					$cont ++;
					?>
					@endforeach
				@endif
			</tbody>
				<input type="hidden" id="cadenaAcciones" name="cadenaAcciones" value="">
		</table>

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
		configurarAnchoModal('500');
		//funcion para los datos de la persona
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

		$("input[name=dni]").change(function(event){
        	$.get("acciones/"+event.target.value+"",function(response, facultad){
				console.log("datos de la cantidad acumulada de acciones");
				console.log(response);
				var cantAcciones=response[0].cantidad_accion_acumulada;
				var limite_accionPor= response[0].limite_acciones;
				var cantidad_limite = parseInt(cantAcciones*limite_accionPor);
				var result="Estimado usuario, por reglas establecidas de la empresa usted solo puede adquirir el 20% de la "+
							"cantidad total de las acciones por el cual usted puede adquirir solo: "+ cantidad_limite+" acciones GRACIAS!";
				document.getElementById("info").innerHTML= result;
			});
    	});
		
	}); 


	$('#cadenaAcciones').val(getCadenaAcciones);
	console.log($('#cadenaAcciones').val(getCadenaAcciones));
</script>