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
						{!! Form::label('nombreAcr', 'Nombre:', array('class' => 'input-sm')) !!}
						{!! Form::text('nombreAcr', '', array('class' => 'form-control input-sm', 'id' => 'nombreAcr')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('fecha', 'Fecha:', array('class' => 'input-sm')) !!}
						{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('estado', 'Estado:', array('class' => 'input-sm')) !!}
						{!! Form::select('estado', $cboEstado, null, array('class' => 'form-control input-sm', 'id' => 'estado')) !!}
					</div>

					<div class="form-group">
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
					</div>
					{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo Credito', array('data-toggle'=>'modal', 'data-target'=>'#creditoManModal','class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevo', 'onclick' => '#')) !!}
					{!! Form::close() !!}
                </div>
            </div>

			<div id="listado{{ $entidad }}"></div>
			
            <table id="datatable" class="table table-striped table-bordered">
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="creditoManModal" tabindex="-1" role="dialog" aria-labelledby="creditoManModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      </div>
      <div class="modal-body">
		<fieldset>
		<div id="divMensajeError"></div>
		<form id="formMantCredito" action="">
			
			<div class="form-row">
				<div class="form-group col-6 col-md-6 col-sm-6">
					{!! Form::label('valor_credito', 'Valor de Credito:', array('class' => '')) !!}
					{!! Form::text('valor_credito', null, array('class' => 'form-control input-xs input-number', 'id' => 'valor_credito', 'placeholder' => 's/.')) !!}
				</div>
				<div class="form-group col-6 col-md-6 col-sm-6">
					{!! Form::label('cantidad_cuotas', 'N° Cuotas:', array('class' => '')) !!}
					{!! Form::text('cantidad_cuotas', null, array('class' => 'form-control input-xs input-number', 'id' => 'cantidad_cuotas', 'placeholder' => 'Ingrese Numero de cuotas', 'maxlength' => '8')) !!}
				</div>
			</div>

			<div class="form-row">
				<div class="form-group col-6 col-md-6 col-sm-6">
					{!! Form::label('comision', 'Interes mensual (%):', array('class' => '')) !!}
					{!! Form::text('comision', 20, array('class' => 'form-control input-xs', 'id' => 'comision', 'placeholder' => 'Ingrese el interes mensual')) !!}
				</div>
				<div class="form-group col-6 col-md-6 col-sm-6" >
					{!! Form::label('fechacred', 'Fecha:', array('class' => '')) !!}
					{!! Form::date('fechacred', null, array('class' => 'form-control input-xs', 'id' => 'fechacred')) !!}
				</div>
			</div>

			<div class="form-row">
				<div id='txtcliente' class="form-group col-6 col-md-6 col-sm-6">
				{!! Form::label('dnicl', 'DNI del Cliente:', array('class' => '')) !!}
				{!! Form::text('dnicl', null, array('class' => 'form-control input-xs', 'id' => 'dnicl', 'placeholder' => 'Ingrese el DNI del cliente')) !!}
				<p id="nombrescl" class="" ></p>
				<input type="hidden" id="idcl" name="idcl" value="" tipocl=''>
				</div>

				<div id='txtaval' class="form-group col-6 col-md-6 col-sm-6">
				{!! Form::label('dniavl', 'DNI del Aval:', array('class' => '')) !!}
				{!! Form::text('dniavl', 	null, array('class' => 'form-control input-xs', 'id' => 'dniavl', 'placeholder' => 'Ingrese el DNI del Aval')) !!}
				<p id="nombresavl" class="" ></p>
				<input type="hidden" id="idavl", name="idavl" value="" tipoavl=''>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12 text-right">
					{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarcredito();')) !!}
					&nbsp;
					{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
				</div>
			</div>
		</form>
		</fieldset>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
	$(document).ready(function () {

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth()+1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		//var fechai = (fechaActual.getFullYear()) +"-"+month+"-01";
		//var fechaf = (fechaActual.getFullYear() +1) + "-"+month+"-"+day;
		
		configurarAnchoModal('');

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-01";
		var fechai2 = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		//var fechaf = (fechaActual.getFullYear() +1) + "-"+month+"-"+day;
		$('#fecha').val(fechai);
		$('#fechacred').val(fechai2);
		//$('#txtaval').hide();
		$("#dniavl").prop('disabled', true);
		//$('#fechaf').val(fechaf);
		buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
		$(IDFORMBUSQUEDA + '{{ $entidad }} :input[id="nombreAcr"]').keyup(function (e) {
			var key = window.event ? e.keyCode : e.which;
			if (key == '13') {
				buscar('{{ $entidad }}');
			}
		});

		$("input[name=dnicl]").keyup(function(event){
        	$.get("personas/"+event.target.value+"",function(response, facultad){
				console.log("datos de la persona");
				console.log(response);
				$('#nombrescl').val('');
				$('#idcl').val('');
				$('#idavl').val('');
			//	document.getElementById("nombrescl").innerHTML = response[0].nombres +" "+ response[0].apellidos;
				if(response.length>0){
				for(i=0; i<response.length; i++){
					document.getElementById("nombrescl").innerHTML = response[i].nombres +" "+ response[i].apellidos;
					document.getElementById("idcl").value = response[i].id;
					console.log(response[i].tipo);
					if( response[i].tipo.trim() == 'S'){
						$("#dniavl").prop('disabled', true);
					}else{
						$("#dniavl").prop('disabled', false);
					}
				}}else{
					$("#dniavl").prop('disabled', true);
					document.getElementById("nombrescl").innerHTML = "El DNI ingresado no existe";
				}
			});
    	});

		$("input[name=dniavl]").keyup(function(event){
        $.get("personas/"+event.target.value+"",function(response, facultad){
				$('#nombresavl').val('');
				$('#idavl').val('');
				if(response.length>0){
					for(i=0; i<response.length; i++){
						document.getElementById("nombresavl").innerHTML = response[i].nombres +" "+ response[i].apellidos;
						document.getElementById("idavl").value = response[i].id;
					}
				}else{
					document.getElementById("nombresavl").innerHTML = "El DNI ingresado no existe";
				}
			});
    });

	});

	function guardarcredito(){

		$.ajax({
			url: 'creditos/guardarcredito',
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			data: $('#formMantCredito').serialize(),
			beforeSend: function(){
				
	        },
	        success: function(res){
				$('#divMensajeError').html(res);
				buscar('{{ $entidad }}');
				$('#valor_credito').val('');
				$('#cantidad_cuotas').val('');
				$('#dnicl').val('');
				$('#dniavl').val('');
				$('#nombrescl').empty();
				$('#nombresavl').empty();
				$('#idcl').val('');
				$('#idavl').val('');
				$('#txtaval').hide();
				//cerrarModal();
				$('#creditoManModal').modal('hide');
	        }
		}).fail(function(){
			//$('.incorrecto').removeClass('hidden');
			//$('.correcto').addClass('hidden');
		});

	}

</script>