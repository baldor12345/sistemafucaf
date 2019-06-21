
<div id="divMensajeError{!! $entidad !!}"></div>
<div id="info2"></div>
{!! Form::model($persona, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="row">
	<div class="form-group">
		<div class="col-sm-6 col-xs-12">
			{!! Form::label('codigo', 'Codigo:*', array('class' => 'control-label')) !!}
			@if($persona !== null)
			{!! Form::text('codigo', null , array('class' => 'form-control input-xs', 'id' => 'codigo', 'placeholder' => 'Ingrese Codigo', 'maxlength' => '30','readonly')) !!}
			@else

			{!! Form::text('codigo', $codigo_generado , array('class' => 'form-control input-xs', 'id' => 'codigo', 'placeholder' => 'Ingrese Codigo', 'maxlength' => '30','readonly')) !!}
			@endIf
		</div>
		<div class="col-sm-6 col-xs-12">
			{!! Form::label('dni', 'DNI:*', array('class' => 'control-label')) !!}
			{!! Form::text('dni', null, array('class' => 'form-control input-xs input-number', 'id' => 'dni', 'placeholder' => 'Ingrese DNI', 'maxlength' => '8')) !!}
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<div class="col-sm-6 ">
				{!! Form::label('nombres', 'Nombres:*', array('class' => '')) !!}
				{!! Form::text('nombres', null, array('class' => 'form-control input-xs', 'id' => 'nombres', 'placeholder' => 'Ingrese Nombres', 'maxlength' => '50')) !!}
		</div>
		<div class="col-sm-6 " >
				{!! Form::label('apellidos', 'Apellidos:*', array('class' => '')) !!}
				{!! Form::text('apellidos', null, array('class' => 'form-control input-xs', 'id' => 'apellidos', 'placeholder' => 'Ingrese Apellidos', 'maxlength' => '50')) !!}
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<div class="col-sm-4">
			{!! Form::label('fecha_nacimiento', 'Fecha de Nacimiento:*', array('class' => '',  'id' => 'datosApoderado')) !!}
			{!! Form::date('fecha_nacimiento', null, array('class' => 'form-control input-xs fech', 'id' => 'fecha_nacimiento' , 'placeholder' => '','onchange'=>'evaluarFecha();')) !!}
		</div>
		<div class="col-sm-4">
			{!! Form::label('sexo', 'Sexo:*', array('class' => '')) !!}
			{!! Form::select('sexo', $cboSexo, null, array('class' => 'form-control input-xs', 'id' => 'sexo')) !!}
		</div>
		<div class="col-sm-4">
			{!! Form::label('estado_civil', 'Estado Civil:*', array('class' => '')) !!}
			{!! Form::select('estado_civil', $cboEstadoCivil, null, array('class' => 'form-control input-xs', 'id' => 'estado_civil')) !!}
		</div>
	</div>
<?php
	if($persona != null){
		echo "<input type='hidden' id='fechaTempNac' value='".Date::parse($persona->fecha_nacimiento )->format('d/m/Y')."'>";
		echo "<input type='hidden' id='fechaTempIni' value='".Date::parse($persona->fechai)->format('d/m/Y')."'>";
	}else{
		echo "<input type='hidden' id='fechaTempNac' value=''>";
		echo "<input type='hidden' id='fechaTempIni' value=''>";
	}
?>
</div>


<div class="row">
    <div class="form-group">
		<div class="col-sm-6">
				{!! Form::label('personas_en_casa', 'Personas en casa:', array('class' => '')) !!}
				{!! Form::number('personas_en_casa', null, array('class' => 'form-control input-xs ', 'id' => 'personas_en_casa', 'placeholder' => '', 'min'=>'1', 'max'=>'20')) !!}
		</div>
		<div class="col-sm-6" >
				{!! Form::label('direccion', 'Direccion:*', array('class' => '')) !!}
				{!! Form::text('direccion', null, array('class' => 'form-control input-xs', 'id' => 'direccion', 'placeholder' => 'Ingrese direccion')) !!}
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<div class="col-sm-6">
				{!! Form::label('ocupacion', 'Ocupacion:', array('class' => '')) !!}
				{!! Form::text('ocupacion', null, array('class' => 'form-control input-xs', 'id' => 'ocupacion', 'placeholder' => 'Ingrese ocupacion')) !!}
		</div>
		<div class="col-sm-6" >
				{!! Form::label('email', 'E-mail:', array('class' => '')) !!}
				{!! Form::text('email', null, array('class' => 'form-control input-xs', 'id' => 'email', 'placeholder' => 'Ingrese email')) !!}
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<div class=" col-sm-4">
				{!! Form::label('telefono_fijo', 'Telefono fijo:', array('class' => '')) !!}
				{!! Form::text('telefono_fijo', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_fijo', 'placeholder' => 'Ingrese telefono fijo', 'maxlength' => '15')) !!}
		</div>
		<div class=" col-sm-4" >
				{!! Form::label('telefono_movil1', 'Telefono movil 1:', array('class' => '')) !!}
				{!! Form::text('telefono_movil1', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_movil1', 'placeholder' => 'Ingrese telefono movil', 'maxlength' => '15' )) !!}
		</div>
		<div class=" col-sm-4" >
				{!! Form::label('telefono_movil2', 'Telefono movil 2:', array('class' => '')) !!}
				{!! Form::text('telefono_movil2', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_movil2', 'placeholder' => 'Ingrese telefono movil', 'maxlength' => '15')) !!}
		</div>
	</div>
</div>



<div class="row">
	<div class="form-group">
		<div class=" col-sm-4">
			{!! Form::label('ingreso_personal', 'Ingreso personal:*', array('class' => '')) !!}
			{!! Form::text('ingreso_personal', null, array('class' => 'form-control input-xs ', 'id' => 'ingreso_personal',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
		</div>
		<div class=" col-sm-4" >
			{!! Form::label('ingreso_familiar', 'Ingreso familiar:*', array('class' => '')) !!}
			{!! Form::text('ingreso_familiar', null, array('class' => 'form-control input-xs ', 'id' => 'ingreso_familiar',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
		</div>
		<div class=" col-sm-4" >
			{!! Form::label('estado', 'Estado:*', array('class' => 'input-sm')) !!}
			{!! Form::select('estado', $cboEstado, null, array('class' => 'form-control input-sm', 'id' => 'estado')) !!}
		</div>
	</div>
</div>



<div class="row">
    <div class="form-group">
		<div class="col-sm-6">
				{!! Form::label('tipo', 'Tipo:*', array('class' => 'input-sm')) !!}
				{!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-sm', 'id' => 'tipo')) !!}
		</div>
		<div class="col-sm-6" >
				{!! Form::label('fechai', 'Fecha de Inicio:*', array('class' => '')) !!}
				{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
		</div>
	</div>
</div>


<div class="form-group" id='oculto' style="display:none;">
	<legend>Datos del Apoderado:</legend>
	<div class="col-lg-12 col-md-12 col-sm-12">
		{!! Form::label('apoderado_id', 'Apoderado:', array('class' => '')) !!}
		{!! Form::select('apoderado_id', $cboPers, null, array('class' => ' input-sm', 'id' => 'apoderado_id')) !!}
	</div>
</div>


<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarpersona', 'onclick' => 'guardarpersona(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('650');
		evaluarFecha();

		$('#apoderado_id').select2({
			dropdownParent: $("#modal"+(contadorModal-1)),
			
			minimumInputLenght: 2,
			ajax: {
				
				url: "{{ URL::route($ruta['listpersonas'], array()) }}",
				dataType: 'json',
				delay: 250,
				data: function(params){
					return{
						q: $.trim(params.term)
					};
				},
				processResults: function(data){
					return{
						results: data
					};
				}
				
			}
		});

	}); 

	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	function evaluarFecha(){
		//fecha actual
		if($('#fecha_nacimiento').val() !== ""){
			var fechaActual = new Date();
			var añoActual = fechaActual.getFullYear();
			var mesActual = fechaActual.getMonth();
			//fecha obtenida del formulario
			var valoresFechaSel = $('#fecha_nacimiento').val().split('-');
			if((añoActual-valoresFechaSel[0])<=18 ){
				console.log("es menor de edad= "+(añoActual-valoresFechaSel[0]));
				document.getElementById('oculto').style.display = 'block';
			}else{
				document.getElementById('oculto').style.display = 'none';
			}
		}else{
			
		}
		
	}

		

	var fechaActual = new Date();
	var day = ("0" + fechaActual.getDate()).slice(-2);
	var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
	var fecha_nac = "1980" +"-"+month+"-"+day+"";
	var fecha_In = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";

	if($('#fechaTempNac').val() !== ""){
		// DD/MM/YYYY
		var valoresFechaNac = $('#fechaTempNac').val().split('/');
		var valoresFechaIni = $('#fechaTempIni').val().split('/');
		//yyy/MM/DD
		var fechaNac = valoresFechaNac[2] + "-" + valoresFechaNac[1] + "-" + valoresFechaNac[0];
		var fechaIni = valoresFechaIni[2] + "-" + valoresFechaIni[1] + "-" + valoresFechaIni[0];
		$('#fecha_nacimiento').val(fechaNac);
		$('#fechai').val(fechaIni);
	}else{
		$('#fecha_nacimiento').val(fecha_nac);
		$('#fechai').val(fecha_In);
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
		var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
		if(preg.test(__val__) === true){
			return true;
		}else{
		return false;
		}
		
	}
	
	function guardarpersona(entidad){
		var acciones = '{{$acciones}}';
		var ahorros = '{{$ahorros}}';
		var credito = '{{$credito}}';
		var estado = $('#estado').val();
		var tipo = $('#tipo').val();


		var $fecha_n = $('#fecha_nacimiento').val();
		var year_eval = $fecha_n.split('-');
		var date_now = new Date();
		var  year_now = date_now.getFullYear();

		if(estado == 'A'){
			if(tipo.trim() == 'C'){
				if(acciones == 0){
					if((year_now-year_eval[0])>=18){
						$('#info2').hide();
						guardar(entidad)
						console.log("entra aca a y c");
					}else{
						var apoderado = $('#apoderado_id').val();
						if(apoderado != ''){
							$('#info2').hide();
							guardar(entidad)
							console.log("entra aca a y s");
						}else{
							document.getElementById("info2").innerHTML = "<div class='alert alert-danger' role='alert'><span >por favor seleccione apoderado, el siguiente socio o cliente ingresado es menor de edad!</span></div>";
							$('#info2').show();
						}
					}
					
				}else{
					document.getElementById("info2").innerHTML = "<div class='alert alert-danger' role='alert'><span >accion no pudo ser completada, socio tiene acciones activas, gracias!</span></div>";
					$('#info2').show();
				}
			}
			if(tipo.trim() == 'S'){
				if((year_now-year_eval[0])>=18){
					$('#info2').hide();
					guardar(entidad)
					console.log("entra aca s y mayor");
				}else{
					var apoderado2 = $('#apoderado_id').val();
					if(apoderado2 != ''){
						$('#info2').hide();
						console.log(guardar(entidad));
						console.log("entra aca s y menor");
					}else{
						document.getElementById("info2").innerHTML = "<div class='alert alert-danger' role='alert'><span >por favor seleccione apoderado, el siguiente socio o cliente ingresado es menor de edad!</span></div>";
						$('#info2').show();
					}
				}
			}
			
		}
		if(estado == 'I'){
			console.log("acciones: "+acciones+" ahorros "+ahorros+" credito "+credito);
			if((acciones == 0) && (ahorros == 0) && (credito == 0)){
				$('#info2').hide();
				guardar(entidad);
			}else{
				document.getElementById("info2").innerHTML = "<div class='alert alert-danger' role='alert'><span >accion no pudo ser completada, socio o cliente tiene credito, ahorros o acciones activas, gracias!</span></div>";
					$('#info2').show();
			}
		}
	}
</script>