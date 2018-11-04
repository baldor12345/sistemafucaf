<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($ahorros, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="form-group">
		<div id='txtcliente' class="form-group col-6 col-md-6 col-sm-6">
			{!! Form::label('dnicl', 'DNI del Cliente: *', array('class' => '')) !!}
			{!! Form::text('dnicl', null, array('class' => 'form-control input-xs', 'id' => 'dnicl', 'placeholder' => 'Ingrese el DNI del cliente')) !!}
			<p id="nombrescl" class="" >DNI Cliente Vacio</p>
			<input type="hidden" id="id_cliente" name="id_cliente" value="" >
		</div>

		<div class="col-lg-6 col-md-6 col-sm-6">
			{!! Form::label('importe', 'Importe S/.: *', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			{!! Form::text('importe', null, array('class' => 'form-control input-xs', 'id' => 'importe', 'placeholder' => 'Ingrese el monto de ahorro')) !!}
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6">
			{!! Form::label('interes', 'Interes mensual (%): *', array('class' => 'col-lg-3 col-md-3 col-sm-3 control-label')) !!}
			{!! Form::text('interes', null, array('class' => 'form-control input-xs', 'id' => 'interes', 'placeholder' => 'Interes mensual')) !!}
		</div>

		<div class="form-group col-6 col-md-6 col-sm-6">
			{!! Form::label('periodo', 'Periodo (N° Meses): *', array('class' => '')) !!}
			{!! Form::text('periodo', null, array('class' => 'form-control input-xs', 'id' => 'periodo', 'placeholder' => 'Ingrese Numero de meses')) !!}
		</div>
	
		<div class="form-group col-6 col-md-6 col-sm-6" >
			{!! Form::label('fecha_inicio', 'Fecha de inicio: *', array('class' => '')) !!}
			{!! Form::date('fecha_inicio', null, array('class' => 'form-control input-xs', 'id' => 'fecha_inicio')) !!}
		</div>
		<div class="form-group">
			{!! Form::label('concepto', 'Concepto:', array('class' => 'input-sm')) !!}
			{!! Form::select('concepto', $cboConcepto, null, array('class' => 'form-control input-sm', 'id' => 'concepto')) !!}
		</div>
		<div class="form-group col-12" >
			{!! Form::label('descripcion', 'Descripción: ', array('class' => '')) !!}
			{!! Form::textarea('descripcion', null, array('class' => 'form-control input-sm','rows' => 4, 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción')) !!}
		</div>
	</div>
	
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('650');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
}); 
</script>