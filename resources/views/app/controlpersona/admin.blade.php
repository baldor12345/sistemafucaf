<!-- Page-Title -->
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            {{--
            <ol class="breadcrumb pull-right">
                <li><a href="#">Minton</a></li>
                <li><a href="#">Tables</a></li>
                <li class="active">Datatable</li>
            </ol>
            --}}
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

					<div class="form-group">
						{!! Form::label('fecha', 'Fecha de reunion:', array('class' => 'input-sm')) !!}
						{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha',  'onchange' => 'buscar(\''.$entidad.'\')')) !!}
						
					</div>

					<div class="form-group">
						{!! Form::label('tipo', 'Tipo:', array('class' => 'input-sm')) !!}
						{!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-sm', 'id' => 'tipo')) !!}
					</div>
					
					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="fa fa-check fa-lg"></i> Generar Reporte', array('class' => 'btn btn-success btn-md', 'id' => 'btnGuardar', 'onclick' => 'reporteasistencia(\''.$entidad.'\', \''.URL::route($ruta["generarreporteasistenciaPDF"], array()).'\')')) !!}
					{!! Form::close() !!}
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
        var month = ("0" + (fechaActual.getMonth()+1)).slice(-2);
        var fechaactualr = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
        $('#fecha').val(fechaactualr);
	});


	function reporteasistencia(entidad, rutarecibo) {

		var fechai = $('#fecha').val();
		var tipo = $('#tipo').val();
		modalrecibopdf(rutarecibo+"/"+fechai, '100', 'recibo credito');
    }


	function modalrecibopdf(url_pdf, ancho_modal, titulo_modal) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}
</script>