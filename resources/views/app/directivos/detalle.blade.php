
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open([null, 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => '']) !!}
<div class="row">
    {!! Form::label('codigo', 'PRESIDENTE:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
	@if($presidente != null)
	{!! Form::label('codigo', $presidente->apellidos.'  '.$presidente->nombres, array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@else
	{!! Form::label('codigo', '--', array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@endif
</div>
</br>
<div class="row">
    {!! Form::label('codigo', 'SECRETARIO:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
	@if($secretario != null)
	{!! Form::label('codigo', $secretario->apellidos.'  '.$secretario->nombres, array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@else
	{!! Form::label('codigo', '--', array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@endif
</div>
</br>
<div class="row">
    {!! Form::label('codigo', 'TESORERO:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
	@if($tesorero != null)
	{!! Form::label('codigo', $tesorero->apellidos.'  '.$tesorero->nombres, array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@else
	{!! Form::label('codigo', '--', array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@endif
</div>
</br>
<div class="row">
    {!! Form::label('codigo', 'VOCAL:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
	@if($vocal != null)
	{!! Form::label('codigo', $vocal->apellidos.'  '.$vocal->nombres, array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@else
	{!! Form::label('codigo', '--', array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@endif
</div>
</br>
<div class="row">
    {!! Form::label('codigo', 'ESTADO:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
	@if($estado == 'A')
	{!! Form::label('codigo', 'Activo', array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@else
	{!! Form::label('codigo', 'Inactivo', array('class' => 'col-sm-8 col-xs-12 control-label')) !!}
	@endif
</div>
</br>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-rigth">
        &nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('550');
	}); 
	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});
</script>