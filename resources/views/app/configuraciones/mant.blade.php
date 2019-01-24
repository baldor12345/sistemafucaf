
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($configuraciones, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="row">
	<fieldset class="col-md-6">    	
		<legend>Acciones</legend>
		<div class="panel panel-default">
			<div class="form-group">
				{!! Form::label('codigo', 'Codigo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
				<div class="col-sm-9 col-xs-12">
					{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo', 'placeholder' => 'Ingrese codigo')) !!}
				</div>
			</div>
			<div class="form-group">
				{!! Form::label('precio_accion', 'Precio:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
				<div class="col-sm-9 col-xs-12">
					{!! Form::text('precio_accion', null, array('class' => 'form-control input-xs', 'id' => 'precio_accion', 'placeholder' => 'Ingrese precio',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
				</div>
			</div>
			<div class="form-group ">
				{!! Form::label('limite_acciones', 'Limite:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
				<div class="col-sm-9 col-xs-12">
					{!! Form::text('limite_acciones', null, array('class' => 'form-control input-xs', 'id' => 'limite_acciones', 'placeholder' => 'en % del total',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
				</div>
			</div>
			
		</div>
	</fieldset>	
	
	<fieldset class="col-md-6">    	
		<legend>Crédito</legend>
		<div class="panel panel-default">
			<div class="form-group">
				{!! Form::label('tasa_interes_credito', 'Interes:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
				<div class="col-sm-9 col-xs-12">
					{!! Form::text('tasa_interes_credito', null, array('class' => 'form-control input-xs', 'id' => 'tasa_interes_credito', 'placeholder' => 'en % cada credito por mes.',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
				</div>
			</div>

			<div class="form-group ">
				{!! Form::label('tasa_interes_multa', 'Interes Multa:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
				<div class="col-sm-9 col-xs-12">
					{!! Form::text('tasa_interes_multa', null, array('class' => 'form-control input-xs', 'id' => 'tasa_interes_multa', 'placeholder' => 'en % multa por periodo.',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
				</div>
			</div>
	
			<div class="form-group ">
				{!! Form::label('tasa_interes_ahorro', 'Tasa de Interes Ahorros:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
				<div class="col-sm-9 col-xs-12">
					{!! Form::text('tasa_interes_ahorro', null, array('class' => 'form-control input-xs', 'id' => 'tasa_interes_ahorro', 'placeholder' => 'en % ahorros',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
				</div>
			</div>

		</div>
	</fieldset>
</div>
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripcion')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-6 col-xs-6">
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => '')) !!}
			</div>
		</div>
		<?php
			if($configuraciones != null){
				echo "<input type='hidden' id='fechaTemp' value='".Date::parse($configuraciones->fecha )->format('d/m/Y')."'>";
			}else{
				echo "<input type='hidden' id='fechaTemp' value=''>";
			}
		?>
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
		configurarAnchoModal('800');
	}); 

	var fechaActual = new Date();
	var day = ("0" + fechaActual.getDate()).slice(-2);
	var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
	var fecha_In = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";

	if($('#fechaTemp').val() !== ""){
		// DD/MM/YYYY
		var valoresFecha = $('#fechaTemp').val().split('/');
		//yyy/MM/DD
		var fecha = valoresFecha[2] + "-" + valoresFecha[1] + "-" + valoresFecha[0];
		$('#fecha').val(fecha);
	}else{
		$('#fecha').val(fecha_In);
	}

	//evaluar numeros 
	function filterFloat(evt,input){
		// Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
		var key = window.Event ? evt.which : evt.keyCode;    
		var chark = String.fromCharCode(key);
		var tempValue = input.value+chark;
		if(key >= 48 && key <= 57){
			if(filter(tempValue)=== false){
				return false;
			}else{       
				return true;
			}
		}else{
			if(key == 8 || key == 13 || key == 0) {     
				return true;              
			}else if(key == 46){
					if(filter(tempValue)=== false){
						return false;
					}else{       
						return true;
					}
			}else{
				return false;
			}
		}
	}
	function filter(__val__){
		var preg = /^([0-9]+\.?[0-9]{0,4})$/; 
		if(preg.test(__val__) === true){
			return true;
		}else{
		return false;
		}
		
	}
</script>