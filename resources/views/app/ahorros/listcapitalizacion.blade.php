@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else

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
		$contador = 1;
		$meses = array(
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Septiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre",
        );
		?>
		@foreach ($lista as $key => $value)
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->capital_mensual }}</td>
			<td>{{ $value->interes_mensuals}} </td>
			<td>{{ $meses[$mes]}}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>

		@endforeach
        
	</tbody>
</table>

@endif

