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
					
					<div class="form-group">
							{!! Form::label('anio', 'Año:', array('class' => 'input-sm')) !!}
							{!! Form::select('anio', $anios, $anioactual, array('class' => 'form-control input-sm', 'id' => 'anio')) !!}
						</div>
					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo Cálculo', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnCalcular', 'onclick' => 'abrirmodal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.'Distribucion de utilidades en el año '.'\', this);')) !!}
					{!! Form::close() !!}
                </div>
            </div>

			<div id="listado{{ $entidad }}"></div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function () {
		$('#anio').select2();
		$('#anio').val("{{ $anioactual }}");
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="anio"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
		
	});
	function abrirmodal(ruta, titulo){
		titulo = "Distribucion de Utilidades al año "+$('#anio').val(); 
		var rutamodal = ruta +'&anio='+$('#anio').val();
		modal(rutamodal, titulo);
	}
	//Funcion para abrir pdf en una pestaña nueva del navegador
	function modalrecibopdf(url_pdf) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}
	
</script>