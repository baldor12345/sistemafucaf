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
			@if($existe_dist_pendiente)
			<div class="alert bg-warning col-xl-6 col-lg-8 col-md-12 col-sm-12 -col-xs-12">
				<div class="row ">
					<div class="col-md-6">
						<strong>Distribución de utilidades Pendiente</strong>
						<ul>
							<li>Utilidad Distribuible Total: {{ $distribuicionPendiente[0]->utilidad_distribuible }}</li>
							<li>Porcentaje Pendiente a distribuir: {{ $distribuicionPendiente[0]->porcentaje_faltante."%" }}</li>
							<li>Utilidad Pendiente por distribuir: {{ ($distribuicionPendiente[0]->porcentaje_faltante/100)*$distribuicionPendiente[0]->utilidad_distribuible }}</li>
						</ul>
					</div>
					<div class="col-md-6" style="text-align: left;">
						{!! Form::button('<i class="glyphicon glyphicon-check"></i> Distribuir Faltante', array('class' => 'btn btn-succes waves-effect waves-light m-l-10 btn-md', 'id' => 'btnDistribuirFalt', 'onclick' => 'modal (\''.URL::route($ruta["vistadistribuirfaltante"], array('distribucion_id'=>$distribuicionPendiente[0]->id)).'\', \''."Distribucion de utilidades faltantes".'\', this);')) !!}
					</div>
				</div>
			</div>
			@endif
            <div class="row m-b-5">
                <div class="col-sm-12">
					{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
					<div class="form-group">
						{!! Form::label('titulo', 'Titulo:', array('class' => 'input-sm')) !!}
						{!! Form::text('titulo', '', array('class' => 'form-control input-sm', 'id' => 'titulo')) !!}
					</div>

					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar1', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Apertura Caja', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevocaja', 'onclick' => 'modal (\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\', this);')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-asterisk"></i> Reportes Mes', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevoReporte', 'onclick' => 'modal (\''.URL::route($ruta["cargarreporte"], array('listar'=>'SI')).'\', \''.$titulo_reporte.'\', this);')) !!}
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
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="login"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
	});
	
	function modalrecibopdf(url_pdf, ancho_modal, titulo_modal) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}
	
</script>