@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">

	<thead>
		<tr>
			<td colspan="2" align="center"></td>
			<td colspan="2" align="center">ACCIONES</td>
			<td colspan ="2" align="center">CREDITO</td>
			<td colspan ="1" align="center">AHORROS</td>
			<td rowspan="2" colspan ="1" align="center">FECHA</td>
			<td rowspan="2" colspan ="1" align="center">DESCRIPCION</td>
			<td rowspan="2" colspan="2" align="center">OPERACIONES</td>
		</tr>
		<tr>
			<td colspan="1" align="center">#</td>
			<td colspan="1" align="center">Codigo</td>
			<td colspan="1" align="center">Precio</td>
			<td colspan="1" align="center">Limite</td>
			<td colspan="1" align="center">Interes</td>
			<td colspan="1" align="center">Mora</td>
			<td colspan="1" align="center">Interes</td>
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->codigo }}</td>
			<td>{{ $value->precio_accion }}</td>
			<td>{{ ($value->limite_acciones*100) .'%' }}</td>
			<td>{{ number_format($value->tasa_interes_credito,3) }}</td>
			<td>{{ number_format($value->tasa_interes_multa,3) }}</td>
			<td>{{ number_format($value->tasa_interes_ahorro,3) }}</td>
			<td>{{ $value->fecha }}</td>
			<td>{{ $value->descripcion }}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table
@endif