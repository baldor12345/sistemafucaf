<h4 class="modal-title">Detalle de acciones</h4>
<div class="modal-body">
	<div class="row">
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
								<td fechaT='{{ $value->acciones_fecha }}' > {{ Date::parse($value->acciones_fecha)->format('d/m/y') }} </td>
								<td estadoT='{{ $value->acciones_descripcion }}'>{{$value->acciones_descripcion}}</td>
								<td><a target="_blank" href="{{ route('generarvoucheraccionPDF', array('id' => $value->acciones_persona_id,'cant' => $value->cantidad_accion_comprada, 'fecha' => $value->acciones_fecha ) ) }}" class="btn btn-info waves-effect waves-light btn-xs" ><i class="glyphicon glyphicon-download-alt" ></i> descargar</a></td>
							</tr>
							<?php
							$contador = $contador + 1;
							?>
							@endforeach
						</tbody>
						
					</table>
					@endif
					<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
	</div>
</div>


