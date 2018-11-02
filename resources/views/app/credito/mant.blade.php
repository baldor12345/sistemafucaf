
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($credito, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

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
		<div class="form-group col-12" >
			{!! Form::label('descripcion', 'Descripción: ', array('class' => '')) !!}
			{!! Form::textarea('descripcion', null, array('class' => 'form-control input-sm','rows' => 4, 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción')) !!}
		</div>
		<div class="form-group col-6 col-md-6 col-sm-6" >
			{!! Form::label('btnCronograma', 'Ver cronograma de pagos: *', array('class' => '')) !!}
			{!! Form::button('<i class="fa fa-check fa-lg"></i> Cronograma', array('class' => 'btn btn-success btn-sm', 'id' => 'btnCronograma', 'onclick' => 'generarCronograma();')) !!}
		</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}


<script type="text/javascript">
	$(document).ready(function() {

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth()+1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		
		$('#fechacred').val(fechai);
		$("#dniavl").prop('disabled', true);
		console.log("INGRESO AQUI: "+fechai);

		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('650');

		
		$("input[name=dnicl]").keyup(function(event){
			$.get("personas/"+event.target.value+"",function(response, facultad){
				//console.log("datos de la persona");
				//console.log(response);
				$('#nombrescl').val('');
				$('#idcl').val('');
				$('#idavl').val('');
				if(response.length>0){
				
					$("#nombrescl").html(response[0].nombres +" "+ response[0].apellidos);
					console.log("esponse[0]: "+response[0].nombres +" "+ response[0].apellidos);
					$("#idcl").val(response[0].id);
					console.log(response[0].tipo);
					if( response[0].tipo.trim() == 'S'){
						$("#dniavl").prop('disabled', true);
						$("#lblavl").html('DNI del Aval:');
					}else{
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

</script>