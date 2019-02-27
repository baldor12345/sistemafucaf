<div class="row">
	<div class="card-box table-responsive">
		{!! Form::open(['route' => $ruta["buscarresumen"], 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaAccion3']) !!}
			{!! Form::hidden('page', 1, array('id' => 'page')) !!}
			{!! Form::hidden('fecha', $caja[0]->fecha_horaapert, array('id' => 'fecha')) !!}
			{!! Form::hidden('filas', 6, array('id' => 'idcaja','onchange' => 'buscar(\''.$entidad.'\')')) !!}
			{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
		{!! Form::close() !!}
		<div id="listado{{ $entidad }}"></div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('500');
	buscar("{{ $entidad }}");
	init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
	
}); 
</script>
