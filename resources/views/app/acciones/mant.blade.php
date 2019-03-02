<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>
<div id="divMensajeError{!! $entidad !!}"></div>
<div id="infoaccion3"></div>
<div id="infoaccion2"></div>
<div id="infoaccion"></div>
{!! Form::model($acciones, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div>
	{!! Form::label('dni', 'Socio:', array('class' => 'col-sm-2 col-xs-12 control-label')) !!}
	<div class="col-sm-10 col-xs-12">
		{!! Form::select('selectnom', $cboPers, null, array('class' => 'form-control input-sm', 'id' => 'selectnom')) !!}
	</div>
</div>
</br></br>
<input type="hidden" id="cantaccionpersona", name="cantaccionpersona" value="">
<input type="hidden" id="cantacciontotal", name="cantacciontotal" value="">

<div class="row">
	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="form-group">
			{!! Form::label('cantidad_accion', ' Cantidad:', array('class' => 'col-sm-6  col-xs-12 control-label')) !!}
			<div class="col-sm-6 col-xs-12">
				{!! Form::text('cantidad_accion', null, array('class' => 'form-control input-xs input-number', 'id' => 'cantidad_accion', 'placeholder' => '....', 'maxlength' => '3')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="form-group">
			{!! Form::label('configuraciones_id', 'Precio:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
			<div class="col-sm-8 col-xs-12">
				{!! Form::select('configuraciones_id', $cboConfiguraciones, $id_config, array('class' => 'form-control input-xs', 'id' => 'configuraciones_id')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-4 col-sm-12 col-xs-12">
		<div class="form-group">
			{!! Form::label('total', 'Total S/.:', array('class' => 'col-sm-6 col-xs-12 control-label')) !!}
			<div class="col-sm-6 col-xs-12">
				{!! Form::text('total', 0.0, array('class' => 'form-control input-xs', 'id' => 'total', 'readonly')) !!}
			</div>
		</div>
	</div>
</div>


<div class="row">
	<div class="col-md-6">
		<div class="form-group ">
			{!! Form::label('fechai', 'Fecha:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
			<div class="col-sm-8 col-xs-12">
				{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label('concepto_id', 'Concepto:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::select('concepto_id', $cboConcepto, null, array('class' => 'form-control input-xs', 'id' => 'concepto_id')) !!}
			</div>
		</div>
	</div>
</div>

<div class="row" id='oculto' style="display:none;">
	<fieldset> 
		<div class="col-md-12">
			<p style="font-family: italic; font size: 16px; color:#FF0000">Contribucion de Ingreso como nuevo Socio</p>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				{!! Form::label('contribucion_id', 'Concepto:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
				<div class="col-sm-8 col-xs-12">
					{!! Form::select('contribucion_id', $cboContribucion, null, array('class' => 'form-control input-xs', 'id' => 'contribucion_id')) !!}
				</div>
			</div>	
		</div>
		<div class="col-md-6">
			<div class="form-group ">
				{!! Form::label('monto', 'Monto S/.:', array('class' => 'col-sm-5 col-xs-12 control-label')) !!}
				<div class="col-sm-7 col-xs-12">
					{!! Form::text('monto', null , array('class' => 'form-control input-xs', 'id' => 'monto', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
				</div>
			</div>
		</div>
	</fieldset>
</div>


<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-2 col-xs-12 control-label')) !!}
	<div class="col-sm-10 col-xs-12">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'descripcion')) !!}
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardaraccion', 'onclick' => 'guardaraccion(\''.$entidad.'\', \''.URL::route($ruta["reciboaccionpdf"], array()).'\')')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}


<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('570');

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		var fecha_caja = '{{ $fecha_caja }}';
		if(fecha_caja != 0){
			$('#fechai').val(fecha_caja);
		}else{
			$('#fechai').val(fechai);
		}
		

		$('#selectnom').select2({
            dropdownParent: $("#modal"+(contadorModal-1)),
            
            minimumInputLenght: 2,
            ajax: {
               
                url: "{{ URL::route($ruta['listpersonas'], array()) }}",
                dataType: 'json',
                delay: 250,
                data: function(params){
                    return{
                        q: $.trim(params.term)
                    };
                },
                processResults: function(data){
                    return{
                        results: data
                    };
                },
                cache: true
            }
        });

		$('#selectnom').change(function(event){
			$.get("acciones/"+$(this).val()+"", function(response, acciones){
				var cantAcciones=0;			
				if(response.length !=0 ){
					for(i=0; i<response.length; i++){
						cantAcciones+=  parseInt(response[i].cantidad_accion_acumulada);
					}

					var limite_accionPor= response[0].limite_acciones;
					var cantidad_limite = parseInt(cantAcciones*limite_accionPor);
				}
				$.get("acciones/"+event.target.value+"/1",function(response2, acciones){
					$('#cantaccionpersona').val(response2);
					$('#cantacciontotal').val(cantAcciones);
				});
				
				document.getElementById("infoaccion").innerHTML = "<div class='alert alert-success' role='success'><span >Estimado Socio!</br>solo puede adquirir el 20% de la "+
								"cantidad total de las acciones por el cual usted puede adquirir solo: "+ cantidad_limite+" acciones GRACIAS!</span></div>";
					$('#infoaccion').show();
			});
		});

		
		$('#selectnom').change(function(event){
			var fecha = $('#fechai').val();
			$.get("acciones/"+$(this).val()+"/"+fecha+"/1/1", function(response, acciones){
				var cant_acciones=0;
				var cant_acciones = response;
				if(cant_acciones != 0){
					document.getElementById("infoaccion3").innerHTML = "<div class='alert alert-warning' role='warning'><span >Socio seleccionado ya compro "+cant_acciones+" en esta fecha</span></div>";
					$('#infoaccion').show();
				}
				
			});
		});


		
		$("#imprimir_voucher").change(function(event) {
            var checkbox = event.target;
            if (checkbox.checked) {
                $("#imprimir_voucher").val(1);
            } else {
                $("#imprimir_voucher").val(0);
            }
        });

	
		$("input[name=cantidad_accion]").change(function(event){
			var cantidad_ingresad = parseInt($('#cantidad_accion').val());
			var precio = '{{ $precio_accion }}';
			if(cantidad_ingresad != 0){
				$('#total').val(cantidad_ingresad*precio);
			}else{
				$('#total').val('0.0');
			}
		});


		$("input[name=cantidad_accion]").change(function(event){
			var cantidad = parseInt($('#cantaccionpersona').val());
			
			if(cantidad == 0){
				document.getElementById('oculto').style.display = 'block';
			}else{
				document.getElementById('oculto').style.display = 'hide';
			}
		});
        	
		
	}); 

	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	function guardaraccion(entidad, rutarecibo) {
		var cantidad_limite = parseInt($('#cantacciontotal').val()*0.2);
		var accion_persona1 = parseInt($('#cantaccionpersona').val());
		var lmite = (cantidad_limite-accion_persona1);
		if(lmite < 0){
			lmite =0;
		}
		var cantid = $('#cantidad_accion').val();
		var accion_inicio = parseInt($('#cantaccionpersona').val());
		if(accion_inicio !=0){
			if(lmite>=cantid){
				var idformulario = IDFORMMANTENIMIENTO + entidad;
				var data         = submitForm(idformulario);
				var respuesta    = null;
				var listar       = 'NO';
				if ($(idformulario + ' :input[id = "listar"]').length) {
					var listar = $(idformulario + ' :input[id = "listar"]').val()
				};
				$('#btnGuardaraccion').button('loading');
				data.done(function(msg) {
					respuesta = msg;
				}).fail(function(xhr, textStatus, errorThrown) {
					respuesta = 'ERROR';
					$('#btnGuardaraccion').removeClass('disabled');
					$('#btnGuardaraccion').removeAttr('disabled');
					$('#btnGuardaraccion').html('<i class="fa fa-check fa-lg"></i>Guardar');
				}).always(function() {
					
					if(respuesta[0] === 'ERROR'){
					}else{
						
						if (respuesta[0] === 'OK') {
							cerrarModal();
							modalrecibopdf(rutarecibo+"/"+respuesta[1]+"/"+respuesta[2]+"/"+respuesta[3], '100', 'recibo accion');
							if (listar === 'SI') {
								if(typeof entidad2 != 'undefined' && entidad2 !== ''){
									entidad = entidad2;
								}
								buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
							}        
						} else {
							mostrarErrores(respuesta, idformulario, entidad);
							$('#btnGuardaraccion').removeClass('disabled');
							$('#btnGuardaraccion').removeAttr('disabled');
							$('#btnGuardaraccion').html('<i class="fa fa-check fa-lg"></i>Guardar');
						}
					}
				});
			}else{
				document.getElementById("infoaccion2").innerHTML = "<div class='alert alert-danger' role='danger'><span >la cantidad maxima que puede adquirir es "+lmite+"</span></div>";
				$('#infoaccion2').show();
				$('#btnGuardaraccion').removeClass('disabled');
				$('#btnGuardaraccion').removeAttr('disabled');
				$('#btnGuardaraccion').html('<i class="fa fa-check fa-lg"></i>Guardar');
			}
		}else{
			var contribucion = $('#monto').val();
			if(contribucion != ''){
				if(lmite>=cantid){
					var idformulario = IDFORMMANTENIMIENTO + entidad;
					var data         = submitForm(idformulario);
					var respuesta    = null;
					var listar       = 'NO';
					if ($(idformulario + ' :input[id = "listar"]').length) {
						var listar = $(idformulario + ' :input[id = "listar"]').val()
					};
					$('#btnGuardaraccion').button('loading');
					data.done(function(msg) {
						respuesta = msg;
					}).fail(function(xhr, textStatus, errorThrown) {
						respuesta = 'ERROR';
						$('#btnGuardaraccion').removeClass('disabled');
						$('#btnGuardaraccion').removeAttr('disabled');
						$('#btnGuardaraccion').html('<i class="fa fa-check fa-lg"></i>Guardar');
					}).always(function() {
						
						if(respuesta[0] === 'ERROR'){
						}else{
							
							if (respuesta[0] === 'OK') {
								cerrarModal();
								modalrecibopdf(rutarecibo+"/"+respuesta[1]+"/"+respuesta[2]+"/"+respuesta[3], '100', 'recibo accion');
								if (listar === 'SI') {
									if(typeof entidad2 != 'undefined' && entidad2 !== ''){
										entidad = entidad2;
									}
									buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
								}        
							} else {
								mostrarErrores(respuesta, idformulario, entidad);
								$('#btnGuardaraccion').removeClass('disabled');
								$('#btnGuardaraccion').removeAttr('disabled');
								$('#btnGuardaraccion').html('<i class="fa fa-check fa-lg"></i>Guardar');
							}
						}
					});
				}else{
					document.getElementById("infoaccion").innerHTML = "<div class='alert alert-warning' role='warning'><span >la cantidad maxima que puede adquirir es "+lmite+"</span></div>";
					$('#infoaccion').show();
					$('#btnGuardaraccion').removeClass('disabled');
					$('#btnGuardaraccion').removeAttr('disabled');
					$('#btnGuardaraccion').html('<i class="fa fa-check fa-lg"></i>Guardar');
				}
			}else{
				document.getElementById("infoaccion2").innerHTML = "<div class='alert alert-danger' role='danger'><span >por favor ingrese monto contribucion de ingreso</span></div>";
					$('#infoaccion2').show();
			}
		}
		
        
	}
	
	

</script>