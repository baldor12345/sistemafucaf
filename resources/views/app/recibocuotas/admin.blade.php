<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            
            <h4 class="page-title">{{ $title }}</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card-box table-responsive">

            <div class="row m-b-30">
                <div class="col-sm-12">
					{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
					{!! Form::hidden('anio', $anioactual, array('id' => 'anio')) !!}
					{!! Form::hidden('mes', $mesactual, array('id' => 'mes')) !!}
					
					<div class="form-group">
						{!! Form::label('nombres', 'Nombre:', array('class' => 'input-sm')) !!}
						{!! Form::text('nombres', '', array('class' => 'form-control input-sm', 'id' => 'nombres')) !!}
					</div>

					<div class="form-group">
						{!! Form::label('fecha_recibocuotas', 'Fecha de aplicación:', array('class' => 'input-sm')) !!}
						{!! Form::date('fecha_recibocuotas', $fecha_actual, array('class' => 'form-control input-xs', 'id' => 'fecha_recibocuotas', 'onchange'=>'cambiardatos()')) !!}
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
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombres"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});

	});

	function cambiardatos(){
		var fechadate = new Date($('#fecha_recibocuotas').val());
        $("#anio").val(fechadate.getFullYear());
        $("#mes").val(parseInt(fechadate.getMonth()) + 1);
	}

	//Funcion para abrir pdf en una pestaña nueva del navegador
	function modalrecibopdf(url_pdf, ancho_modal, titulo_modal) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}
	
</script>