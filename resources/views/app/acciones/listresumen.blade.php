@if(count($lista) == 0)
	<h3 class="text-warning">No se encontraron resultados.</h3>
@else
<div style="margin: 0px">
{!! $paginacion or '' !!}
</div>
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
            <td align="center">{{ $value->persona_codigo }}</td>
			<td >{{ $value->persona_apellidos.' '.$value->persona_nombres }}</td>
		</tr>
		<?php
			$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif