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
			
			<td>{{ $value->nombres}} </td>
			<td>{{ $value->valor_credito }}</td>
			<td>{{ $value->cantidad_cuotas }}</td>
			@if ($value->estado === '0')
			<td>Pendiente</td>
			@else
			<td>Cancelado</td>
			@endif
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Detalle', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
		</tr>

		<?php
		$contador = $contador + 1;
		?>

		@endforeach
	</tbody>
</table
@endif