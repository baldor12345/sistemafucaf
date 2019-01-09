
<style>
a.disabled {
   pointer-events: none;
   cursor: default;
}
</style>
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
			<td>{{ Date::parse( $value->fecha_horaApert )->format('Y-m-d  H:i')  }}</td>
			@if ($value->fecha_horaCierre !== null)
			<td>{{ Date::parse( $value->fecha_horaCierre )->format('Y-m-d H:i')  }}</td>
			@else
			<td id="cerrado" >-</td>
			@endif
			<td>{{ $value->monto_iniciado }}</td>
			<td>{{ $value->monto_cierre or '-' }}</td>
			<td>{{ $value->diferencia_monto  or '-'}}</td>
			@if ($value->estado === 'A')
			<td style="color:green;font-weight: bold;" >Abierto</td>
			@else
			<td style="color:red;font-weight: bold;" >Cerrado</td>
			@endif
			<td>{!! Form::button('<div class="glyphicon  glyphicon-list"></div> Ver', array('onclick' => 'modal (\''.URL::route($ruta["detalle"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_transaccion.'\', this);', 'class' => 'btn  btn-xs btn-success')) !!}</td>
			
			@if ($value->estado === 'C')
			<td>{!! Form::button('<div class="glyphicon  glyphicon-plus"></div> Nuevo', array('onclick' => 'modal (\''.URL::route($ruta["nuevomovimiento"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_nuevomovimiento.'\', this);', 'class' => 'btn  btn-xs btn-info','disabled' )) !!}</td>
			@else
			<td>{!! Form::button('<div class="glyphicon  glyphicon-plus"></div> Nuevo', array('onclick' => 'modal (\''.URL::route($ruta["nuevomovimiento"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_nuevomovimiento.'\', this);', 'class' => 'btn  btn-xs btn-info')) !!}</td>
			@endif

			<td><a target="_blank" href="{{ route('reportecajaPDF', array('id' => $value->id) ) }}" class="btn btn-info waves-effect waves-light btn-xs"><i class="glyphicon glyphicon-download-alt"></i> Caja</a></td>
			

			<?php if($caja_last->id == $value->id){ if($value->estado == 'C'){ ?>
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Reaperturar', array('onclick' => 'modal (\''.URL::route($ruta["cargarreapertura"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_reapertura.'\', this);','class' => 'btn btn-xs btn-warning')) !!}</td>
				
			<?php }else if($value->estado == 'A'){ ?>
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Reaperturar', array('onclick' => 'modal (\''.URL::route($ruta["cargarreapertura"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_reapertura.'\', this);','class' => 'btn btn-xs btn-warning','disabled')) !!}</td>
			<?php } }else if($caja_last->id != $value->id){?>
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Reaperturar', array('onclick' => 'modal (\''.URL::route($ruta["cargarreapertura"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_reapertura.'\', this);','class' => 'btn btn-xs btn-warning','disabled')) !!}</td>
			<?php }?>


			@if ($value->estado === 'C')
			<td>{!! Form::button('<div class="glyphicon glyphicon-star-empty"></div> Cierre', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_cerrarCaja.'\', this);', 'class' => 'btn btn-xs btn-secondary','disabled' )) !!}</td>
			@else
			<td>{!! Form::button('<div class="glyphicon glyphicon-star-empty"></div> Cierre', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_cerrarCaja.'\', this);', 'class' => 'btn btn-xs btn-secondary')) !!}</td>
			@endif
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table
@endif