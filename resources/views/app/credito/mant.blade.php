
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($credito, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
		{!! Form::label('valor_credito', 'Valor de Credito:', array('class' => '')) !!}
		{!! Form::text('valor_credito', null, array('class' => 'form-control input-xs input-number', 'id' => 'valor_credito', 'placeholder' => 's/.')) !!}

    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
		{!! Form::label('cantidad_cuotas', 'N° Cuotas:', array('class' => '')) !!}
		{!! Form::text('cantidad_cuotas', null, array('class' => 'form-control input-xs input-number', 'id' => 'cantidad_cuotas', 'placeholder' => 'Ingrese Numero de cuotas', 'maxlength' => '8')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
		{!! Form::label('comision', 'Interes mensual (%):', array('class' => '')) !!}
		{!! Form::text('comision', 20, array('class' => 'form-control input-xs', 'id' => 'comision', 'placeholder' => 'Ingrese el interes mensual')) !!}
    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
		{!! Form::label('fechacred', 'Fecha:', array('class' => '')) !!}
		{!! Form::date('fechacred', null, array('class' => 'form-control input-xs', 'id' => 'fechacred')) !!}
    </div>

		<div id='txtcliente' class="form-group col-md-6 col-sm-6">
		{!! Form::label('dnicl', 'DNI del Cliente:', array('class' => '')) !!}
		{!! Form::text('dnicl', null, array('class' => 'form-control input-xs', 'id' => 'dnicl', 'placeholder' => 'Ingrese el DNI del cliente')) !!}
    <p id="nombrescl" class="" ></p>
		<input type="hidden" id="idcl" name="idcl" value="" tipocl=''>
		</div>

		<div id='txtaval' class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
		{!! Form::label('dniavl', 'DNI del Aval:', array('class' => '')) !!}
		{!! Form::text('dniavl', 	null, array('class' => 'form-control input-xs', 'id' => 'dniavl', 'placeholder' => 'Ingrese el DNI del Aval')) !!}
    <p id="nombresavl" class="" ></p>
		<input type="hidden" id="idavl", name="idavl" value="" tipoavl=''>
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
		//var fechai = (fechaActual.getFullYear()) +"-"+month+"-01";
		//var fechaf = (fechaActual.getFullYear() +1) + "-"+month+"-"+day;
		$('#fechacred').val(fechai);
		$('#txtaval').hide();

		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('650');

		$("input[name=dnicl]").change(function(event){
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
						$('#txtaval').hide();
					}else{
						$('#txtaval').show();
					}
				}}else{
				
					document.getElementById("nombrescl").innerHTML = "El DNI ingresado no existe";
				}
			});
    });

		$("input[name=dniavl]").change(function(event){
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
	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});
</script>