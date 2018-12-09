<div class="row">
	    <div class="col-sm-12">
	        <div class="card-box table-responsive">
				{!! Form::open(['route' => null, 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-inline', 'id' => 'formnuevapregunta']) !!}
				<div class="row m-b-30">
					<div class="col-sm-12">
						{!! Form::hidden('page', 1, array('id' => 'page')) !!}
						<div class="form-group">
                            {!! Form::label('concepto_id', 'Concepto:', array('class' => 'input-sm')) !!}
                            {!! Form::select('concepto_id', $cboConcepto, null, array('class' => 'form-control input-sm', 'id' => 'concepto_id')) !!}
                        </div>
						<div class="form-group">
                            {!! Form::label('filas', 'Filas a mostrar:')!!}
                            {!! Form::selectRange('filas', 1, 30, 7, array('class' => 'form-control input-xs', 'onchange' => 'detalle(\''.$entidad.'\')')) !!}
                        </div>
										
						{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				    </div>
				</div>
				{!! Form::close() !!}
				<div id="tablapreguntas">
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
                                <td>{{ Date::parse($value->fecha )->format('Y/m/d')  }}</td>
                                <td>{{ $value->monto }}</td>
                                <td>{{ $value->concepto->titulo }}</td>
								@if ($value->concepto->tipo === 'I')
								<td style="color:green;font-weight: bold;" >Ingreso</td>
								@else
								<td style="color:red;font-weight: bold;" >Egreso</td>
								@endif
								@if ($value->persona !== null)
								<td>{{ $value->persona->nombres.' '.$value->persona->apellidos }}</td>
								@else
								<td > - - -</td>
								@endif
                                <td>{{ $value->descripcion }}</td>
                            </tr>
							<?php
								$contador = $contador + 1;
							?>
							@endforeach
						</tbody>
					</table>
					@endif
				</div>

				<table class="table-bordered table-striped table-condensed" align="center">
					<thead>
						<tr>
							<th class="text-center" colspan="2"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Resumen de Caja</font></font></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Ingresos :</font></font></th>
							<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $ingresos }}</font></font></th>
						</tr>

						<tr>
							<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Egresos :</font></font></th>
							<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $egresos }}</font></font></th>
						</tr>
						<tr>
							<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Saldo :</font></font></th>
							<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ $diferencia }}</font></font></th>
						</tr>
					</tbody>
				</table>
	        </div>
	    </div>
	</div>
</div>

<div class="form-group text-center">
	{!! Form::button('Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCerrar', 'onclick' => 'cerrarModal();')) !!}
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1200');
}); 
</script>
