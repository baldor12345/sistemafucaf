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
		@if(trim($persona->tipo) == 'E')
			@foreach ($lista as $key => $value)
			<tr>
				<td>{{ $contador }}</td>
				<td>{{Date::parse($value->fecha)->format('d/m/Y') }}</td>
				<td>{{ round($value->monto,1) }}</td>
				@if($tipo == 'I')
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Imprimir Voucher', array('class' => 'btn btn-xs btn-light', 'disabled'=>true)) !!}</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('class' => 'btn btn-xs btn-light', 'disabled'=>true)) !!}</td>
				@else
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Imprimir Voucher', array('onclick' => 'imprimirpdf(\''.URL::route($ruta["generareciboretiroPDF"], array($value->transaccion_id)).'\')','class' => 'btn btn-xs btn-warning')) !!}</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modal (\''.URL::route($ruta["delete"], array($value->transaccion_id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
				@endif
			</tr>
			<?php
			$contador = $contador + 1;
			?>
			@endforeach
		@else
			@foreach ($lista as $key => $value)
			<tr>
				<td>{{ $contador }}</td>
				<td>{{Date::parse($value->fecha)->format('d/m/Y') }}</td>
				<td>{{ round($value->monto,1) }}</td>
				@if($tipo == 'I')
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Imprimir Voucher', array('onclick' => 'imprimirpdf(\''.URL::route($ruta["generareciboahorroPDF1"], array($value->transaccion_id)).'\')','class' => 'btn btn-xs btn-warning')) !!}</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modalElim(\''.URL::route($ruta["delete"], array($value->transaccion_id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
				@else
				<td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Imprimir Voucher', array('onclick' => 'imprimirpdf(\''.URL::route($ruta["generareciboretiroPDF"], array($value->transaccion_id)).'\')','class' => 'btn btn-xs btn-warning')) !!}</td>
				<td>{!! Form::button('<div class="glyphicon glyphicon-remove"></div> Eliminar', array('onclick' => 'modalElim(\''.URL::route($ruta["delete"], array($value->transaccion_id, 'SI')).'\', \''.$titulo_eliminar.'\', this);', 'class' => 'btn btn-xs btn-danger')) !!}</td>
				@endif
			</tr>
			<?php
			$contador = $contador + 1;
			?>
			@endforeach
		@endif
	</tbody>
</table>
<script type="text/javascript">
	$(document).ready(function() {
		
    });
	function modalElim(ruta, titulo){
		modal(ruta, titulo);
        $("#modal"+(contadorModal - 1)).on('hidden.bs.modal', function () {
            buscar("Detalleahorro");
        });
	}
</script>

@endif


