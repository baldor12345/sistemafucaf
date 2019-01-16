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
		$nomMeses = array(
        '1'=>'Ene',
        '2'=>'Feb',
        '3'=>'Mar',
        '4'=>'Abr',
        '5'=>'May',
        '6'=>'Jun',
        '7'=>'Jul',
        '8'=>'Ago',
        '9'=>'Sep',
        '10'=>'Oct',
        '11'=>'Nov',
        '12'=>'Dic');
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->titulo }}</td>
			<td>{{ $value->utilidad_distribuible }}</td>
			<td>{{-- -  {!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo Cálculo', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnCalcular', 'onclick' => 'mostrarditribucion(\''.URL::route($ruta["mostrar"], array('listar'=>'SI')).'\', \''.'Distribucion de utilidades en el año '.'\', this);')) !!}} --}}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif