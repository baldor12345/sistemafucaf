<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>


<script>
function cargarselect2(entidad){
		var select = $('#tipo_id').val();

		if(select == ''){
			$('#concepto_id').html('<option value="" selected="selected">Seleccione</option>');
			return false;
		}

		route = 'caja/cargarselect/' + select + '?entidad=' + entidad + '&t=si';

		$.ajax({
			url: route,
			headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
			type: 'GET',
			beforeSend: function() {
				$('#concepto_id').html('<option value="" selected="selected">Seleccione</option>');
			},
	        success: function(res){
	        	$('#concepto_id').html(res);
	        }
		});
}



</script>

<div id="divMensajeError{!! $entidad !!}"></div>
<div id="divinfo"></div>
<div id="divinfo2"></div>
{!! Form::open(array('route' => array('caja.registrarmovimiento', $id),'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
	{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha', null, array('class' => 'form-control input-xs','min'=>'', 'max'=>'', 'id' => 'fecha', 'placeholder' => '')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('tipo_id', 'Tipo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('tipo_id', $cboTipo, null, array('class' => 'form-control input-xs', 'id' => 'tipo_id', 'onchange' => 'cargarselect2("concepto")')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('concepto_id', 'Concepto:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('concepto_id', $cboConceptos, null, array('class' => 'form-control input-xs', 'id' => 'concepto_id')) !!}
	</div>
</div>


<div>
	{!! Form::label('selectnom', 'Socio:', array('class' => 'col-sm-3 col-xs-12 control-label ')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('selectnom', $cboPers, null, array('class' => 'form-control input-sm', 'id' => 'selectnom')) !!}
	</div>
</div>
</br></br>

<div class="form-group" id="btnOculto">
	<div class="col-sm-6 col-xs-12 control-label form-check form-check-inline">
		<input checked class="form-check-input" type="radio" name="editable" id="editableno" value="1">
		<label class="form-check-label" for="editableno">Administrativo</label>
	</div>
	<div class="col-sm-6 col-xs-12 control-label form-check form-check-inline">
		<input class="form-check-input" type="radio" name="editable" id="editablesi" value="0">
		<label class="form-check-label" for="editablesi">Otros</label>
	</div>
</div>

<div class="form-group">
	{!! Form::label('total', 'Total(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('total', null, array('class' => 'form-control input-xs ', 'id' => 'total', 'placeholder' => 'S/.',  'onkeypress'=>'return filterFloat(event,this);')) !!}
	</div>
</div>


<div class="form-group">
	{!! Form::label('comentario', 'Comentario:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('comentario', null, array('class' => 'form-control input-xs', 'id' => 'comentario', 'placeholder' => '')) !!}
	</div>
</div>


<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarmovim', 'onclick' => 'guardarmoviemiento(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('450');
		$("#btnOculto").hide();
		$(".dni").html('DNI: <sup style="color: blue;">Opcional</sup>');
		$('#divinfo').html('<div class="alert bg-warning" role="alert"><strong>SALDO EN CAJA (S/.): </strong>{{ $diferencia }}</div>');
		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fecha = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		var fecha_caja = '{{ $fecha_caja }}';
		$('#fecha').val(fecha_caja);
		

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
                }
                
            }
        });

	}); 

	$("input[name=concepto_id]").change(function(){
		var concepto_id = $('#concepto_id').val();
		if(parseInt(concepto_id) == 22){
			document.getElementById("divinfo2").innerHTML = "<div class='alert alert-info' role='info'><span >por el concepto seleccionado se registrara en el rubro de REC. CAPITAL!</span></div>";
			$('#divinfo2').show();
		}
		
	});


	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	function filterFloat(evt,input){
		// Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
		var key = window.Event ? evt.which : evt.keyCode;    
		var chark = String.fromCharCode(key);
		var tempValue = input.value+chark;
		if(key >= 48 && key <= 57){
			if(filter(tempValue)=== false){
				return false;
			}else{       
				return true;
			}
		}else{
			if(key == 8 || key == 13 || key == 0) {     
				return true;              
			}else if(key == 46){
					if(filter(tempValue)=== false){
						return false;
					}else{       
						return true;
					}
			}else{
				return false;
			}
		}
	}
	function filter(__val__){
		var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
		if(preg.test(__val__) === true){
			return true;
		}else{
		return false;
		}
		
	}

	$(function() {
		$("#tipo_id").on('change', function() {
			var valor = $(this).val();
			switch (valor) {
				case "E":
				$("#btnOculto").show();
				break;

				case "I":
				$("#btnOculto").hide();
				break;
			}
		}).change();
	});

	function guardarmoviemiento(entidad){
		var fecha_de_caja = '{{ $fecha_caja }}';
		var fecha_seleccionada = $('#fecha').val();
		var saldo = parseFloat('{{ $diferencia }}');
		var monto_ingresado = parseFloat($('#total').val());
		var tipo_id = $('#tipo_id').val();
		if(fecha_de_caja>fecha_seleccionada){
			document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-danger' role='alert'><span >La fecha ingresada no puede ser menor que la fecha de apertura de caja, gracias!</span></div>";
			$('#divMensajeError{{ $entidad }}').show();
			$('#btnGuardarmovim').removeClass('disabled');
			$('#btnGuardarmovim').removeAttr('disabled');
			$('#btnGuardarmovim').html('<i class="fa fa-check fa-lg"></i>Guardar');
		}else{
			if(tipo_id === "E"){
				if(monto_ingresado>saldo){
					document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-danger' role='alert'><span >Saldo de caja insuficiente!</span></div>";
					$('#divMensajeError{{ $entidad }}').show();
					$('#btnGuardarmovim').removeClass('disabled');
					$('#btnGuardarmovim').removeAttr('disabled');
					$('#btnGuardarmovim').html('<i class="fa fa-check fa-lg"></i>Guardar');
				}else{
					guardar2(entidad);
				}
			}
			if(tipo_id === "I"){
				var concepto_id = $('#concepto_id').val();
				var persona_id = $('#selectnom').val();
				if(concepto_id == 22){
					if(persona_id != 0){
						guardar2(entidad);
					}else{
						document.getElementById("divMensajeError{{ $entidad }}").innerHTML = "<div class='alert alert-danger' role='alert'><span >Seleccione socio o cliente</span></div>";
						$('#divMensajeError{{ $entidad }}').show();
						$('#btnGuardarmovim').removeClass('disabled');
						$('#btnGuardarmovim').removeAttr('disabled');
						$('#btnGuardarmovim').html('<i class="fa fa-check fa-lg"></i>Guardar');
					}
				}else{
					guardar2(entidad);
				}
				
			}
		}
	}

	function guardar2 (entidad) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var data         = submitForm(idformulario);
		var respuesta    = '';
		var listar       = 'NO';
		if ($(idformulario + ' :input[id = "listar"]').length) {
			var listar = $(idformulario + ' :input[id = "listar"]').val()
		};
		$('#btnGuardarmovim').button('loading');
		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
			$('#btnGuardarmovim').removeClass('disabled');
			$('#btnGuardarmovim').removeAttr('disabled');
			$('#btnGuardarmovim').html('<i class="fa fa-check fa-lg"></i>Guardar');
		}).always(function() {
			if(respuesta === 'ERROR'){
			}else{
				if (respuesta === 'OK') {
					cerrarModal();
					if (listar === 'SI') {
						buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
						
					}        
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
					$('#btnGuardarmovim').removeClass('disabled');
					$('#btnGuardarmovim').removeAttr('disabled');
					$('#btnGuardarmovim').html('<i class="fa fa-check fa-lg"></i>Guardar');
				}
			}
		});
	}


</script>