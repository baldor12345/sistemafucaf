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
			<td>{{ date("d/m/Y",strtotime($value->fecha_deposito)) }}</td>
			<td>{{ ($value->fecha_retiro != "")?date("d/m/Y",strtotime($value->fecha_retiro)):""}}</td>
			<td>{{ ($value->estado=='P'?'Pendiente':'Retirado') }}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->ahorros_id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);','class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->ahorros_id, 'listar'=>'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Ver', array('onclick' => 'modal (\''.URL::route($ruta["verahorro"], array($value->ahorros_id, 'listar'=>'SI')).'\', \''.$titulo_verahorro.'\', this);','class' => 'btn btn-xs btn-warning')) !!}</td>
		</tr>

		<?php
		$contador = $contador + 1;
		?>

		@endforeach
	</tbody>
</table>

@endif

