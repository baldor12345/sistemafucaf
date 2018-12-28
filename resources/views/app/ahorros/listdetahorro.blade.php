@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<div class="form-group col-12" style="height: 15px">
	@if($tipo == 'I')
	<h4 id="tituloretiro_ahorro" >Depositos de ahorros: </h4>
	@else
	<h4 id="tituloretiro_ahorro" >Retirod de ahorros: </h4>
	@endif
</div>
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
			<td>{{Date::parse($value->fecha)->format('d/m/Y') }}</td>
            <td>{{ $value->monto }}</td>
            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Imprimir Voucher', array('onclick' => '','class' => 'btn btn-xs btn-warning')) !!}</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>

@endif

