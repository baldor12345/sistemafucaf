<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($ahorros, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="row">
	<div class="card-box table-responsive crbox">
		<div class="form-group">
			<div class="form-group col-6 col-md-6 col-sm-6">
				{!! Form::label('dnicl', 'DNI del Cliente: *', array('class' => 'control-label')) !!}
				@if($ahorros = null)
				{!! Form::text('dnicl', null, array('class' => 'form-control input-xs input-number', 'id' => 'dnicl', 'placeholder' => 'Ingrese el DNI del cliente')) !!}
				@else
				{!! Form::text('dnicl', $dni, array('class' => 'form-control input-xs', 'id' => 'dnicl', 'placeholder' => 'Ingrese el DNI del cliente')) !!}
				@endif
				<p id="nombrescl" class="" >DNI Cliente Vacio</p>
				<input type="hidden" id="persona_id" name="persona_id" value="" >
			</div>
			<div class="form-group col-6 col-md-6 col-sm-6" style="margin-left: 15px">
				{!! Form::label('capital', 'Importe S/.: *', array('class' => '')) !!}
				{!! Form::text('capital', null, array('class' => 'form-control input-md input-number', 'id' => 'capital', 'placeholder' => 'Ingrese el monto de ahorro')) !!}
			</div>
		</div>
		<div class = "form-group">
			<div class="form-group col-6 col-md-6 col-sm-6">
				{!! Form::label('interes', 'Interes mensual (%): *', array('class' => '')) !!}
				{!! Form::text('interes',($configuraciones->tasa_interes_ahorro*100), array('class' => 'form-control input-xs', 'id' => 'interes', 'placeholder' => 'Interes mensual')) !!}
			</div>
			<div class="form-group col-6 col-md-6 col-sm-6" style="margin-left: 15px" >
				{!! Form::label('fechai', 'Fecha de deposito: *', array('class' => '')) !!}
				{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai')) !!}
			</div>
		</div>
		<div class="form-group col-12 col-md-12 col-sm-12" >
			{!! Form::label('concepto', 'Concepto:', array('class' => '')) !!}
			{!! Form::select('concepto', $cboConcepto, $idopcion, array('class' => 'form-control input-xs', 'id' => 'concepto')) !!}
		</div>
		<div class="form-group col-12 col-md-12 col-sm-12" >
			{!! Form::label('comision', 'Comision por voucher S/.: 0.10')!!}
		</div>
		<div class="form-group col-12 col-md-12 col-sm-12" >
			{!! Form::label('totalpagar', 'Total a cancelar S/.: ',array('id' => 'totalpagar'))!!}
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarahorro(\''.$entidad.'\', \''.URL::route($ruta["generareciboahorroPDF"], array()).'\')')) !!}
				&nbsp;
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
		
	</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
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
	$("input[name=capital]").keyup(function(event){
		var capitalahorro = $('#capital').val();
		if(capitalahorro != ''){
			$('#totalpagar').html('Total a cancelar S/.: '+(capitalahorro - (-0.10)));
		}else{
			$('#totalpagar').html('Total a cancelar S/.: ');
		}
	});
}); 

function guardarahorro1(entidad, rutarecibo){
	if(isNaN($('#capital').val()) == false){
		if($('#capital').val() != ''){
			guardar(entidad);
			
		}else{
			var mensaje = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Valor de Monto vacio.!</strong></div>';
			$('#divMensajeErrorAhorros').html(mensaje);
		}
	}else{
		var mensaje = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Valor de monto no válido.!</strong></div>';
        $('#divMensajeErrorAhorros').html(mensaje);
	}


}

function guardarahorro(entidad,rutarecibo) {
	var idformulario = IDFORMMANTENIMIENTO + entidad;
	var data         = submitForm(idformulario);
	var respuesta    = '';
	var listar       = 'NO';
	if ($(idformulario + ' :input[id = "listar"]').length) {
		var listar = $(idformulario + ' :input[id = "listar"]').val()
	};
	data.done(function(msg) {
		respuesta = msg;
	}).fail(function(xhr, textStatus, errorThrown) {
		respuesta = 'ERROR';
	}).always(function() {
		
		if(respuesta === 'ERROR'){
		}else{
			if (respuesta === 'OK') {
				cerrarModal();
				window.open(rutarecibo, "Voucher deposito ahorro", "width=400, height=500, left=200, top=100");
				if (listar === 'SI') {
					if(typeof entidad2 != 'undefined' && entidad2 !== ''){
						entidad = entidad2;
					}
					buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
				}        
			} else {
				mostrarErrores(respuesta, idformulario, entidad);
			}
		}
	});
}

</script>