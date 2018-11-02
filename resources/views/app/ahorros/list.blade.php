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
			
			<td>{{ $value->nombres." ".$value->apellidos}} </td>
			<td>{{ $value->importe }}</td>
			<td>{{ $value->periodo }}</td>
			<td>{{ $value->fecha_inicio }}</td>
			<td>{{ $value->fecha_fin }}</td>
			<!--<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Ver detalle', array('onclick' => 'modal (\''.URL::route($ruta["detallecredito"], array($value->credito_id, 'SI')).'\', \'Detalle de credito\', this);','class' => 'btn btn-xs btn-warning btndetcredito', "idcred"=>$value->credito_id)) !!}</td>-->
		</tr>

		<?php
		$contador = $contador + 1;
		?>

		@endforeach
	</tbody>
</table>
@endif

