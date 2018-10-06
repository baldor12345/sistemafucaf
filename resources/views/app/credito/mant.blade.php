
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($credito, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('valor_credito', 'Valor de Credito:', array('class' => '')) !!}
			{!! Form::text('valor_credito', null, array('class' => 'form-control input-xs', 'id' => 'valor_credito', 'placeholder' => 's/.')) !!}

    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('cantidad_cuotas', 'N° Cuotas:', array('class' => '')) !!}
			{!! Form::text('cantidad_cuotas', null, array('class' => 'form-control input-xs input-number', 'id' => 'cantidad_cuotas', 'placeholder' => 'Ingrese Numero de cuotas', 'maxlength' => '8')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('comision', 'Comision:', array('class' => '')) !!}
			{!! Form::text('comision', null, array('class' => 'form-control input-xs', 'id' => 'comision', 'placeholder' => 'Ingrese el interes mensual')) !!}

    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('fecha', 'Fecha:', array('class' => '')) !!}
			{!! Form::date('fecha', null, array('class' => 'form-control input-xs', 'id' => 'fecha')) !!}
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
		configurarAnchoModal('650');
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
	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});
</script>