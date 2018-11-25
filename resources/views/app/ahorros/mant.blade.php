<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($ahorros, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="row">
		<div id='txtcliente' class="form-group col-6 col-md-6 col-sm-6">
			{!! Form::label('dnicl', 'DNI del Cliente: *', array('class' => '')) !!}

			@if($ahorros = null)
			{!! Form::text('dnicl', null, array('class' => 'form-control input-xs', 'id' => 'dnicl', 'placeholder' => 'Ingrese el DNI del cliente')) !!}
			@else
			{!! Form::text('dnicl', $dni, array('class' => 'form-control input-xs', 'id' => 'dnicl', 'placeholder' => 'Ingrese el DNI del cliente')) !!}
			@endif
			<p id="nombrescl" class="" >DNI Cliente Vacio</p>
			<input type="hidden" id="persona_id" name="persona_id" value="" >
		</div>
		
		<div class="form-group col-6 col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('importe', 'Importe S/.: *', array('class' => '')) !!}
			{!! Form::text('importe', null, array('class' => 'form-control input-xs', 'id' => 'importe', 'placeholder' => 'Ingrese el monto de ahorro')) !!}
		</div>
	</div>
	
		<div class="form-group">
			<div class="form-group col-6 col-md-6 col-sm-6">
				{!! Form::label('interes', 'Interes mensual (%): *', array('class' => '')) !!}
				{!! Form::text('interes', null, array('class' => 'form-control input-xs', 'id' => 'interes', 'placeholder' => 'Interes mensual')) !!}
			</div>
			
			<div class="form-group col-6 col-md-6 col-sm-6" style="margin-left: 25px;">
				{!! Form::label('periodo', 'Periodo (N° Meses): *', array('class' => '')) !!}
				{!! Form::text('periodo', null, array('class' => 'form-control input-xs', 'id' => 'periodo', 'placeholder' => 'Ingrese Numero de meses')) !!}
			</div>
		</div>
		<div class="form-group ">
			<div class="form-group col-6 col-md-6 col-sm-6" >
				{!! Form::label('fecha_inicio', 'Fecha de inicio: *', array('class' => '')) !!}
				{!! Form::date('fecha_inicio', null, array('class' => 'form-control input-xs', 'id' => 'fecha_inicio')) !!}
			</div>
			
			<div class="form-group col-6 col-md-6 col-sm-6" style="margin-left: 25px;">
				{!! Form::label('concepto', 'Concepto:', array('class' => '')) !!}
				{!! Form::select('concepto', $cboConcepto, $idopcion, array('class' => 'form-control input-sm', 'id' => 'concepto')) !!}
			</div>
		</div>
		<div class="form-group col-12" >
			{!! Form::label('descripcion', 'Descripción: ', array('class' => '')) !!}
			{!! Form::textarea('descripcion', null, array('class' => 'form-control input-sm','rows' => 4, 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción')) !!}
		</div>

		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar','data-dismiss'=>'modal', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
				&nbsp;
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	console.log("dni: "+$('#dnicl').val());
	if($('#dnicl').val() != ''){
		$.get("personas/"+$('#dnicl').val()+"",function(response, facultad){
			if(response.length>0){
				$("#nombrescl").html(response[0].nombres +" "+ response[0].apellidos);
				$("#persona_id").val(response[0].id);
			}else{
				$("#nombrescl").html("El DNI ingresado no existe");
			}
		});
	}else{
		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day;
		
		$('#fecha_inicio').val(fechai);
	}

	configurarAnchoModal('650');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$("input[name=dnicl]").keyup(function(event){
        	$.get("personas/"+event.target.value+"",function(response, facultad){
				//console.log("datos de la persona");
				//console.log(response);
				$('#nombrescl').val('');
				$('#persona_id').val('');
				if(response.length>0){
					$("#nombrescl").html(response[0].nombres +" "+ response[0].apellidos);
					$("#persona_id").val(response[0].id);
				}else{
					$("#nombrescl").html("El DNI ingresado no existe");
				}
			});
    	});

}); 

</script>