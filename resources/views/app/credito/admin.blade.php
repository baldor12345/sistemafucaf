
<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ $title }}</h4>
        </div>
    </div>
</div>
{{-- <style>
	.tooltipg {
	  position: relative;
	  display: inline-block;
	  border-bottom: 1px dotted black;
	}
	
	.tooltipg .tooltiptextg {
	  visibility: hidden;
	  width: 120px;
	  background-color: black;
	  color: #fff;
	  text-align: center;
	  border-radius: 6px;
	  padding: 5px 0;
	  position: absolute;
	  z-index: 1;
	  top: -5px;
	  left: 110%;
	}
	
	.tooltipg .tooltiptextg::after {
	  content: "";
	  position: absolute;
	  top: 50%;
	  right: 100%;
	  margin-top: -5px;
	  border-width: 5px;
	  border-style: solid;
	  border-color: transparent black transparent transparent;
	}
	.tooltipg:hover .tooltiptextg {
	  visibility: visible;
	}
</style> --}}

<div class="row">
    <div class="col-sm-12">
        <div class="card-box table-responsive">
		
            <div class="row m-b-30">
                <div class="col-sm-12">
						{!! Form::open(['route' => $ruta["search"], 'method' => 'POST' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
						{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
					<div class="form-group">
						{!! Form::label('txtbusquedanombre', 'Nombre:', array('class' => 'input-sm')) !!}
						{!! Form::text('txtbusquedanombre', '', array('class' => 'form-control input-sm', 'id' => 'txtbusquedanombre')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('fechabusqueda', 'Desde la fecha:', array('class' => 'input-sm')) !!}
						{!! Form::date('fechabusqueda', null, array('class' => 'form-control input-xs', 'id' => 'fechabusqueda',  'onchange' => 'buscar(\''.$entidad.'\')')) !!}
						
					</div>
					<div class="form-group">
						{!! Form::label('estadobusqueda', 'Estado:', array('class' => 'input-sm')) !!}
						{!! Form::select('estadobusqueda', $cboEstado, null, array('class' => 'form-control input-sm', 'id' => 'estadobusqueda', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>

					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md','data-toggle'=>'tooltip', 'id' => 'btnBuscar','data-placement'=>'top', 'title'=>'boton para buscar los datos que estan en la base de dtaos de sistema fucaf boton para buscar los datos que estan en la base de dtaos de sistema fucaf boton para buscar los datos que estan en la base de dtaos de sistema fucaf', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo Credito', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevo', 'onclick' => 'abrirModalMant(\''.URL::route($ruta["create"], array('listar'=>'SI')).'\', \''.$titulo_registrar.'\');')) !!}
					
					{{--<div class="tooltipg"><i class="fa fa-question-circle-o" aria-hidden="true"></i>
						<span class="tooltiptextg"> Alexander gastelo benavides </span>
					</div> --}}
				
					{!! Form::close() !!}
                </div>
            </div>
			<div id="listado{{ $entidad }}"></div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
		var fechaActual = new Date();
        var fechai = (fechaActual.getFullYear()-3) +"-01-01";

		$('#fechabusqueda').val(fechai);

        buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombres"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});
    });
    function abrirModalMant(rutaMant, titulo){
        if({{ $caja_id }} != 0){
            //var rutaMant = "{{  URL::route($ruta['create'], array('listar'=>'SI')) }}";
            modal(rutaMant,titulo);
        }else{
            bootbox.alert("<div class='alert alert-danger'><strong>¡Error!</strong> Caja no aperturada, asegurese de aperturar caja.!</div>");
        }
    }
/*
	//Funcion para abrir pdf en un modal
	function modalrecibopdf2(url_pdf, ancho_modal, titulo_modal){
		var motbx = bootbox.dialog({
		          message: '<object class="preview-pdf-file" type="application/pdf" data="'+url_pdf+'" width="100%" height="500px"></object><div class="modal-footer"><button type="button" class="btn btn-warning" id="btnCerrarPdf" >Close</button></div>',      
		          title: ""+titulo_modal,
		          "className" : "preview-pdf-modal",
		          onEscape: function() {}
		        });
		motbx.prop('id', 'modalvspdf');
		$('#modalvspdf').children('.modal-dialog').css('width','auto');
		$('#modalvspdf').children('.modal-dialog').css('max-width', ancho_modal+'px');
		$('#modalvspdf').css('resize', 'both');
		$('#btnCerrarPdf').click(function(){
			$('#modalvspdf').modal('hide');
		});
	}*/

	//Funcion para abrir pdf en una pestaña nueva del navegador
	function modalrecibopdf(url_pdf, ancho_modal, titulo_modal) {
		var a = document.createElement("a");
		a.target = "_blank";
		a.href = url_pdf;
		a.click();
	}

</script>