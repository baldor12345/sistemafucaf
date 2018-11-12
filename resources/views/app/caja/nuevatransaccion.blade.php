<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>

<style> 
textarea {
    width: 100%;
    height: 50px;
    padding: 12px 20px;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    background-color: #f8f8f8;
    font-size: 16px;
    resize: none;
}
</style>

<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($caja, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-group">
<p id="info" class="" ></p>
</div>

<div class="form-group">
	{!! Form::label('dni', 'Dni:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('dni', null, array('class' => 'form-control input-xs', 'id' => 'dni', 'placeholder' => 'asegurese de que el dni ya este registrado...' )) !!}
		<p id="nombresCompletos" class="" ></p>
		<input type="hidden" id="persona_id", name="persona_id" value="">
	</div>
</div>

<div class="form-group">
	{!! Form::label('concepto_id', 'Precio de accion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::select('concepto_id', $cboConfiguraciones, null, array('class' => 'form-control input-xs', 'id' => 'concepto_id')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => '')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('monto', 'Monto(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('monto', null, array('class' => 'form-control input-xs', 'id' => 'monto', 'placeholder' => 'S/.')) !!}
	</div>
</div>


<div class="form-group">
	{!! Form::label('descripcion', 'Descripcion:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('descripcion', null, array('class' => 'form-control input-xs', 'id' => 'descripcion', 'placeholder' => 'Ingrese descripcion')) !!}
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

		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fecha_horaApert = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		if(fechaActual.getMinutes()===1 || fechaActual.getMinutes()===2 || fechaActual.getMinutes()===3 ||
			fechaActual.getMinutes()===4 || fechaActual.getMinutes()===5 || fechaActual.getMinutes()===6 ||
			fechaActual.getMinutes()===7 || fechaActual.getMinutes()===8 || fechaActual.getMinutes()===9 ||
			fechaActual.getHours()===1 || fechaActual.getHours()===2 || fechaActual.getHours()===3 ||
			fechaActual.getHours()===4 || fechaActual.getHours()===5 || fechaActual.getHours()===6 ||
			fechaActual.getHours()===7 || fechaActual.getHours()===8 || fechaActual.getHours()===9){

				var horaAp ="0"+fechaActual.getHours()+":0"+fechaActual.getMinutes();
		}else{
				var horaAp =fechaActual.getHours()+":"+fechaActual.getMinutes();
		}
		console.log(horaAp);


		if($('#fechaTemp').val() !== ""){
			// DD/MM/YYYY
			var valoresFecha = $('#fechaTemp').val().split('/');
			//yyy/MM/DD
			var fecha = valoresFecha[2] + "-" + valoresFecha[1] + "-" + valoresFecha[0];
			$('#fecha_horaApert').val(fecha);
			$('#hora_apertura').val($('#horaAp').val());
		}else{
			$('#fecha_horaApert').val(fecha_horaApert);
			$('#hora_apertura').val(horaAp);
		}

		
		/*
		var personas = new Bloodhound({
			datumTokenizer: function (d) {
				return Bloodhound.tokenizers.whitespace(d.value);
			},
			queryTokenizer: Bloodhound.tokenizers.whitespace,
			remote: {
				url: 'person/employeesautocompleting/%QUERY',
				filter: function (personas) {
					return $.map(personas, function (movie) {
						return {
							value: movie.value,
							id: movie.id
						};
					});
				}
			}
		});
		personas.initialize();
		$('#nombrepersona').typeahead(null,{
			displayKey: 'value',
			source: personas.ttAdapter()
		}).on('typeahead:selected', function (object, datum) {
			$('#person_id').val(datum.id);
		});
		, campos de texto=>'disabled' => 'disabled'			
		*/
	}); 
</script>