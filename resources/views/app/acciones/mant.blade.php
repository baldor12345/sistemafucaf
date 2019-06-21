<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>
<div id="divMensajeError{!! $entidad !!}"></div>
<div id="infoaccion3"></div>
<div id="infoaccion"></div>
{!! Form::model($acciones, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('cant_ac',null, array('id' => 'cant_ac')) !!}

<div>
	{!! Form::label('dni', 'Socio:', array('class' => 'col-sm-2 col-xs-12 control-label')) !!}
	<div class="col-sm-10 col-xs-12">
		{!! Form::select('selectnom', $cboPers, null, array('class' => 'form-control input-sm', 'id' => 'selectnom')) !!}
	</div>
</div>
</br></br>

<div class="row">
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="form-group">
			{!! Form::label('cantidad_accion', ' Cantidad:', array('class' => 'col-sm-3  col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('cantidad_accion', null, array('class' => 'form-control input-xs input-number', 'id' => 'cantidad_accion', 'placeholder' => '....', 'maxlength' => '3')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="form-group">
			{!! Form::label('total', ' Total(S/.)', array('class' => 'col-sm-3  col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::text('total', null, array('class' => 'form-control input-xs input-number', 'id' => 'total', 'placeholder' => '....', 'maxlength' => '3','readonly')) !!}
			</div>
		</div>
	</div>

</div>

<div class="row">
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="form-group ">
			{!! Form::label('fechai', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-6 col-sm-12 col-xs-12">
		<div class="form-group">
			{!! Form::label('concepto_id', 'Concepto:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::select('concepto_id', $cboConcepto, null, array('class' => 'form-control input-xs', 'id' => 'concepto_id')) !!}
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
			{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-2 col-xs-12 control-label')) !!}
			<div class="col-sm-10 col-xs-12">
				{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'descripcion')) !!}
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarDevolucionC', 'onclick' => 'guardaraccion(\''.$entidad.'\', \''.URL::route($ruta["reciboaccionpdf"], array()).'\')')) !!}
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

		document.getElementById("infoaccion3").innerHTML = "<div class='alert alert-warning' role='warning'><span >saldo en caja {{ $diferencia }} S/.</span></div>";
		$('#infoaccion3').show();
		

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
			$.get("acciones/"+event.target.value+"/1",function(response2, acciones){
				$('#cant_ac').val(response2);
				document.getElementById("infoaccion").innerHTML = "<div class='alert alert-success' role='success'><span >Estimado Socio! "+
							"usted cuenta con: "+ response2+" acciones hasta la fecha!</span></div>";
				$('#infoaccion').show();
			});
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
		
	}); 

	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	function guardaraccion(entidad, rutarecibo) {
		var cant_accion = parseInt($('#cant_ac').val());
		var saldo_caja = parseFloat('{{ $diferencia }}');
		var monto_total_ = parseFloat($('#total').val());
		var cant_ingresada = parseInt($('#cantidad_accion').val());
		console.log("monto total  "+monto_total_);

		if(cant_ingresada > 0){
			$('#divMensajeErrorAcciones').hide();
			if(cant_accion>=cant_ingresada){
				$('#divMensajeErrorAcciones').hide();
				if(saldo_caja >= monto_total_){
					$('#divMensajeErrorAcciones').hide();
					var idformulario = IDFORMMANTENIMIENTO + entidad;
					var data         = submitForm(idformulario);
					var respuesta    = null;
					var listar       = 'NO';
					if ($(idformulario + ' :input[id = "listar"]').length) {
						var listar = $(idformulario + ' :input[id = "listar"]').val()
					};
					$('#btnGuardarDevolucionC').button('loading');
					data.done(function(msg) {
						respuesta = msg;
					}).fail(function(xhr, textStatus, errorThrown) {
						respuesta = 'ERROR';
						$('#btnGuardarDevolucionC').removeClass('disabled');
						$('#btnGuardarDevolucionC').removeAttr('disabled');
						$('#btnGuardarDevolucionC').html('<i class="fa fa-check fa-lg"></i>Guardar');
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
								$('#btnGuardarDevolucionC').removeClass('disabled');
								$('#btnGuardarDevolucionC').removeAttr('disabled');
								$('#btnGuardarDevolucionC').html('<i class="fa fa-check fa-lg"></i>Guardar');
							}
						}
					});
				}else{
					document.getElementById("divMensajeErrorAcciones").innerHTML = "<div class='alert alert-danger' role='danger'><span >No ay saldo suficiente en caja!</span></div>";
					$('#divMensajeErrorAcciones').show();
					$('#btnGuardarDevolucionC').removeClass('disabled');
					$('#btnGuardarDevolucionC').removeAttr('disabled');
					$('#btnGuardarDevolucionC').html('<i class="fa fa-check fa-lg"></i>Guardar');
				}
			}else{
				document.getElementById("divMensajeErrorAcciones").innerHTML = "<div class='alert alert-danger' role='danger'><span >No cuenta con esta cantidad ingresada de acciones!</span></div>";
					$('#divMensajeErrorAcciones').show();
					$('#btnGuardarDevolucionC').removeClass('disabled');
					$('#btnGuardarDevolucionC').removeAttr('disabled');
					$('#btnGuardarDevolucionC').html('<i class="fa fa-check fa-lg"></i>Guardar');
			}
		}else{
			document.getElementById("divMensajeErrorAcciones").innerHTML = "<div class='alert alert-danger' role='danger'><span >por favor ingrese cantidad</span></div>";
					$('#divMensajeErrorAcciones').show();
					$('#btnGuardarDevolucionC').removeClass('disabled');
					$('#btnGuardarDevolucionC').removeAttr('disabled');
					$('#btnGuardarDevolucionC').html('<i class="fa fa-check fa-lg"></i>Guardar');
		}
		
        
	}
	
	

</script>