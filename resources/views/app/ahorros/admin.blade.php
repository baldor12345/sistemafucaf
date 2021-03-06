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

            <div class="row m-b-10">
                <div class="col-sm-12">
					{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
					{!! Form::hidden('id_caja', $idcaja, array('id' => 'id_caja')) !!}
					<div class="form-group">
						{!! Form::label('nombres', 'Nombre:', array('class' => 'input-sm')) !!}
						{!! Form::text('nombres', '', array('class' => 'form-control input-sm', 'id' => 'nombres')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevo', 'onclick' => 'modalValidadorCaja(\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$tituloRegistrar.'\');')) !!}
					{!! Form::button('<i class="glyphicon "></i> Reporte', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnReporteah', 'onclick' => 'modal(\''.URL::route($ruta["vistareporteahorros"], array('listar'=>'SI')).'\', \''."Reporte Ahorros".'\');')) !!}
					
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
	function actualizardatos(){
		$.ajax({
				url: 'ahorros/actualizarecapitalizacion',
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
				type: 'GET',
				data: null,
				beforeSend: function(){
				},
				success: function(res){
					mostrarMensaje (res, "OK");
				}
			}).fail(function(){
				mostrarMensaje ("Error de consulta..", "ERROR");
			});
	}
	function modalValidadorCaja(ruta,titulo){
		if({{$idcaja}}==0){
			bootbox.alert("<div class='alert alert-danger'><strong>¡Error!</strong> Caja no aperturada, asegurese de aperturar caja.!</div>");
		}else{
			modal(ruta, titulo);
		}
	}
	function imprimirpdf(url_pdf) {
		//console.log("ruta: "+url_pdf);
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}

	function filterFloat(evt,input){
		// Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
		var key = window.Event ? evt.which : evt.keyCode;    
		var chark = String.fromCharCode(key);
		var tempValue = input.value+chark;
		if(key >= 48 && key <= 57){
			if(filter(tempValue)=== false){
				return false;
			}else{       
				return true;
			}
		}else{
			if(key == 8 || key == 13 || key == 0) {     
				return true;              
			}else if(key == 46){
					if(filter(tempValue)=== false){
						return false;
					}else{       
						return true;
					}
			}else{
				return false;
			}
		}
	}
	function filter(__val__){
		var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
		if(preg.test(__val__) === true){
			return true;
		}else{
		return false;
		}
	}
</script>