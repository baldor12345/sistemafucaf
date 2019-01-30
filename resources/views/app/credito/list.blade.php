@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif >{!! $value['valor'] !!}</th>
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
				<td>{{ $value->dni }}</td>
				<td>{{ $value->nombres." ".$value->apellidos}} </td>
				<td>{{ round($value->valor_credito,1) }}</td>
				<td>{{ $value->periodo }}</td>
				@if ($value->estado === '0')
				<td>Pendiente</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Ver detalle', array('onclick' => 'modal (\''.URL::route($ruta["detallecredito"], array($value->credito_id, 'SI')).'\', \'Detalle de credito\', this);','class' => 'btn btn-xs btn-warning btndetcredito')) !!}</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->credito_id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Acción', array('onclick' => 'modal (\''.URL::route($ruta["vistaaccion"], array($value->credito_id, 'SI')).'\', \''."Realizar una operación".'\', this);', 'class' => 'btn btn-xs btn-light')) !!}</td>
				@else
				<td>Cancelado</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Ver detalle', array('onclick' => 'modal (\''.URL::route($ruta["detallecredito"], array($value->credito_id, 'SI')).'\', \'Detalle de credito\', this);','class' => 'btn btn-xs btn-warning btndetcredito')) !!}</td>
				<td>X</td>
				<td>X</td>
				@endif
			</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table
@endif
