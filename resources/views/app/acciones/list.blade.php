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
			<td>{{ $value->persona_apellidos.'  '.$value->persona_nombres }}</td>
			<td align="center" ><strong>{{ $value->cantidad_accion_comprada }}</strong></td>
			<td>{{ $value->precio_accion }}</td>
			<td>{!! Form::button('<div class="glyphicon  glyphicon-list"></div> Ver Historial', array('onclick' => 'modal (\''.URL::route($ruta["listacciones"], array($value->persona_id, 'listar'=>'SI')).'\', \''.'Lista de acciones del socio: '.$value->persona_apellidos.' '.$value->persona_nombres.'\'   , this);', 'class' => 'btn  btn-xs btn-success')) !!}</td>
			@if($idcaja == 0)
			<td>{!! Form::button('<div class="glyphicon  glyphicon-transfer"></div> vender acciones', array('onclick' => 'modal (\''.URL::route($ruta["cargarventa"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_ventaaccion.'\', this);', 'class' => 'btn  btn-xs btn-info', 'disabled' =>'true')) !!}</td>
			@else
			<td>{!! Form::button('<div class="glyphicon  glyphicon-transfer"></div> vender acciones', array('onclick' => 'modal (\''.URL::route($ruta["cargarventa"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_ventaaccion.'\', this);', 'class' => 'btn  btn-xs btn-info')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table
@endif