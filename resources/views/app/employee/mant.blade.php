<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($employee, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<?php  
$date = null;
if ($employee !== NULL) {
	$date = date('d/m/Y',strtotime($employee->birthdate));
}
?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group">
			{!! Form::label('workertype_id', 'Tipo de trabajador:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::select('workertype_id', $cboWorkertype, NULL, array('class' => 'form-control input-xs', 'id' => 'workertype_id')) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group">
			{!! Form::label('lastname', 'Apellidos:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('lastname', null, array('class' => 'form-control input-xs', 'id' => 'lastname', 'placeholder' => 'Ingrese apellidos')) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group">
			{!! Form::label('firstname', 'Nombre:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('firstname', null, array('class' => 'form-control input-xs', 'id' => 'firstname', 'placeholder' => 'Ingrese nombre')) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group">
			{!! Form::label('departamento_id', 'Distrito:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::select('departamento_id', $cboDepartamento, $departamento->id, array('style' => 'display: inline; width: 30%;', 'class' => 'form-control input-xs', 'id' => 'departamento_id', 'onchange' => 'mostrarProvincias(\''.URL::route('provincia.cboprovincia').'\',\''.$entidad.'\', \'M\')')) !!}
				{!! Form::select('provincia_id', $cboProvincia, $provincia->id, array('style' => 'display: inline; width: 30%;', 'class' => 'form-control input-xs', 'id' => 'provincia_id', 'onchange' => 'mostrarDistritos(\''.URL::route('distrito.cbodistrito').'\',\''.$entidad.'\', \'M\')')) !!}
				{!! Form::select('distrito_id', $cboDistrito, $distrito->id, array('style' => 'display: inline; width: 38%;', 'class' => 'form-control input-xs', 'id' => 'distrito_id')) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="form-group">
			{!! Form::label('address', 'Dirección:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('address', null, array('class' => 'form-control input-xs', 'id' => 'address', 'placeholder' => 'Ingrese dirección')) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-5 col-md-5 col-sm-5">
		<div class="form-group">
			{!! Form::label('dni', 'DNI:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('dni', null, array('class' => 'form-control input-xs', 'id' => 'dni', 'placeholder' => 'Ingrese DNI', 'maxlength' => '8')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7">
		<div class="form-group">
			{!! Form::label('ruc', 'RUC:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('ruc', null, array('class' => 'form-control input-xs', 'id' => 'ruc', 'placeholder' => 'Ingrese RUC', 'maxlength' => '11')) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-5 col-md-5 col-sm-5">
		<div class="form-group">
			{!! Form::label('phonenumber', 'Teléfono:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('phonenumber', null, array('class' => 'form-control input-xs', 'id' => 'phonenumber', 'placeholder' => 'Ingrese telefono')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7">
		<div class="form-group">
			{!! Form::label('cellnumber', 'Celular:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				{!! Form::text('cellnumber', null, array('class' => 'form-control input-xs', 'id' => 'cellnumber', 'placeholder' => 'Ingrese celular')) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-5 col-md-5 col-sm-5">
		<div class="form-group">
			{!! Form::label('email', 'Email:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
			<div class="col-lg-8 col-md-8 col-sm-8">
				{!! Form::text('email', null, array('class' => 'form-control input-xs', 'id' => 'email', 'placeholder' => 'Ingrese email')) !!}
			</div>
		</div>
	</div>
	<div class="col-lg-7 col-md-7 col-sm-7">
		<div class="form-group">
			{!! Form::label('birthdate', 'Fecha nacimiento:', array('class' => 'col-lg-5 col-md-5 col-sm-5 control-label')) !!}
			<div class="col-lg-7 col-md-7 col-sm-7">
				<div class='input-group input-group-xs' id='divfechanacimiento'>
					{!! Form::text('birthdate', $date, array('class' => 'form-control input-xs', 'id' => 'birthdate', 'placeholder' => 'Ingrese fecha de nacimiento')) !!}
					<span class="input-group-btn">
						<button class="btn btn-default calendar">
							<i class="glyphicon glyphicon-calendar"></i>
						</button>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="form-group">
	{!! Form::label('observation', 'Observación:', array('class' => 'col-lg-4 col-md-4 col-sm-4 control-label')) !!}
	<div class="col-lg-8 col-md-8 col-sm-8">
		{!! Form::textarea('observation', null, array('style' => 'resize: none;', 'rows' => '3','class' => 'form-control input-xs', 'id' => 'observation', 'placeholder' => 'Ingrese observacion')) !!}
	</div>
</div>
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar', 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="workertype_id"]').focus();
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dni"]').inputmask("99999999");
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="ruc"]').inputmask("99999999999");
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="phonenumber"]').inputmask('Regex', { regex: "[0-9]+-[0-9]+" });
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="cellnumber"]').inputmask('Regex', { regex: "[*]?[#]?[0-9]+-[0-9]+" });
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="birthdate"]').inputmask("dd/mm/yyyy");
		
		jQuery('#birthdate').datepicker({
	        autoclose: true,
	        todayHighlight: true,
	        format: "dd/mm/yyyy",
	    });
		configurarAnchoModal ('700');
	}); 

</script>