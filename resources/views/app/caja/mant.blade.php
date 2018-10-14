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
	{!! Form::label('titulo', 'Titulo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('titulo', null, array('class' => 'form-control input-xs', 'id' => 'titulo', 'placeholder' => 'Ingrese titulo')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('fecha', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha', 'placeholder' => '')) !!}
	</div>
</div>
<?php
	if($caja != null){
		echo "<input type='hidden' id='fechaTemp' value='".Date::parse($caja->fecha )->format('d/m/Y')."'>";
		echo "<input type='hidden' id='horaAp' value='".$caja->hora_apertura."'>";
	}else{
		echo "<input type='hidden' id='fechaTemp' value=''>";
	}
?>

<div class="form-group">
	{!! Form::label('hora_apertura', 'Hora Apertura:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::time('hora_apertura', null, array('class' => 'form-control input-xs', 'id' => 'hora_apertura', 'placeholder' => '')) !!}
	</div>
</div>

<div class="form-group">
	{!! Form::label('monto_iniciado', 'Monto Inicio(S/.):', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
	<div class="col-sm-9 col-xs-12">
		{!! Form::text('monto_iniciado', null, array('class' => 'form-control input-xs', 'id' => 'monto_iniciado', 'placeholder' => 'S/.')) !!}
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
		var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		if(fechaActual.getMinutes()===1 || fechaActual.getMinutes()===2 || fechaActual.getMinutes()===3 ||
			fechaActual.getMinutes()===4 || fechaActual.getMinutes()===5 || fechaActual.getMinutes()===6 ||
			fechaActual.getMinutes()===7 || fechaActual.getMinutes()===8 || fechaActual.getMinutes()===9){
				var horaAp =fechaActual.getHours()+":0"+fechaActual.getMinutes()+":"+fechaActual.getSeconds();;
		}else{
				var horaAp =fechaActual.getHours()+":"+fechaActual.getMinutes()+":"+fechaActual.getSeconds();;
		}
		console.log(horaAp);


		if($('#fechaTemp').val() !== ""){
			// DD/MM/YYYY
			var valoresFecha = $('#fechaTemp').val().split('/');
			//yyy/MM/DD
			var fecha = valoresFecha[2] + "-" + valoresFecha[1] + "-" + valoresFecha[0];
			$('#fecha').val(fecha);
			$('#hora_apertura').val($('#horaAp').val());
		}else{
			$('#fecha').val(fechai);
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