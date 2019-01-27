<div id="divInfo{!! $entidad !!}"></div>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($ahorros, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="row">
	<div class="card-box table-responsive crbox">
		<div class="form-group">
			<div class="form-group col-6 col-md-6 col-sm-12">
				{!! Form::label('selectpersona', 'Socio o Cliente: ', array('class' => 'cliente')) !!}
				{!! Form::select('selectpersona', $cboPers, null, array('class' => 'form-control input-sm', 'id' => 'selectpersona')) !!}
				<input type="hidden" id="persona_id" name="persona_id" value="" tipocl=''>
			</div>
			
			<div class="form-group col-6 col-md-6 col-sm-6" style="margin-left: 15px">
				{!! Form::label('capital', 'Importe S/.: *', array('class' => '')) !!}
				{!! Form::text('capital', null, array('class' => 'form-control input-md', 'id' => 'capital', 'placeholder' => 'Ingrese el monto de ahorro', 'onkeypress'=>'return filterFloat(event,this);')) !!}
			</div>
		</div>
		<div class = "form-group">
			<div class="form-group col-6 col-md-6 col-sm-6">
				{!! Form::label('interes', 'Interes mensual (%): *', array('class' => '')) !!}
				{!! Form::text('interes',($configuraciones->tasa_interes_ahorro*100), array('class' => 'form-control input-xs', 'id' => 'interes', 'placeholder' => 'Interes mensual', 'onkeypress'=>'return filterFloat(event,this);', 'readonly')) !!}
			</div>
			<div class="form-group col-6 col-md-6 col-sm-6" style="margin-left: 15px" >
				{!! Form::label('fechai', 'Fecha de deposito: *', array('class' => '')) !!}
				{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai')) !!}
			</div>
		</div>
		<div class="form-group col-12 col-md-12 col-sm-12" >
			{!! Form::label('concepto', 'Concepto:', array('class' => '')) !!}
			{!! Form::select('concepto', $cboConcepto, $idopcion, array('class' => 'form-control input-xs', 'id' => 'concepto')) !!}
		</div>
		<div class="form-group">
			<div class="col-lg-12 col-md-12 col-sm-12 text-right">
				{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarahorro(\''.$entidad.'\', \''.URL::route($ruta["generareciboahorroPDF"], array()).'\')')) !!}
				&nbsp;
				{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
			</div>
		</div>
	</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
$(document).ready(function() {
	var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day;
		$('#fechai').val(fechai);
	configurarAnchoModal('650');
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
}); 
function guardarahorro(entidad,rutarecibo) {
	var idformulario = IDFORMMANTENIMIENTO + entidad;
	var data         = submitForm(idformulario);
	var respuesta    = '';
	var listar       = 'NO';
	if ($(idformulario + ' :input[id = "listar"]').length) {
		var listar = $(idformulario + ' :input[id = "listar"]').val()
	};
	data.done(function(msg) {
		respuesta = msg;
	}).fail(function(xhr, textStatus, errorThrown) {
		respuesta = 'ERROR';
	}).always(function() {
		
		if(respuesta === 'ERROR'){
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
			}
		}
	});
}

</script>