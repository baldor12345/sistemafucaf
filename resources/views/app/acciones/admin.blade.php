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
						{!! Form::label('dni', 'Dni:', array('class' => 'input-sm')) !!}
						{!! Form::text('dni', '', array('class' => 'form-control input-sm', 'id' => 'dni')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('nombres', 'Nombre:', array('class' => 'input-sm')) !!}
						{!! Form::text('nombres', '', array('class' => 'form-control input-sm', 'id' => 'nombres')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Comprar accion', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevo', 'onclick' => 'modalabriraccion (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', \''.$idcaja.'\');')) !!}
					<a target="_blank" href="{{ route('generarnormasaccionPDF') }}" class="btn btn-primary waves-effect waves-light btn-md" ><i class="glyphicon glyphicon-download-alt" ></i> Normas 20%</a>
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
	});

	
	function modalabriraccion(controlador, titulo, idcaja){
		if(idcaja !=0){
			modal(controlador, titulo);
		}else{
			bootbox.confirm({
				title: "Mensaje de error",
				message: "Caja no aperturada",
				buttons: {
					cancel: {
						label: 'Cancelar'
					},
					confirm: {
						label: 'Aceptar'
					}
				},
				callback: function (result) {
					if(result){
						
					}
				}
			});

		}
		
	}
</script>