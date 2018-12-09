
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($configuraciones, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
<div class="row">
	<div class="col-sm-6 col-xs-6">
		<div class="form-group ">
			{!! Form::label('codigo', 'Codigo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo', 'placeholder' => 'Ingrese codigo...')) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('precio_accion', 'Precio:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('precio_accion', null, array('class' => 'form-control input-xs', 'id' => 'precio_accion', 'placeholder' => 'Ingrese precio')) !!}
			</div>
		</div>


		<div class="form-group ">
			{!! Form::label('ganancia_accion', 'Ganancia por Unidad:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('ganancia_accion', null, array('class' => 'form-control input-xs', 'id' => 'ganancia_accion', 'placeholder' => 'en % cada accion por mes.')) !!}
			</div>
		</div>

		<div class="form-group ">
			{!! Form::label('limite_acciones', 'Limite por Socio:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('limite_acciones', null, array('class' => 'form-control input-xs', 'id' => 'limite_acciones', 'placeholder' => 'en % del total')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => '')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripcion')) !!}
			</div>
		</div>
	</div>

	<div class="col-sm-6 col-xs-6">
		<div class="form-group ">
			{!! Form::label('codigo', 'Codigo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('codigo', null, array('class' => 'form-control input-xs', 'id' => 'codigo', 'placeholder' => 'Ingrese codigo...')) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('tasa_interes_credito', 'Tasa de Interes:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('tasa_interes_credito', null, array('class' => 'form-control input-xs', 'id' => 'tasa_interes_credito', 'placeholder' => 'en % cada credito por mes.')) !!}
			</div>
		</div>


		<div class="form-group ">
			{!! Form::label('tasa_interes_multa', 'Tasa de Interes Multa:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('tasa_interes_multa', null, array('class' => 'form-control input-xs', 'id' => 'tasa_interes_multa', 'placeholder' => 'en % multa por periodo.')) !!}
			</div>
		</div>

		<div class="form-group ">
			{!! Form::label('tasa_interes_ahorro', 'Tasa de Interes Ahorros:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('tasa_interes_ahorro', null, array('class' => 'form-control input-xs', 'id' => 'tasa_interes_ahorro', 'placeholder' => 'en % ahorros')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => '')) !!}
			</div>
		</div>
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripcion')) !!}
			</div>
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
		configurarAnchoModal('800');
	}); 
</script>