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
        <?php
        $fecha_actual = date('Y-m-d'); 
        $fecha_dep = date("Y-m-d", strtotime($value->fecha_deposito));
        $datosfac = explode("-", $fecha_actual);
        $datofdep = explode("-", $fecha_dep); 
        $fechadeposito = new DateTime (''.$datofdep[0].'-'.$datofdep[1].'-'.$datofdep[2]);
        $fechafinal =  new DateTime (''.$datosfac[0].'-'.$datosfac[1].'-'.$datosfac[2]);
        $diferencia = $fechadeposito-> diff($fechafinal);
        $cantmeses = ($diferencia->y * 12) + $diferencia->m;
        $interesganado =($cantmeses >= 1)? $value->importe * pow((101/100),(int)$cantmeses):$value->importe;
        $interesganado -= $value->importe;
        $interesMostrar= round(  $interesganado , 2, PHP_ROUND_HALF_UP);
		?>

		<tr>
			<td>{{ $contador }}</td>
			<td>{{Date::parse($value->fecha_deposito)->format('d/m/Y') }}</td>
            <td>{{ $value->importe }}</td>
            <td>{{ $interesMostrar }}</td>
            <td>{{ $value->importe + $interesMostrar}}</td>
			@if($estado != 'P' )
            <td>{{ ($value->fecha_retiro != null)?Date::parse($value->fecha_retiro)->format('d/m/Y'):""}}</td>
			@endif
            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Ver capitalización', array('onclick' => '','class' => 'btn btn-xs btn-warning')) !!}</td>
			@if($estado == 'P' )
			<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> RETIRAR', array('onclick' => 'modal (\''.URL::route($ruta["retirar"], array($value->ahorros_id, 'listar'=>'SI')).'\', \''.$titulo_retirar.'\', this);','class' => 'btn btn-xs btn-warning')) !!}</td>
			@endif
		</tr>
		<?php
		$contador = $contador + 1;
		?>

		@endforeach
	</tbody>
</table>

@endif

