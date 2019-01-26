<div class="row">
	<div class="card-box table-responsive">
		{!! Form::open(['route' => $ruta["buscaraccion"], 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaAccion1']) !!}
			{!! Form::hidden('page', 1, array('id' => 'page')) !!}
			{!! Form::hidden('idcaja', $idcaja, array('id' => 'idcaja')) !!}
			{!! Form::hidden('persona_id', $persona_id, array('id' => 'persona_id')) !!}
			{!! Form::hidden('filas', 10, array('id' => 'idcaja','onchange' => 'buscar(\''.$entidad.'\')')) !!}
			{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
		{!! Form::close() !!}
		<div id="listado{{ $entidad }}"></div>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('850');
	buscar("{{ $entidad }}");
	init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
	
}); 
</script>
