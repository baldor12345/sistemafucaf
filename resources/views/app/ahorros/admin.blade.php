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
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Actualizar capitalizacion', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnactualizar', 'onclick' => 'actualizardatos();')) !!}
					{!! Form::close() !!}
                </div>
            </div>
			<div id="listado{{ $entidad }}"></div>
        </div>
    </div>
</div>

<script>
	$(document).ready(function () {
		//var fechaActual = new Date();
	/*	var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-01";
		*/
		
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
</script>