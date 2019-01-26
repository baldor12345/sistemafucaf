<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open(array('route' => array('acciones.guardarventa', $persona->id),'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('idpropietario', $persona->id, array('id' => 'idpropietario')) !!}

<div>
	{!! Form::label('dni', 'Comprador:', array('class' => 'col-sm-2 col-xs-12 control-label')) !!}
	<div class="col-sm-10 col-xs-12">
		{!! Form::select('selectnom', $cboPers, null, array('class' => 'form-control input-sm', 'id' => 'selectnom')) !!}
		<input type="hidden" id="persona_id", name="persona_id" value="">
	</div>
</div>

<input type="hidden" id="cantaccionpersona", name="cantaccionpersona" value="">
<input type="hidden" id="cantacciontotal", name="cantacciontotal" value="">
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label('cantidad_accion', 'Cantidad:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
			<div class="col-sm-8 col-xs-12">
				{!! Form::text('cantidad_accion', null, array('class' => 'form-control input-xs input-number', 'id' => 'cantidad_accion', 'placeholder' => 'Ingrese cantidad')) !!}
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label('configuraciones_id', 'Precio:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::select('configuraciones_id', $cboConfiguraciones, null, array('class' => 'form-control input-xs', 'id' => 'configuraciones_id')) !!}
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

<div class="form-group">
	{!! Form::label('concepto_id', 'Propietario:', array('class' => 'col-sm-2 col-xs-12 control-label')) !!}
	<div class="col-sm-10 col-xs-12">
			{!! Form::text('nombres', $nom , array('class' => 'form-control input-xs ', 'id' => 'nombres', 'placeholder' => '','readonly')) !!}
	</div>
</div>	



<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-2 col-xs-12 control-label')) !!}
	<div class="col-sm-10 col-xs-12">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'descripcion')) !!}
	</div>
</div>


<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardaraccionventa(\''.$entidad.'\', \''.URL::route($ruta["reciboaccionventapdf"], array()).'\')')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}


<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('600');

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		$('#fechai').val(fechai);

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

				var comprador_id = parseInt(event.target.value);
				var  Propietario_id = parseInt('{{ $persona->id }}');
				if(comprador_id == Propietario_id){
					document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-danger' role='alert'><span >Los datos ingresados deben ser diferentes de la persona que vende!!</span></div>";
									$('#divMensajeError{{ $entidad }}').show();
				}else{
					document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-success' role='alert'><span >Estimado Socio!</br>solo puede adquirir el 20% de la "+
								"cantidad total de las acciones por el cual usted puede adquirir solo: "+ cantidad_limite+" acciones GRACIAS!</span></div>";
					$('#divMensajeError{{ $entidad }}').show();
				}
				
				
			});
		});
		

		
	});


	$("input[name=cantidad_accion]").change(function(event){
		var cantidad = parseInt(event.target.value);
		var  cant_pers = parseInt('{{ $cant_acciones }}');
		if(cantidad > cant_pers){
			document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-danger' role='alert'><span >No cuenta con esa cantidad  de acciones para la venta!!</span></div>";
									$('#divMensajeError{{ $entidad }}').show();
		}else{
			$('#divMensajeError{{ $entidad }}').show();
		}
	});


	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	function guardaraccionventa(entidad, rutarecibo) {
		var  cant_persona = parseInt('{{ $cant_acciones }}');
		var  cantidad = parseInt($('#cantidad_accion').val());
		if(cantidad > cant_persona){
			document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-danger' role='alert'><span >No cuenta con esa cantidad de acciones para la venta!!</span></div>";
									$('#divMensajeError{{ $entidad }}').show();
		}else{
			$('#divMensajeError{{ $entidad }}').hide();
			var cant = $('')
			var idformulario = IDFORMMANTENIMIENTO + entidad;
			var data         = submitForm(idformulario);
			var respuesta    = null;
			var listar       = 'NO';
			if ($(idformulario + ' :input[id = "listar"]').length) {
				var listar = $(idformulario + ' :input[id = "listar"]').val()
			};
			data.done(function(msg) {
				respuesta = msg;
			}).fail(function(xhr, textStatus, errorThrown) {
				respuesta = 'ERROR';
			}).always(function() {
				if(respuesta[0] === 'ERROR'){
				}else{
					
					if (respuesta[0] === 'OK') {
						cerrarModal();
						modalrecibopdf(rutarecibo+"/"+respuesta[1]+"/"+respuesta[2]+"/"+respuesta[3]+"/"+respuesta[4], '100', 'recibo accion');
						if (listar === 'SI') {
							if(typeof entidad2 != 'undefined' && entidad2 !== ''){
								entidad = entidad2;
							}
							buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
						}        
					} else {
						mostrarErrores(respuesta, idformulario, entidad);
					}
				}
			});
		}
		
	}

</script>