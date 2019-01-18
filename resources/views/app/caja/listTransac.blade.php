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
                                <td>{{ Date::parse($value->transaccion_fecha )->format('Y/m/d')  }}</td>
                                <td>{{ number_format($value->transaccion_monto,1) }}</td>
                                <td>{{ $value->concepto_titulo }}</td>
								@if ($value->concepto_tipo === 'I')
								<td style="color:green;font-weight: bold;" >Ingreso</td>
								@else
								<td style="color:red;font-weight: bold;" >Egreso</td>
								@endif
								@if ($value->persona_nombres !== null)
								<td>{{ $value->persona_nombres.' '.$value->persona_apellidos }}</td>
								@else
								<td > - - -</td>
								@endif
                                <td>{{ $value->transaccion_descripcion }}</td>
                            </tr>
							<?php
								$contador = $contador + 1;
							?>
							@endforeach
						</tbody>
					</table>
					@endif