

<div class="row">
    <div class="col-sm-12">
        <div class="card-box">

            <div class="row m-b-30">
                <div class="col-sm-12">
					{!! Form::open(['route' => $ruta["listsimulador"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
					
					<div class="form-group">
							{!! Form::label('nombres', 'Nombre:', array('class' => 'input-sm')) !!}
							{!! Form::text('nombres', '', array('class' => 'form-control input-sm', 'id' => 'nombres')) !!}
						</div>
					<div class="form-group">
						{!! Form::label('fecha_simulador', 'Fecha:', array('class' => 'input-sm')) !!}
						<input class="form-control input-xs" type="month" name="fecha_simulador" id="fecha_simulador" step="1" min="2008-12" max="2050-12" value="{{ $fecha_simulacion }}">
					</div>

					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					
					{!! Form::close() !!}
                </div>
            </div>

			<div id="listado{{ $entidad }}"></div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function () {
		
		configurarAnchoModal('800');
		buscar('{{ $entidad }}');
		
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombres"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});

	});

	
</script>