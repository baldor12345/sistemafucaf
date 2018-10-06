@php
$documento = NULL;
$observacion = NULL;
$secondtype = 'N';
$comision = "-1";
if (!is_null($cliente)) {
	$documento = $cliente->dni;
	if(is_null($documento) || trim($documento) == ''){
		$documento = $cliente->ruc;
	}
	$observacion = $cliente-> observation;
	$secondtype = $cliente-> secondtype;
	if(!is_null($cliente-> comision)){
		$comision = $cliente-> comision;
	}
}
@endphp
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($cliente, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="form-group col-xs-12">
	{!! Form::label('documento', 'N° Documento:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-4 col-xs-12">
		@if(!is_null($cliente))
			{!! Form::text('documento', $documento, array('class' => 'form-control input-xs', 'id' => 'documento', 'placeholder' => 'Ingrese N° Documento', 'maxlength' => '8')) !!}
		@else
			{!! Form::text('documento', $documento, array('class' => 'form-control input-xs', 'id' => 'documento', 'placeholder' => 'Ingrese N° Documento', 'maxlength' => '8')) !!}
		@endif
	</div>
	{!! Form::label('es_proveedor', 'Es Cliente:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-2 col-xs-12">
		<select id='cbo_esproveedor' name='cbo_esproveedor' class='form-control'>
			<option value='N'>NO</option>
			<option value='S'>SI</option>
		</select>
	</div>
	@php
		echo "<input type='hidden' id='value_proveedor' value='$secondtype'>"
	@endphp
</div>
<div class="form-group col-xs-12">
	{!! Form::label('nombres', 'Nombres:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('nombres', null, array('class' => 'form-control input-xs', 'id' => 'nombres', 'placeholder' => 'Ingrese Nombres', 'disabled' => 'disabled')) !!}
	</div>
</div>
<div class="form-group col-xs-12">
	{!! Form::label('apellidos', 'Apellidos:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('apellidos', null, array('class' => 'form-control input-xs', 'id' => 'apellidos', 'placeholder' => 'Ingrese Apellidos', 'disabled' => 'disabled')) !!}
	</div>
</div>
<!--div class="form-group col-xs-12">
	{!! Form::label('razonsocial', 'Razón Social:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('razonsocial', null, array('class' => 'form-control input-xs', 'id' => 'razonsocial', 'placeholder' => 'Ingrese Razon Social', 'disabled' => 'disabled')) !!}
	</div>
</div-->
<div class="form-group col-xs-12">
	{!! Form::label('celular', 'Cel/Telf:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-5 col-xs-12">
		{!! Form::text('celular', null, array('class' => 'form-control input-xs', 'id' => 'celular', 'placeholder' => 'Ingrese Celular', 'maxlength' => '9')) !!}
	</div>

	<div class="col-sm-4 col-xs-12">
		{!! Form::text('telefono', null, array('class' => 'form-control input-xs', 'id' => 'telefono', 'placeholder' => 'Ingrese Telefono', 'maxlength' => '15')) !!}
	</div>
</div>
<div class="form-group col-xs-12">
	{!! Form::label('fechanacimiento', 'F. Nacimiento:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fechanacimiento', null, array('class' => 'form-control input-xs', 'id' => 'fechanacimiento', 'placeholder' => 'Ingrese Fecha de Nacimiento')) !!}
	</div>
</div>
<div class="form-group col-xs-12">
	{!! Form::label('email', 'E-mail:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::email('email', null, array('class' => 'form-control input-xs', 'id' => 'email', 'placeholder' => 'Ingrese E-mail')) !!}
	</div>
</div>
<div class="form-group col-sm-12">
	{!! Form::label('direccion', 'Direccion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('direccion', null, array('class' => 'form-control input-xs', 'id' => 'direccion', 'placeholder' => 'Ingrese Direccion')) !!}
	</div>
</div>
<div class="form-group col-sm-12">
	{!! Form::label('distrito_id', 'Distrito:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('distrito_id', $cboDistrito, null, array('class' => 'form-control input-xs', 'id' => 'distrito_id')) !!}
	</div>
</div>
<div class="form-group col-sm-12">
	{!! Form::label('observacion', 'Observación:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::textArea('observacion', $observacion, array('class' => 'form-control input-xs', 'id' => 'observacion', 'placeholder' => 'Ingrese Observacion', 'rows' => '3', 'maxlength' => '100')) !!}
	</div>
</div>
<div class="form-group col-sm-12">
	{!! Form::label('comision', 'Comision:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		<select id='cbocomision' name='cbocomision' class='form-control'>
			<option value='-1'>Sin Comision</option>
			<option value='1'>Comision 1</option>
			<option value='2'>Comision 2</option>
			<option value='3'>Comision 3</option>
		</select>
	</div>
	@php
		echo "<input type='hidden' id='value_comision' value='$comision'>"
	@endphp
</div>
<div class="form-group">
	<div class="col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>

{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="documento"]').focus();
		configurarAnchoModal('600');
		$('#documento').on('change', function () {
			if(this.value.length===8){
				$('#nombres').removeAttr('disabled');
				$('#apellidos').removeAttr('disabled');
				//$('#razonsocial').val('');
        		//$('#razonsocial').attr('disabled','disabled');
				$('#nombres').focus();
			}else if(this.value.length===11){
				//$('#razonsocial').removeAttr('disabled');
				$('#nombres').val('');
				$('#apellidos').val('');
				$('#nombres').attr('disabled','disabled');
				$('#apellidos').attr('disabled','disabled');
				//$('#razonsocial').focus();
			}
    	});

		if($('#documento').val()!==""){
			if($('#documento').val().length === 8){
				$('#nombres').removeAttr('disabled');
				$('#apellidos').removeAttr('disabled');
				//$('#razonsocial').attr('disabled','disabled');
			}else{
				//$('#razonsocial').removeAttr('disabled');
				$('#nombres').attr('disabled','disabled');
				$('#apellidos').attr('disabled','disabled');
			}
		}
		$('#cbo_esproveedor').val($('#value_proveedor').val());
		$('#cbocomision').val($('#value_comision').val());
	}); 
</script>