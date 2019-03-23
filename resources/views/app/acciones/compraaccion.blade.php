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

{!! Form::open(array('route' => array('acciones.guardarcompra',$id),'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('persona_id', $id, array('id' => 'persona_id')) !!}
<div class="card-box">
<div class="form-group">
	{!! Form::label('nombres', 'Nombres:', array('class' => 'col-sm-2 col-xs-12 control-label')) !!}
	<div class="col-sm-10 col-xs-12">
			{!! Form::text('nombres', $nom , array('class' => 'form-control input-xs ', 'id' => 'nombres', 'placeholder' => '','readonly')) !!}
	</div>
</div>	

{!! Form::hidden('cantaccionpersona', $cantaccionpersona, array('id' => 'cantaccionpersona')) !!}
{!! Form::hidden('cant_menos_id_select', $cant_menos_id_select, array('id' => 'cant_menos_id_select')) !!}
{!! Form::hidden('cant_total', $cant_total, array('id' => 'cant_total')) !!}
{!! Form::hidden('cantidad_limite', null, array('id' => 'cantidad_limite')) !!}

<div class="row">
	<div class=" col-sm-4 col-xs-12">
		<div class="form-group">
			{!! Form::label('cantidad_accion', ' Cantidad:', array('class' => 'col-sm-6  col-xs-12 control-label')) !!}
			<div class="col-sm-6 col-xs-12">
				{!! Form::text('cantidad_accion', null, array('class' => 'form-control input-xs input-number', 'id' => 'cantidad_accion', 'placeholder' => '....', 'maxlength' => '3')) !!}
			</div>
		</div>
	</div>
	<div class=" col-sm-4 col-xs-12">
		<div class="form-group">
			{!! Form::label('configuraciones_id', 'Precio:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
			<div class="col-sm-8 col-xs-12">
				{!! Form::select('configuraciones_id', $cboConfiguraciones, $id_config, array('class' => 'form-control input-xs', 'id' => 'configuraciones_id')) !!}
			</div>
		</div>
	</div>
	<div class=" col-sm-4 col-xs-12">
		<div class="form-group">
			{!! Form::label('total', 'Total S/.:', array('class' => 'col-sm-6 col-xs-12 control-label')) !!}
			<div class="col-sm-6 col-xs-12">
				{!! Form::text('total', 0.0, array('class' => 'form-control input-xs', 'id' => 'total', 'readonly')) !!}
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-sm-6">
		<div class="form-group ">
			{!! Form::label('fechai', 'Fecha:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
			<div class="col-sm-8 col-xs-12">
				{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
			</div>
		</div>
	</div>
	<div class="col-sm-6">
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
		<div class="col-sm-6">
			<div class="form-group">
				{!! Form::label('contribucion_id', 'Concepto:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
				<div class="col-sm-8 col-xs-12">
					{!! Form::select('contribucion_id', $cboContribucion, null, array('class' => 'form-control input-xs', 'id' => 'contribucion_id')) !!}
				</div>
			</div>	
		</div>
		<div class="col-sm-6">
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
</div>

<div class="row">
	<div class=" col-sm-4 col-xs-12">
		<div class="form-group">
			{!! Form::label('monto_recibido', 'Monto Recibido S/.:', array('class' => 'col-sm-7  col-xs-12 control-label')) !!}
			<div class="col-sm-5 col-xs-12">
				{!! Form::text('monto_recibido', null, array('class' => 'form-control input-xs input-number', 'id' => 'monto_recibido', 'placeholder' => '....', 'maxlength' => '6')) !!}
			</div>
		</div>
	</div>
	<div class=" col-sm-4 col-xs-12">
		<div class="form-group">
			{!! Form::label('monto_pago', 'Monto Pago S/.:', array('class' => 'col-sm-7 col-xs-12 control-label')) !!}
			<div class="col-sm-5 col-xs-12">
				{!! Form::text('monto_pago', 0.0, array('class' => 'form-control input-xs', 'id' => 'monto_pago', 'readonly')) !!}
			</div>
		</div>
	</div>
	<div class=" col-sm-4 col-xs-12">
		<div class="form-group">
			{!! Form::label('monto_devolver', 'Diferencia S/.:', array('class' => 'col-sm-7 col-xs-12 control-label')) !!}
			<div class="col-sm-5 col-xs-12">
				{!! Form::text('monto_devolver', 0.0, array('class' => 'form-control input-xs', 'id' => 'monto_devolver', 'readonly')) !!}
			</div>
		</div>
	</div>
</div>



<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar Compra', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardaraccion', 'onclick' => 'guardaraccion(\''.$entidad.'\', \''.URL::route($ruta["reciboaccionpdf"], array()).'\')')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}

<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('650');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="cantidad_accion"]').focus();
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
		
		var cantidad_accion_persona = parseInt($('#cantaccionpersona').val());
		var cant_accion_total = parseInt($('#cant_total').val());
		$('#cantidad_limite').val(((cant_accion_total-(5*cantidad_accion_persona)))/4);
		var cantidad_limite = parseInt($('#cantidad_limite').val());
		if(cantidad_limite <0){
			cantidad_limite =0;
		}

		document.getElementById("infoaccion").innerHTML = "<div class='alert alert-success' role='success'><span >Estimado Socio!</br>solo puede adquirir el 20% de la "+
						"cantidad total de las acciones por el cual usted puede adquirir solo: "+ cantidad_limite+" acciones GRACIAS!</span></div>";
			$('#infoaccion').show();

		
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
				$('#monto_pago').val(cantidad_ingresad*precio);
			}else{
				$('#total').val('0.0');
				$('#monto_pago').val('0.0');
			}
		});

		$("input[name=monto_recibido]").change(function(event){
			var monto_recibido = parseFloat($('#monto_recibido').val());
			var monto_pago = parseFloat($('#monto_pago').val());
			if(monto_recibido<monto_pago){
				document.getElementById("infoaccion2").innerHTML = "<div class='alert alert-danger' role='danger'><span >Monto Recibido debe ser mayor a Monto de pago</span></div>";
				$('#infoaccion2').show();
			}else{
				if(monto_recibido != 0.0){
					$('#monto_devolver').val(monto_recibido-monto_pago);
				}else{
					$('#monto_devolver').val('0.0');	
				}
			}
		});

		$("input[name=monto]").change(function(event){
			var contribucion = parseFloat($('#monto').val());
			var cantidad_ingresado = parseInt($('#cantidad_accion').val());
			var precio = '{{ $precio_accion }}';
			if(contribucion != 0){
				$('#monto_pago').val(cantidad_ingresado*precio+contribucion);

				var total_ = parseFloat($('#monto_pago').val());
				$('#monto_devolver').val(total_-(cantidad_ingresado*precio+contribucion));
			}
		});

		var cantidad = parseInt('{{$cantaccionpersona}}');
		if(cantidad == 0){
			document.getElementById('oculto').style.display = 'block';
		}else{
			document.getElementById('oculto').style.display = 'hide';
		}
        	
		
	}); 

	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	function guardaraccion(entidad, rutarecibo) {
		var lmite = parseInt($('#cantidad_limite').val());
		if(lmite < 0){
			lmite =0;
		}
		var cantid = parseInt($('#cantidad_accion').val());
		var accion_inicio = parseInt('{{ $cantaccionpersona }}');
		if(accion_inicio !=0){
			if(lmite<=cantid){
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
				if(lmite<=cantid){
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