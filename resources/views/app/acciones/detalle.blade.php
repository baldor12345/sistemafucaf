
<div class="modal-header">
        <h4 class="modal-title">Detalle de acciones</h4>
</div>

<div class="modal-body">
	<div class="row">
		<div class="col-sm-12">
			<div class="card-box table-responsive">
				<div id="tabla">
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
							$contador = $inicio + 1;
							?>
							@foreach ($lista as $key => $value)
							<tr>
								<td >{{$contador}}</td>
								<td cant='{{ $value->cantidad_accion_comprada }}' >{{ $value->cantidad_accion_comprada}}</td>
								@if ($value->acciones_estado === 'C')
								<td estadoT='{{ $value->acciones_estado }}'>Compra</td>
								@else
								<td estadoT='{{ $value->acciones_estado }}'>Venta</td>
								@endif
								<td fechaT='{{ $value->acciones_fecha }}' >{{ $value->acciones_fecha}}</td>
								<td estadoT='{{ $value->acciones_descripcion }}'>{{$value->acciones_descripcion}}</td>
							</tr>
							<?php
							$contador = $contador + 1;
							?>
							@endforeach
						</tbody>
						
					</table>
					@endif
				</div>
			</div>
		</div>
	</div>

</div>
<div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>

