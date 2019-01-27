@if(count($lista) == 0)
	<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
			<th style="font-size: 13px" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
			$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td style="font-size: 13px">{{ $contador }}</td>
			<td style="font-size: 13px">{{ $month[intval(Date::parse($value->fecha )->format('m'))].' - '.Date::parse($value->fecha )->format('Y')  }}</td>
			<td style="font-size: 13px">{{ number_format($value->monto,1) }}</td>
			<td style="font-size: 13px">{{ $value->concepto->titulo }}</td>
			@if ($value->concepto->tipo === 'I')
			<td style="color:green;font-weight: bold;" >Ingreso</td>
			@else
			<td style="color:red;font-weight: bold;" >Egreso</td>
			@endif
			@if ($value->persona != null)
			<td style="font-size: 13px">{{ $value->persona->nombres.' '.$value->persona->apellidos }}</td>
			@else
			<td style="font-size: 13px"> - - -</td>
			@endif
			<td style="font-size: 13px">{{ $value->descripcion }}</td>
		</tr>
		<?php
			$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif