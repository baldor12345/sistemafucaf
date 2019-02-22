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
            <td align="center">{{ $value->accion_codigo }}</td>
			<td align="center">{{ $Month[intval(Date::parse($value->accion_fecha )->format('m'))].' - '.Date::parse($value->accion_fecha )->format('Y') }}</td>
			@if ($value->accion_estado === 'C')
			<td align="center" >Compra</td>
			@else
			<td align="center" >Venta</td>
			@endif
            @if($value->accion_descripcion != '')
			<td width="30%">{{ $value->accion_descripcion }}</td>
			@else
			<td align="center">---</td>
			@endif
			@if($value->accion_estado === 'C')
			<td align="center"><a target="_blank" href="{{ route('generarvoucheraccionPDF', array('id' => $value->accion_persona_id,'cant' => '1', 'fecha' => $value->accion_fecha ) ) }}" class="btn btn-info waves-effect waves-light btn-xs" ><i class="glyphicon glyphicon-download-alt" ></i> descargar</a></td>
			@else
			<td align="center"><a target="_blank" href="{{ route('generarvoucheraccionventaPDF', array('id' => $value->accion_persona_id,'cant' => '1', 'fecha' => $value->accion_fecha ) ) }}" class="btn btn-info waves-effect waves-light btn-xs" ><i class="glyphicon glyphicon-download-alt" ></i> descargar</a></td>
			@endif

			@if($fecha_caja !=0)
				@if( $fecha_caja == Date::parse($value->accion_fecha)->format('Y-m'))
				<td align="center">{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->accion_id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
				@else
				<td align="center">{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->accion_id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger','disabled')) !!}</td>
				@endif
			@else
				<td align="center">{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->accion_id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger','disabled')) !!}</td>
			@endif
		</tr>
		<?php
			$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif