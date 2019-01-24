<div class="row">
	    <div class="col-sm-12">
	        <div class="card-box table-responsive">
				{!! Form::open(['route' => $ruta["buscartransaccion"], 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaTransaccion']) !!}
				<div class="row m-b-30">
					<div class="col-sm-12">
						{!! Form::hidden('page', 1, array('id' => 'page')) !!}
						{!! Form::hidden('idcaja', $id, array('id' => 'idcaja')) !!}
						{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
						<div class="form-group">
                            {!! Form::label('concepto_id1', 'Concepto:', array('class' => 'input-sm')) !!}
                            {!! Form::select('concepto_id1', $cboConcepto, null, array('class' => 'form-control input-sm', 'id' => 'concepto_id1')) !!}
                        </div>
						<div class="form-group">
                            {!! Form::label('filas', 'Filas a mostrar:')!!}
                            {!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                        </div>
										
						{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				    </div>
				</div>
				{!! Form::close() !!}
			
				<div id="listado{{ $entidad }}"></div>
			

				<table class="table-bordered table-striped table-condensed" align="center">
					<thead>
						<tr>
							<th class="text-center" colspan="2"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Resumen de Caja</font></font></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Ingresos :</font></font></th>
							<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ number_format($ingresos,1) }}</font></font></th>
						</tr>

						<tr>
							<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Egresos :</font></font></th>
							<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ number_format($egresos,1) }}</font></font></th>
						</tr>
						<tr>
							<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Saldo :</font></font></th>
							<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">{{ number_format($diferencia,1) }}</font></font></th>
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
	buscar("{{ $entidad }}");
	init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
}); 
</script>
