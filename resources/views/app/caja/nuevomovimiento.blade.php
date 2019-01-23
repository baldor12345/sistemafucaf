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
{!! Form::open(array('route' => array('caja.registrarmovimiento', $id),'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
	{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => '')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('tipo', 'Tipo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
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

<div class="form-group">
	{!! Form::label('dni', 'DNI:', array('class' => 'col-sm-3 col-xs-12 control-label dnil')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('dni', null, array('class' => 'form-control input-xs input-number ', 'id' => 'dni', 'placeholder' => 'Asegurese de que el DNI ya este registrado...')) !!}
		<p id="nombres" class="" >DNI Vacio</p>
	</div>
	<input type="hidden" id="persona_id" name="persona_id" value="" >
</div>

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
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('400');
		$("#btnOculto").hide();
		$(".dnil").html('DNI: <sup style="color: blue;">Opcional</sup>');
		
		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fecha = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		$('#fecha').val(fecha);

		$("#persona_id").val('');
		$("input[name=dni]").change(function(event){
        	$.get("personas/"+event.target.value+"",function(response, persona){
				if(response.length>0){
					$("#nombres").html(response[0].nombres +" "+ response[0].apellidos);
					$("#persona_id").val(response[0].id);
				}else{
					$("#nombres").html("El DNI ingresado no existe");
					$("#persona_id").val('');
				}
			});
		});


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

</script>