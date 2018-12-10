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
					{!! Form::button('<i class="glyphicon glyphicon-plus"></i> Nuevo Credito', array('data-toggle'=>'modal', 'data-target'=>'#'.($idcaja == 0?'modal_validador':'creditoManModal'),'class' => 'btn btn-info waves-effect waves-light m-l-10 btn-md', 'id' => 'btnNuevo', 'onclick' => '#')) !!}
					{!! Form::close() !!}
                </div>
            </div>
			<div id="listado{{ $entidad }}"></div>
            <table id="datatable" class="table table-striped table-bordered">
            </table>
        </div>
    </div>
</div>
<!--MODAL MANTENIMIENTO DE CREDITO-->
<div class="modal fade" id="creditoManModal" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-labelledby="creditoManModal" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
	  	<h5 class="modal-title" id="exampleModalLabel">NUEVO CREDITO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
		<fieldset>
		<div class="card-box table-responsive">
		<div id="divMensajeError" class="alert alert-danger"></div>
		<form id="formMantCredito" action="">
			<div class="form-row">
				<div id='txtcliente' class="form-group col-6 col-md-6 col-sm-6">
					{!! Form::label('dnicl', 'DNI del Cliente: *', array('class' => '')) !!}
					{!! Form::text('dnicl', null, array('class' => 'form-control input-xs', 'id' => 'dnicl', 'placeholder' => 'Ingrese el DNI del cliente')) !!}
					<p id="nombrescl" class="" >DNI Cliente Vacio</p>
					<input type="hidden" id="idcl" name="idcl" value="" tipocl=''>
				</div>

				<div id='txtaval' class="form-group col-6 col-md-6 col-sm-6">
					{!! Form::label('dniavl', 'DNI del Aval:', array('id' => 'lblavl', 'class' => '')) !!}
					{!! Form::text('dniavl', 	null, array('class' => 'form-control input-xs', 'id' => 'dniavl', 'placeholder' => 'Ingrese el DNI del Aval')) !!}
					<p id="nombresavl" class="" >DNI Aval Vacio</p>
					<input type="hidden" id="idavl", name="idavl" value="" tipoavl=''>
				</div>
				<div class="form-group col-6 col-md-6 col-sm-6">
					{!! Form::label('valor_credito', 'Valor de Credito: *', array('class' => '')) !!}
					{!! Form::text('valor_credito', null, array('class' => 'form-control input-xs input-number', 'id' => 'valor_credito', 'placeholder' => 's/.')) !!}
				</div>
				<div class="form-group col-6 col-md-6 col-sm-6">
					{!! Form::label('tasa_interes', 'Interes mensual (%):', array('class' => '')) !!}
					{!! Form::text('tasa_interes', ($configuraciones->tasa_interes_credito*100).'', array('class' => 'form-control input-xs', 'id' => 'tasa_interes', 'placeholder' => 'Ingrese el interes mensual %')) !!}
				</div>
			
				<div class="form-group col-6 col-md-6 col-sm-6">
				{!! Form::label('periodo', 'Periodo (N° Meses): *', array('class' => '')) !!}
				{!! Form::text('periodo', null, array('class' => 'form-control input-xs', 'id' => 'periodo', 'placeholder' => 'Ingrese Numero de meses')) !!}
				</div>
			
				<div class="form-group col-6 col-md-6 col-sm-6" >
					{!! Form::label('fechacred', 'Fecha: *', array('class' => '')) !!}
					{!! Form::date('fechacred', null, array('class' => 'form-control input-xs', 'id' => 'fechacred')) !!}
				</div>
				<div class="form-group col-6 col-md-6 col-sm-6" >
					{!! Form::button('<i class="fa fa-check fa-lg"></i>Ver Cronograma', array('class' => 'btn btn-success btn-sm', 'id' => 'btnCronograma', 'onclick' => 'generarCronograma();')) !!}
				</div>
			</div>
			<div class="form-group">
				<div class="col-lg-12 col-md-12 col-sm-12 text-right">
					{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarcredito();')) !!}
					&nbsp;
					{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModalmancred();')) !!}
				</div>
			</div>
		</form>
		<div>
		</fieldset>
      </div>
    </div>
  </div>
</div>

<!--MODAL CRONOGRAMA DE CUOTAS -->
<div class="modal fade" id="cronogramaModal" tabindex="-1" role="dialog" aria-labelledby="cronogramaModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="exampleModalLabel">Cronograma de pagos</h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  	<fieldset class="col-12">
			<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
				<thead>
					<tr>
						<th style='width: 5%' class='text-center'>#</th>
						<th style='width: 10%'>INTERES</th>
						<th style='width: 30%' class='text-center'>PARTE CAPITAL</th>
						<th style='width: 30%' class='text-center'>MONTO CUOTA</th>
						<th style='width: 25%' class='text-center'>FECHA DE PAGO</th>
					</tr>
				</thead>
				<tbody id='filasTcuotas'>

				</tbody>
				<tfoot>
				</tfoot>
			</table>
			<div class="form-row">
				<div class="form-group col-12" >
					{!! Form::label('', 'Interes total: ', array('id'=>'interesTotal','class' => '')) !!}
					{!! Form::label('', 'Capital total: ', array('id'=>'capitalTotal','class' => '')) !!}
				</div>
			</div>
		</fieldset>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- MODAL VALIDACION CAJA APERTURADA-->
<div class="modal fade" id="modal_validador" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <div class="alert alert-danger">
		<strong>¡Error!</strong> Caja no aperturada, porfavor aperture primero. !
	  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
 <!--CODIGO JAVASCRIPT -->
<script>

	$(document).ready(function () {
		$('#divMensajeError').hide();
		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth()+1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		
		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-01";
		var fechai2 = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		
		$('#fecha').val(fechai);
		$('#fechacred').val(fechai2);
		$("#dniavl").prop('disabled', true);
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
				$('#nombrescl').val('');
				$('#idcl').val('');
				$('#idavl').val('');
				if(response.length>0){
				
					$("#nombrescl").html(response[0].nombres +" "+ response[0].apellidos);
					$("#idcl").val(response[0].id);
					if( response[0].tipo.trim() == 'S'){
						$("#idcl").attr('tipocl','s');
						$("#dniavl").prop('disabled', true);
						$("#lblavl").html('DNI del Aval:');
					}else{
						$("#idcl").attr('tipocl','c');
						$("#dniavl").prop('disabled', false);
						$("#lblavl").html('DNI del Aval: *');
					}
				}else{
					$("#dniavl").prop('disabled', true);
					$("#lblavl").html('DNI del Aval:');

					$("#nombrescl").html("El DNI ingresado no existe");
				}
			});
		});

		$("input[name=dniavl]").keyup(function(event){
        	$.get("personas/"+event.target.value+"",function(response, facultad){
				$('#nombresavl').val('');
				$('#idavl').val('');
				if(response.length>0){
					$("#nombresavl").html(response[0].nombres +" "+ response[0].apellidos);
					$("#idavl").val(response[0].id);
				}else{
					$("nombresavl").html("El DNI ingresado no existe");
				}
			});
    	});
	});
	
	function guardarcredito(){
		if(validarcamposman()){
			$.ajax({
				url: 'creditos/guardarcredito',
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
				type: 'GET',
				data: $('#formMantCredito').serialize(),
				beforeSend: function(){
				},
				success: function(res){
					if(res == "OK"){
						limpiar();
						buscar('{{ $entidad }}');
						$("#nombrescl").html("DNI Cliente Vacio");
						$('#nombresavl').html("DNI Aval Vacio");
						$('#creditoManModal').modal('hide');
					}else{
						document.getElementById("divMensajeError").innerHTML = res+"";
						$('#divMensajeError').show();
					}
				}
			}).fail(function(){
				document.getElementById("divMensajeError").innerHTML = "Ingrese todos los campos obligatorios!";
				$('#divMensajeError').show();
			});
		}else{
			document.getElementById("divMensajeError").innerHTML = "Asegurese de rellenar todos los campos obligatorios correctamente!";
			$('#divMensajeError').show();
		}
	}

	function cerrarModalmancred(){
		$('#creditoManModal').modal('hide');
	}
	function validarcamposman(){
		var res = true;
		if($('#periodo').val() == "" || $('#idcl').val() == ""|| $('#valor_credito').val() == "" || $('#tasa_interes').val() == ""){
			res = false;
		}
		if($("#idcl").attr('tipocl') == 'c'){
			if($('#idavl').val() == ''){
				res = false;
			}
		}
		return res;
	}
	function limpiar(){
		$('#valor_credito').val('');
		$('#periodo').val('');
		$('#dnicl').val('');
		$('#dniavl').val('');
		$('#divMensajeError').hide();
		$('#idcl').val('');
		$('#idavl').val('');
	}

	$("#creditoManModal").on('hidden.bs.modal', function () {
		limpiar();
		$('#divMensajeError').html('');
		$("#nombrescl").html("DNI Cliente Vacio");
		$('#nombresavl').html("DNI Aval Vacio");
	});
	

	function generarCronograma(){
		$('#filasTcuotas').empty();
		var periodo= parseInt($('#periodo').val());
		var Monto= parseFloat($('#valor_credito').val());
		var Interes= parseFloat($('#tasa_interes').val());
		var CapitalInicial= parseFloat($('#valor_credito').val());
		//var fecha = new Date(año,mes,dia);
		var montInteres=0.00;
		var montCapital=0.00;
		var montCuota = 0.00;
		var fechac = new Date($('#fechacred').val());
		fechac.setDate(fechac.getDate() + 1);
		var interesAcumulado=0.00;
		var capitalTotal = 0.00;
		var sumacuotas = 0.00;
		var fila='';
		//FORMULA: CUOTA = (Interes * CpitalInicial)/(1-  (1/ (1+InteresMensual)^NumeroCuotas)  );  Math.pow(7, 2);
		montCuota =((Interes/100) * CapitalInicial) / (1 - (Math.pow(1/(1+(Interes)/100), periodo)));
		var i=0;
		
		for(i=0; i<periodo; i++){
			fechac.setMonth(fechac.getMonth() + 1);
			var day = ("0" + fechac.getDate()).slice(-2);
			var month = ("0" + (fechac.getMonth() + 1)).slice(-2);
			montInteres =  (Interes/100)*CapitalInicial;
			interesAcumulado = montInteres + interesAcumulado;
			montCapital= montCuota - montInteres;
			CapitalInicial = CapitalInicial - montCapital;
			capitalTotal += montCapital;
			sumacuotas += montCuota;
			fila = fila + "<tr>"
					+"<td>"+(i+1)+"</td>"
					+"<td>"+RoundDecimal(montInteres,2)+"</td>"
					+"<td>"+RoundDecimal(montCapital,2)+"</td>"
					+"<td>"+RoundDecimal(montCuota,2)+"</td>"
					+"<td>"+fechac.getDate()+"/"+(fechac.getMonth()+1)+"/"+(fechac.getFullYear())+"</td>"
					+"</tr>";
		}
		
		interesAcumulado = interesAcumulado;
		fila += "<tr><td>TOTAL</td><td>"+RoundDecimal(interesAcumulado,2)+"</td><td>"+RoundDecimal(capitalTotal,2)+"</td><td>"+RoundDecimal(sumacuotas,2)+"</td></tr>";
		$("#filasTcuotas").append(fila);
		$('#cronogramaModal').modal('show');
		$('#interesToal').empty();
		$('#capitalTotal').empty();
		$('#interesTotal').text("Interes total: " +RoundDecimal(interesAcumulado,1));
		$('#capitalTotal').text("Total al finalizar: " + RoundDecimal(capitalTotal,1));

	}

	function RoundDecimal(numero, decimales) {
		numeroRegexp = new RegExp('\\d\\.(\\d){' + decimales + ',}');   // Expresion regular para numeros con un cierto numero de decimales o mas
		if (numeroRegexp.test(numero)) {         // Ya que el numero tiene el numero de decimales requeridos o mas, se realiza el redondeo
			return Number(numero.toFixed(decimales));
		} else {
			return Number(numero.toFixed(decimales)) === 0 ? 0 : numero;  // En valores muy bajos, se comprueba si el numero es 0 (con el redondeo deseado), si no lo es se devuelve el numero otra vez.
		}
	}

</script>