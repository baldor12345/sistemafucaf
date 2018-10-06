
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($persona, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('codigo', 'Codigo:', array('class' => '')) !!}
			{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo', 'placeholder' => 'Ingrese Codigo')) !!}

    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('dni', 'DNI:', array('class' => '')) !!}
			{!! Form::text('dni', null, array('class' => 'form-control input-xs input-number', 'id' => 'dni', 'placeholder' => 'Ingrese DNI', 'maxlength' => '8')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('nombres', 'Nombres:', array('class' => '')) !!}
			{!! Form::text('nombres', null, array('class' => 'form-control input-xs', 'id' => 'nombres', 'placeholder' => 'Ingrese Nombres')) !!}

    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('apellidos', 'Apellidos:', array('class' => '')) !!}
			{!! Form::text('apellidos', null, array('class' => 'form-control input-xs', 'id' => 'apellidos', 'placeholder' => 'Ingrese Apellidos')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4 col-sm-4">
			{!! Form::label('fecha_nacimiento', 'Fecha de Nacimiento:', array('class' => '',  'id' => 'datosApoderado')) !!}
			{!! Form::date('fecha_nacimiento', null, array('class' => 'form-control input-xs fech', 'id' => 'fecha_nacimiento' , 'placeholder' => '','onchange'=>'evaluarFecha();')) !!}
    </div>
    <div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('sexo', 'Sexo:', array('class' => '')) !!}
			{!! Form::select('sexo', $cboSexo, null, array('class' => 'form-control input-xs', 'id' => 'sexo')) !!}
    </div>
		<div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('estado_civil', 'Estado Civil:', array('class' => '')) !!}
			{!! Form::select('estado_civil', $cboEstadoCivil, null, array('class' => 'form-control input-xs', 'id' => 'estado_civil')) !!}
    </div>
</div>

</div>





<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('personas_en_casa', 'Personas en casa:', array('class' => '')) !!}
			{!! Form::number('personas_en_casa', null, array('class' => 'form-control input-xs', 'id' => 'personas_en_casa', 'placeholder' => '')) !!}
    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('direccion', 'Direccion:', array('class' => '')) !!}
			{!! Form::text('direccion', null, array('class' => 'form-control input-xs', 'id' => 'direccion', 'placeholder' => 'Ingrese direccion')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('ocupacion', 'Ocupacion:', array('class' => '')) !!}
			{!! Form::text('ocupacion', null, array('class' => 'form-control input-xs', 'id' => 'ocupacion', 'placeholder' => 'Ingrese ocupacion')) !!}
    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('email', 'E-mail:', array('class' => '')) !!}
			{!! Form::text('email', null, array('class' => 'form-control input-xs', 'id' => 'email', 'placeholder' => 'Ingrese email')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4 col-sm-4">
			{!! Form::label('telefono_fijo', 'Telefono fijo:', array('class' => '')) !!}
			{!! Form::text('telefono_fijo', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_fijo', 'placeholder' => 'Ingrese telefono fijo', 'maxlength' => '15')) !!}
    </div>
    <div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('telefono_movil1', 'Telefono movil 1:', array('class' => '')) !!}
			{!! Form::text('telefono_movil1', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_movil1', 'placeholder' => 'Ingrese telefono movil', 'maxlength' => '15' )) !!}
    </div>
	<div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('telefono_movil2', 'Telefono movil 2:', array('class' => '')) !!}
			{!! Form::text('telefono_movil2', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_movil2', 'placeholder' => 'Ingrese telefono movil', 'maxlength' => '15')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
		{!! Form::label('ingreso_personal', 'Ingreso personal:', array('class' => '')) !!}
		{!! Form::text('ingreso_personal', null, array('class' => 'form-control input-xs', 'id' => 'ingreso_personal', 'placeholder' => '')) !!}

    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
		{!! Form::label('ingreso_familiar', 'Ingreso familiar:', array('class' => '')) !!}
		{!! Form::text('ingreso_familiar', null, array('class' => 'form-control input-xs', 'id' => 'ingreso_familiar', 'placeholder' => '')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('tipo', 'Tipo:', array('class' => 'input-sm')) !!}
			{!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-sm', 'id' => 'tipo')) !!}
    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('fechai', 'Fecha de Inicio:', array('class' => '')) !!}
			{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
    </div>
</div>


<div class="form-row" id='oculto' style="display:none;">
		<legend>Datos del Apoderado:</legend>
		<div class="form-row">
			{!! Form::label('nombres_apoderado', 'Nombres y Apellidos del apoderado:', array('class' => '')) !!}
			{!! Form::text('nombres_apoderado', null, array('class' => 'form-control input-xs', 'id' => 'nombres_apoderado', 'placeholder' => 'Ingrese Nombres y apellidos del apoderado...')) !!}
		</div>

		<div class="form-row">
			<div class="form-group col-md-3 col-sm-3">
					{!! Form::label('dni_apoderado', 'DNI', array('class' => '')) !!}
					{!! Form::text('dni_apoderado', null, array('class' => 'form-control input-xs input-number', 'id' => 'dni_apoderado', 'placeholder' => 'Ingrese DNI', 'maxlength' => '8')) !!}
			</div>
			<div class="form-group col-md-3 col-sm-3" style="margin-left: 12.5px;">
					{!! Form::label('telefono_fijo_apoderado', 'Telefono', array('class' => '')) !!}
					{!! Form::text('telefono_fijo_apoderado', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_fijo_apoderado', 'placeholder' => 'Ingrese telefono', 'maxlength' => '15')) !!}
			</div>
			<div class="form-group col-md-6 col-sm-6" style="margin-left: 12.5px;">
				{!! Form::label('direccion_apoderado', 'Direccion ', array('class' => '')) !!}
				{!! Form::text('direccion_apoderado', null, array('class' => 'form-control input-xs', 'id' => 'direccion_apoderado', 'placeholder' => 'Ingrese direccion')) !!}
			</div>
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
		configurarAnchoModal('650');
		evaluarFecha();
		
	}); 
	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	function evaluarFecha(){
		//fecha actual
		var fechaActual = new Date();
		var añoActual = fechaActual.getFullYear();
		var mesActual = fechaActual.getMonth();


		//fecha obtenida del formulario
		var valoresFechaSel = $('#fecha_nacimiento').val().split('-');
		if((añoActual-valoresFechaSel[0])<=18 ){
			console.log("es menor de edad= "+(añoActual-valoresFechaSel[0]));
			$('#datosApoderado').toggle( 
				function(e){ 
					$('#oculto').slideDown();
					$(this).text('Fecha de Nacimiento:');
				}, // Separamos las dos funciones con una coma
			);
			
		}
		/*
		else{
			$('#datosApoderado').toggle( 
				function(e){ 
					$('#oculto').slideUp();
					$(this).text('Fecha de Nacimiento:');
					e.preventDefault();
				}	// Separamos las dos funciones con una coma
			);
		}
		*/

	}
	
	



</script>