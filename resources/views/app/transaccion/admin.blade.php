<a class="glyphicon glyphicon-chevron-left btn btn-warning btn-xs " href="#" onclick="cargarRutaMenu('caja', 'container', '0');">Volver</a>
<div class="row">
    <div class="col-sm-12">
        <div class="card-box table-responsive">

			<div class="form-row">
				<div class="form-group col-md-6 col-sm-6">
					<div class="row m-b-30" id="resumen">
						<div class="col-sm-12">
						{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
						{!! Form::hidden('page', 1, array('id' => 'page')) !!}
						{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
						
							<div class="form-group">
								{!! Form::label('fechai', 'Fecha:', array('class' => 'input-sm')) !!}
								{!! Form::date('fechai', '', array('class' => 'form-control input-sm', 'id' => 'fechai')) !!}
							</div>

							<div class="form-group">
								{!! Form::label('concepto_id', 'Concepto:', array('class' => 'input-sm')) !!}
								{!! Form::select('concepto_id', $cboConcepto, null, array('class' => 'form-control input-sm', 'id' => 'concepto_id')) !!}
							</div>
							
							<div class="form-group">
								{!! Form::label('filas', 'Filas a mostrar:')!!}
								{!! Form::selectRange('filas', 1, 30, 7, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
							</div>
						{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
						{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nueva Transaccion', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevo', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
						{!! Form::close() !!}
						</div>
					</div>
				</div>
				<div class="form-group col-md-6 col-sm-6" >
					<div class="row m-b-30" style="border: 3px solid blue; ">
						<div class=" from-row " >
							<div class="form-group">
								{!! Form::label('login', 'Ingresos s/.:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
								<div class="col-sm-9 col-xs-12">
									<p> {{ $ingresos }}</p>
								</div>
							</div>
							<div class="form-group">
								{!! Form::label('login', 'Egresos s/.:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
								<div class="col-sm-3 col-xs-12">
									<p > {{ $egresos }}</p>
								</div>
							</div>

							<div class="form-group">
								{!! Form::label('login', 'Diferencia s/.:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
								<div class="col-sm-3 col-xs-12">
									<p> {{ $diferencia }}</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div id="listado{{ $entidad }}"></div>
			
            <table id="datatable" class="table table-striped table-bordered">
            </table>
        </div>
    </div>
</div>

<script>
	$(document).ready(function () {
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="login"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-01";
		var fechaf = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		$('#fechai').val(fechai);
		$('#fechaf').val(fechaf);

	});
	
</script>