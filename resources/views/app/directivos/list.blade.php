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
			<td>{{ date('d/m/Y', strtotime($value->periodoi)) }}</td>
			<td>{{ date('d/m/Y', strtotime($value->periodof)) }}</td>
			@if($value->estado == 'A')
			<td align="center">Activo</td>
			@else
			<td align="center">inactivo</td>
			@endif
			<td>{!! Form::button('<div class="glyphicon  glyphicon-list"></div> ver detalle', array('onclick' => 'modal (\''.URL::route($ruta["listdirectivos"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_detalle.' '.$month[intval(Date::parse($value->periodoi )->format('m'))].' - '.Date::parse($value->periodoi )->format('Y').' a '.$month[intval(Date::parse($value->periodof )->format('m'))].' - '.Date::parse($value->periodof)->format('Y').'\'   , this);', 'class' => 'btn  btn-xs btn-success')) !!}</td>
			<td><a target="_blank" href="{{ route('directivosPDF', $value->id) }}" class="btn btn-primary waves-effect waves-light btn-xs" ><i class="glyphicon glyphicon-download-alt" ></i> Descargar PDF</a></td>
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Editar', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_modificar.'\', this);', 'class' => 'btn btn-xs btn-warning')) !!}</td>
		</tr>

		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
