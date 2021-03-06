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

            <div class="row m-b-5">
                <div class="col-sm-12">
					{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

					<div class="form-group">
						{!! Form::label('fechaini', 'Desde:', array('class' => 'input-sm')) !!}
						{!! Form::date('fechaini', null, array('class' => 'form-control input-xs', 'id' => 'fechaini',  'onchange' => 'buscar(\''.$entidad.'\')')) !!}
						
					</div>
					<div class="form-group">
						{!! Form::label('fechafin', 'Hasta:', array('class' => 'input-sm')) !!}
						{!! Form::date('fechafin', null, array('class' => 'form-control input-xs', 'id' => 'fechafin',  'onchange' => 'buscar(\''.$entidad.'\')')) !!}
						
					</div>

					<div class="form-group">
						{!! Form::label('tipoi', 'Tipo:', array('class' => 'input-sm')) !!}
						{!! Form::select('tipoi', $cboTipo, null, array('class' => 'form-control input-sm', 'id' => 'tipoi')) !!}
					</div>
					
					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:', array('class'=>'input-sm'))!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-sm', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-xs', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-xs', 'id' => 'btnNuevo', 'onclick' => 'modalnuevo (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\');')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-folder-open "></i> Reportes', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-xs', 'id' => 'btnNuevoReporte', 'onclick' => 'modal (\''.URL::route($ruta["cargarreporte"], array('listar'=>'SI')).'\', \''.$titulo_reporte.'\', this);')) !!}
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
		
		var fechaActual = new Date();
        var day = ("0" + fechaActual.getDate()).slice(-2);
        var month = ("0" + (fechaActual.getMonth()+1)).slice(-2);
        var fechaactualr = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-01";
		//fecha inicial
		$('#fechafin').val(fechaactualr);
		//fecha final
        $('#fechaini').val(fechai);

		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="login"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		buscar('{{ $entidad }}');
	});

	function modalnuevo(ruta, titulo){
		var rutamodal = ruta+'&fecha='+$('#fechaf').val();
		modal(rutamodal, titulo);
	}


	function modalrecibopdf(url_pdf, ancho_modal, titulo_modal) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}
</script>