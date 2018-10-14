

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
			<td>{{ $value->titulo }}</td>	
			<td>{{ Date::parse($value->fecha )->format('d/m/Y')  }}</td>
			<td>{{ $value->hora_apertura }}</td>
			<td>{{ $value->hora_cierre }}</td>
			<td>{{ $value->monto_iniciado }}</td>
			<td>{{ $value->monto_cierre }}</td>
			<td>{{ $value->diferencia_monto }}</td>
			@if ($value->estado === 'A')
			<td id="abierto" >Abierto</td>
			@else
			<td id="cerrado" >Cerrado</td>
			@endif
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-star-empty"></div> Cierre de Caja', array('onclick' => 'modal (\''.URL::route($ruta["cargarCaja"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_cerrarCaja.'\', this);', 'class' => 'btn btn-xs btn-secondary')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table
@endif