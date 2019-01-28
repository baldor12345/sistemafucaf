
<div id="divInfo" class="alert alert-success">Acreditado: {{ $persona->nombres." ".$persona->apellidos }}
	<ul>
		<li>Monto Cuota: {{ $cuota->parte_capital + $cuota->interes}}</li>
		<li>Saldo Restante: {{ $cuota->saldo_restante}}</li>
		<li>Numero cuota: {{ $cuota->numero_cuota."/".$credito->periodo}}</li>
	</ul>
</div>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($cuota, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('id_cuota', $cuota->id, array('id' => 'id_cuota')) !!}
{!! Form::hidden('monto_cuota', 0, array('id' => 'monto_cuota')) !!}

<div class="form-row">
    <div class="form-group col-md-12 col-sm-12">
		{!! Form::label('porcentaje_mora', 'Porcentaje de Mora (%):', array('class' => '')) !!}
		{!! Form::text('porcentaje_mora', 3, array('class' => 'form-control input-xs input-number', 'id' => 'porcentaje_mora', 'placeholder' => 'Ingrese porcentaje mora %','onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::label('monto_moratorio', 'Monto de mora aplicado S/.: ', array('class' => '', 'id'=>'monto_moratorio')) !!}
	</div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Aplicar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
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
		var saldorestante = "{{ $cuota->saldo_restante }}";
		var montomora = $('#porcentaje_mora').val()/100 * saldorestante;
		$('#monto_cuota').val(RoundDecimal(montomora, 4));
		$('#monto_moratorio').html('Monto de mora aplicado S/.: '+RoundDecimal(montomora, 1));
		
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
	function RoundDecimal(numero, decimales) {
		numeroRegexp = new RegExp('\\d\\.(\\d){' + decimales + ',}');   // Expresion regular para numeros con un cierto numero de decimales o mas
		if (numeroRegexp.test(numero)) {         // Ya que el numero tiene el numero de decimales requeridos o mas, se realiza el redondeo
			return Number(numero.toFixed(decimales));
		} else {
			return Number(numero.toFixed(decimales)) === 0 ? 0 : numero;  // En valores muy bajos, se comprueba si el numero es 0 (con el redondeo deseado), si no lo es se devuelve el numero otra vez.
		}
	}
	
</script>