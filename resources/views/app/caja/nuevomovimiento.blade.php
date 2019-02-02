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

<div id="divinfo"></div>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::open(array('route' => array('caja.registrarmovimiento', $id),'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
	{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha', null, array('class' => 'form-control input-xs','min'=>'', 'max'=>'', 'id' => 'fecha', 'placeholder' => '')) !!}
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

		$('#selectnom').change(function(){
            $.get("creditos/"+$(this).val()+"",function(response, facultad){
                var persona = response[0];
                var numCreditos = response[1];
                var numAcciones = response[2];

                if(persona.length>0){
                    $("#persona_id").val(persona[0].id);
                    var msj = "<div class='alert alert-success'><strong>¡Detalles: !</strong><ul><li>Nombre: "+persona[0].nombres+" "+persona[0].apellidos+"</li><li>Tipo: "+(persona[0].tipo.trim() == 'C'? "Cliente": "Socio")+"</li><li>Creditos activos: "+numCreditos+"</li><li>Acciones: "+numAcciones+"</li></ul> </div>";
                        $('#divInfo{{ $entidad }}').html(msj);
                        $('#divInfo{{ $entidad }}').show();
                }else{
                    $("#persona_id").val(0);
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