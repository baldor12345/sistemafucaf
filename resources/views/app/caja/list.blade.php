
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
				<th style="font-size: 14px" @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td style="font-size: 14px">{{ $contador }}</td>
			<td style="font-size: 14px">{{ $value->titulo }}</td>	
			<td style="font-size: 14px">{{ Date::parse( $value->fecha_horaapert )->format('Y-m-d  H:i')  }}</td>
			@if ($value->fecha_horacierre !== null)
			<td style="font-size: 14px">{{ Date::parse( $value->fecha_horacierre )->format('Y-m-d H:i')  }}</td>
			@else
			<td id="cerrado" style="font-size: 14px" >-</td>
			@endif
			<td style="font-size: 14px">{{ number_format($value->monto_iniciado,1) }}</td>
			<td style="font-size: 14px">{{ number_format($value->monto_cierre,1) }}</td>
			@if ($value->estado === 'A')
			<td style="color:green;font-weight: bold; font-size: 14px;" >Abierto</td>
			@else
			<td style="color:red;font-weight: bold; font-size: 14px;" >Cerrado</td>
			@endif
			<td>{!! Form::button('<div class="glyphicon  glyphicon-list" ></div> Ver', array('onclick' => 'modal (\''.URL::route($ruta["detalle"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_transaccion.'\', this);', 'class' => 'btn  btn-xs btn-success')) !!}</td>
			
			@if ($value->estado === 'C')
			<td>{!! Form::button('<div class="glyphicon  glyphicon-plus"></div> Nuevo', array('onclick' => 'modal (\''.URL::route($ruta["nuevomovimiento"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_nuevomovimiento.'\', this);', 'class' => 'btn  btn-xs btn-info','disabled' )) !!}</td>
			@else
			<td>{!! Form::button('<div class="glyphicon  glyphicon-plus"></div> Nuevo', array('onclick' => 'modal (\''.URL::route($ruta["nuevomovimiento"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_nuevomovimiento.'\', this);', 'class' => 'btn  btn-xs btn-info')) !!}</td>
			@endif

			<td><a target="_blank" href="{{ route('reportecajaPDF', array('id' => $value->id) ) }}" class="btn btn-info waves-effect waves-light btn-xs"><i class="glyphicon glyphicon-download-alt"></i> Caja</a></td>
			

			<?php if($caja_last->id == $value->id){ if($value->estado == 'C'){ ?>
				<td>{!! Form::button('<div class="glyphicon glyphicon-refresh"></div> Reaperturar', array('onclick' => 'modal (\''.URL::route($ruta["cargarreapertura"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_reapertura.'\', this);','class' => 'btn btn-xs btn-warning')) !!}</td>
				
			<?php }else if($value->estado == 'A'){ ?>
				<td>{!! Form::button('<div class="glyphicon glyphicon-refresh"></div> Reaperturar', array('onclick' => 'modal (\''.URL::route($ruta["cargarreapertura"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_reapertura.'\', this);','class' => 'btn btn-xs btn-warning','disabled')) !!}</td>
			<?php } }else if($caja_last->id != $value->id){?>
				<td>{!! Form::button('<div class="glyphicon glyphicon-refresh"></div> Reaperturar', array('onclick' => 'modal (\''.URL::route($ruta["cargarreapertura"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_reapertura.'\', this);','class' => 'btn btn-xs btn-warning','disabled')) !!}</td>
			<?php }?>


			@if ($value->estado === 'C')
			<td>{!! Form::button('<div class="glyphicon glyphicon-remove-circle"></div> Cierre', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_cerrarCaja.'\', this);', 'class' => 'btn btn-xs btn-secondary','disabled' )) !!}</td>
			@else
			<td>{!! Form::button('<div class="glyphicon glyphicon-remove-circle"></div> Cierre', array('onclick' => 'modal (\''.URL::route($ruta["edit"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_cerrarCaja.'\', this);', 'class' => 'btn btn-xs btn-secondary')) !!}</td>
			@endif
			
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table
@endif