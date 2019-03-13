<div id="divInfo{!! $entidad !!}"></div>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($ahorros, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="row">
	<div class="card-box table-responsive crbox">
		<div class="form-group">
			<div class=" col-6 col-md-6 col-sm-12">
				<div class="form-group col-md-12">
					{!! Form::label('selectpersona', 'Socio o Cliente: ', array('class' => 'cliente')) !!}
					{!! Form::select('selectpersona', $cboPers, null, array('class' => 'form-control input-sm', 'id' => 'selectpersona')) !!}
					<input type="hidden" id="persona_id" name="persona_id" value="" tipocl=''>
				</div>
			</div>
			
			<div class="col-6 col-md-6 col-sm-12">
				<div class="form-group col-12 col-md-12 col-sm-12">
					{!! Form::label('capital', 'Importe S/.: *', array('class' => '')) !!}
					{!! Form::text('capital', null, array('class' => 'form-control input-md', 'id' => 'capital', 'placeholder' => 'Ingrese el monto de ahorro', 'onkeypress'=>'return filterFloat(event,this);')) !!}
				</div>
			</div>
		</div>
		<div class = "form-group">
			<div class="col-6 col-md-6 col-sm-12">
				<div class="form-group col-12 col-md-12 col-sm-12">
					{!! Form::label('interes', 'Interes mensual (%): *', array('class' => '')) !!}
					{!! Form::text('interes',($configuraciones->tasa_interes_ahorro*100), array('class' => 'form-control input-xs', 'id' => 'interes', 'placeholder' => 'Interes mensual', 'onkeypress'=>'return filterFloat(event,this);', 'readonly')) !!}
				</div>
			</div>
			<div class="col-6 col-md-6 col-sm-12">
				<div class="form-group col-12 col-md-12 col-sm-12">
					{!! Form::label('fechai', 'Fecha de deposito: *', array('class' => '')) !!}
					{!! Form::date('fechai', $fecha_pordefecto, array('class' => 'form-control input-xs', 'id' => 'fechai')) !!}
				</div>
			</div>
		</div>
<hr>
		<div class = "form-group">
            <div class="col-4 col-md-4 col-sm-12">
                <div class="form-group col-md-12">
                    {!! Form::label('monto_recibido_ah', 'Monto Recibido: ', array('class' => '')) !!}
                    {!! Form::text('monto_recibido_ah', null, array('class' => 'form-control input-sm ', 'id' => 'monto_recibido_ah', 'placeholder' => 's/.','onkeypress'=>'return filterFloat(event,this);')) !!}
                </div>
            </div>
   
            <div class=" col-4 col-md-4 col-sm-12" >
                <div class="form-group col-md-12" >
                    {!! Form::label('monto_ahorro_ah', 'Monto Pago s/.:', array('class' => '')) !!}
                    {!! Form::text('monto_ahorro_ah', 0, array('class' => 'form-control input-sm', 'id' => 'monto_ahorro_ah', 'placeholder' => '', 'readonly')) !!}
                </div>
            </div>

            <div class=" col-4 col-md-4 col-sm-12" >
                <div class="form-group col-md-12" >
                    {!! Form::label('monto_dif_ah', 'Diferencia s/.:', array('class' => '')) !!}
                    {!! Form::text('monto_dif_ah', 0, array('class' => 'form-control input-sm', 'id' => 'monto_dif_ah', 'placeholder' => '', 'readonly')) !!}
                </div>
            </div>
        </div>
		{{-- <div class="form-group col-12 col-md-12 col-sm-12" >
			{!! Form::label('concepto', 'Concepto:', array('class' => '')) !!}
			{!! Form::select('concepto', $cboConcepto, $idopcion, array('class' => 'form-control input-xs', 'id' => 'concepto')) !!}
		</div> --}}
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarAhorro', 'onclick' => 'guardarahorro(\''.$entidad.'\', \''.URL::route($ruta["generareciboahorroPDF"], array()).'\')')) !!}
				&nbsp;
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
	</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	// console.log("FECHA_DEFECTO: {{ $fecha_pordefecto }}");

	configurarAnchoModal('750');
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	$('#selectpersona').select2({
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
	$('#selectpersona').change(function(){

		$.get("creditos/"+$(this).val()+"",function(response, facultad){
			var persona = response[0];
			var numCreditos = response[1];
			var numAcciones = response[2];

			if(persona.length>0){
				$("#persona_id").val(persona[0].id);
				var msj = "<div class='alert alert-success'><strong>¡Detalles: !</strong><ul><li>Nombre: "+persona[0].nombres+" "+persona[0].apellidos+"</li><li>Tipo: "+(persona[0].tipo.trim() == 'C'? "Cliente": "Socio")+"</li><li>Creditos activos: "+numCreditos+"</li><li>Acciones: "+numAcciones+"</li></ul> </div>";
					$('#divInfo{{ $entidad }}').html(msj);
					$('#divInfo{{ $entidad }}').show();
					
				if( persona[0].tipo.trim() == 'S'){
					$("#persona_id").attr('tipocl','S');
				}else{
					$("#persona_id").attr('tipocl','C');
				}
			}else{
				$("#persona_id").val(0);
			}
		});
	});

	$("#capital").on('keyup', function(){
		$('#monto_ahorro_ah').val($(this).val() || 0);
		calcularDif();
	}).keyup();

	$("#monto_recibido_ah").on('keyup', function(){
		calcularDif();

	   }).keyup();
	
}); 
function calcularDif(){
	var monto_ahorro = RoundDecimal(parseFloat($('#monto_ahorro_ah').val()), 1) || 0;
		   var monto_recibido = RoundDecimal(parseFloat($("#monto_recibido_ah").val()), 1) || 0;
		   var monto_dif_ah = RoundDecimal(monto_recibido - monto_ahorro, 1) || 0;
		   $('#monto_dif_ah').val(monto_dif_ah);
}
function guardarahorro(entidad,rutarecibo) {

	$monto_dif = parseFloat($('#monto_dif_ah').val());
 
	if($monto_dif >=0){
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var data         = submitForm(idformulario);
		var respuesta    = '';
		var listar       = 'NO';
		if ($(idformulario + ' :input[id = "listar"]').length) {
			var listar = $(idformulario + ' :input[id = "listar"]').val()
		};
		$('#btnGuardarAhorro').button('loading');
		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			
			if(respuesta === 'ERROR'){
				$('#btnGuardarAhorro').removeClass('disabled');
				$('#btnGuardarAhorro').removeAttr('disabled');
				$('#btnGuardarAhorro').html('<i class="fa fa-check fa-lg"></i> Registrar');
			}else{
				if (respuesta === 'OK') {
					cerrarModal();
					imprimirpdf(rutarecibo);
					
					if (listar === 'SI') {
						if(typeof entidad2 != 'undefined' && entidad2 !== ''){
							entidad = entidad2;
						}
						buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
					}        
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
					$('#btnGuardarAhorro').removeClass('disabled');
				$('#btnGuardarAhorro').removeAttr('disabled');
				$('#btnGuardarAhorro').html('<i class="fa fa-check fa-lg"></i> Registrar');
				
				}
			}
		});
	}else{

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