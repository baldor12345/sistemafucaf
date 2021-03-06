@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else

{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			@foreach($cabecera as $key => $value)
				<th  @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
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
			<td align="center">{{ $value->persona_dni }}</td>
			<td align="center">{{ $value->persona_codigo }}</td>
			<td>{{ $value->persona_apellidos.'  '.$value->persona_nombres }}</td>
			@if($idcaja == 0)
			<td align="center">{!! Form::button('<div class="glyphicon  glyphicon-plus"></div> COMPRAR', array('onclick' => 'modal (\''.URL::route($ruta["cargarcompra"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);', 'class' => 'btn  btn-warning waves-effect waves-light btn-xs ', 'disabled'=>'true')) !!}</td>
			<td align="center">{!! Form::button('<div class="glyphicon  glyphicon-transfer"></div> VENDER', array('onclick' => 'modal (\''.URL::route($ruta["cargarventa"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_ventaaccion.'\', this);', 'class' => 'btn  btn-xs btn-info', 'disabled' =>'true')) !!}</td>
			@else
			<td align="center">{!! Form::button('<div class="glyphicon  glyphicon-plus"></div> COMPRAR', array('onclick' => 'modal (\''.URL::route($ruta["cargarcompra"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);', 'class' => 'btn  btn-xs btn-info')) !!}</td>
			<td align="center">{!! Form::button('<div class="glyphicon  glyphicon-transfer"></div> VENDER', array('onclick' => 'modal (\''.URL::route($ruta["cargarventa"], array($value->persona_id, 'listar'=>'SI')).'\', \''.$titulo_ventaaccion.'\', this);', 'class' => 'btn btn-warning waves-effect waves-light  btn-xs ')) !!}</td>
			@endif
			<td align="center">{!! Form::button('<div class="glyphicon  glyphicon-list"></div> Ver Historial', array('onclick' => 'modal (\''.URL::route($ruta["listacciones"], array($value->persona_id, 'listar'=>'SI')).'\', \''.'Lista de acciones del socio: '.$value->persona_apellidos.' '.$value->persona_nombres.'\'   , this);', 'class' => 'btn  btn-xs btn-success')) !!}</td>
			<td align="center">{!! Form::button('<div class="glyphicon  glyphicon-download-alt"></div> Reporte', array('onclick' => 'modal (\''.URL::route($ruta["modalreporte"], array($value->persona_id, 'listar'=>'SI')).'\', \''.'Reporte por Rango de fecha del socio: '.$value->persona_apellidos.' '.$value->persona_nombres.'\', this);', 'class' => 'btn  btn-primary waves-effect waves-light btn-xs ')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif