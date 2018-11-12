
<div class="row">
	<div class="col-sm-12">
		<div id="carousel-ejemplo" class="carousel slide" data-ride="carousel">
  				<div class="carousel-inner" role="listbox">
    				<div class="item active">
      					<!-- ppppp -->
						<div class="row">
						    <div class="col-sm-12">
						        <div class="card-box table-responsive">
						            <div class="form-row m-b-30">
						                <div class="col-sm-12">
											{!! Form::open(['route' => null, 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'id' => 'formnuevapregunta']) !!}
											{!! Form::hidden('page', 1, array('id' => 'page')) !!}

                                            <div class="form-group col-md-7 col-sm-6">
                                                <div class="col-sm-12">
                                                    <div class="form-group col-sm-4">
                                                        {!! Form::label('fechai', 'Fecha:', array('class' => 'input-sm')) !!}
                                                        {!! Form::date('fechai', '', array('class' => 'form-control input-sm', 'id' => 'fechai')) !!}
                                                     </div>

                                                    <div class="form-group col-sm-4">
                                                        {!! Form::label('concepto_id', 'Concepto:', array('class' => 'input-sm')) !!}
                                                        {!! Form::select('concepto_id', $cboConcepto, null, array('class' => 'form-control input-sm', 'id' => 'concepto_id')) !!}
                                                    </div>

                                                    <div class="form-group col-sm-4">
                                                        {!! Form::label('filas', 'Filas a mostrar:')!!}
                                                        {!! Form::selectRange('filas', 1, 30, 7, array('class' => 'form-control input-xs', 'onchange' => 'detalle(\''.$entidad.'\')')) !!}
                                                    </div>
                                                </div>
                                                {!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
                                                {!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nueva Transaccion', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$tituloNuevaTransaccion.'\', this);')) !!}
                                            </div>
                                            
                                            <div class="form-group col-md-5 col-sm-6">
                                                <div class="row m-b-30" style="border: 1px solid blue; ">
                                                    <div class=" form-row " >
                                                        <div class="form-group col-sm-4">
                                                            {!! Form::label('login', 'Ingresos s/.:', array('class' => '')) !!}
                                                            <p> {{ $ingresos }}</p>
                                                        </div>
                                                        <div class="form-group col-sm-4">
                                                            {!! Form::label('login', 'Egresos s/.:', array('class' => '')) !!}
                                                                <p > {{ $egresos }}</p>
                                                        </div>

                                                        <div class="form-group col-sm-4">
                                                            {!! Form::label('login', 'Diferencia s/.:', array('class' => '')) !!}
                                                                <p> {{ $diferencia }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
				
											{!! Form::close() !!}
						                </div>
						            </div>

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
                                                    <td>{{ Date::parse($value->transaccion_fecha )->format('Y/m/d')  }}</td>
                                                    <td>{{ $value->transaccion_monto }}</td>
                                                    <td>{{ $value->concepto_titulo }}</td>
                                                    <td>{{ $value->transaccion_descripcion }}</td>
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
    
				    <div class="item">
				      	<a href="#carousel-ejemplo" style="btn btn-default btn-xs" data-slide="prev" onclick="$('.correcto').addClass('hidden');"><div class="retorno glyphicon glyphicon-chevron-left"></div> Atrás</a>
						<div class="row">
						    <div class="col-sm-12">
						        <div class="card-box table-responsive">
						            <div id="tablaalternativas">							            
									</div>
						        </div>
						    </div>
						</div>
				    </div>           
  				</div>
  			</div>
  		</div>
  	</div>
</div>
<div class="form-group text-center">
	{!! Form::button('Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCerrar', 'onclick' => 'cerrarModal();')) !!}
</div>
<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1000');
}); 
</script>
