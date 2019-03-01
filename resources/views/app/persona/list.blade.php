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
			<td>{{ $value->codigo }}</td>
			<td>{{ $value->dni }}</td>
			<td>{{ $value->apellidos.'  '.$value->nombres }}</td>
			<td>{{ $value->telefono_fijo }}</td>
			<td>{{ $value->email }}</td>
			@if ($value->estado === 'A')
			<td style="color:green;font-weight: bold;" >Activo</td>
			@else
			<td style="color:red;font-weight: bold;" >Inactivo</td>
			@endif
			<td><a target="_blank" href="{{ route('generarestadocuentaPDF', $value->id) }}" class="btn btn-primary waves-effect waves-light btn-xs" ><i class="glyphicon glyphicon-download-alt" ></i> Desc.</a></td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Edit.', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Elim.', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif