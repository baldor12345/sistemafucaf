@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->persona_dni }}</td>
			<td>{{ $value->persona_nombres.'  '.$value->persona_apellidos }}</td>
			<td>{{ $value->cantidad_accion_comprada }}</td>
			<td>{{ $value->precio_accion }}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-list"></div> ver detalle', array('onclick' => 'modal(\''.URL::route($ruta["listacciones"], $value->persona_id).'\');', 'class' => 'btn btn-block btn-xs')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table
@endif