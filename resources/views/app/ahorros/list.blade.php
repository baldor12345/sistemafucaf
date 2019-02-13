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
		$tasainteresahorro = $configuraciones->tasa_interes_ahorro;

		?>
		@foreach ($lista as $key => $value)
		
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->dni }}</td>
			<td>{{ $value->nombres." ".$value->apellidos}} </td>
			<td>{{ round($value->capital,1) }}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> DETALLE', array('onclick' => 'modal (\''.URL::route($ruta["vistadetalleahorro"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_vistadetalleahorro.'\', this);','class' => 'btn btn-xs btn-light')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> HISTORICO', array('onclick' => 'modal (\''.URL::route($ruta["vistahistoricoahorro"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_vistahistoricoahorro.'\', this);','class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> RETIRAR', array('onclick' => 'modal (\''.URL::route($ruta["vistaretiro"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_vistaretiro.'\', this);','class' => 'btn btn-xs btn-success')) !!}</td>
		</tr>

		<?php
		$contador = $contador + 1;
		?>

		@endforeach
	</tbody>
</table>

@endif

