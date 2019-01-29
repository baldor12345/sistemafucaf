@if( $day >= $periodo_fin )
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($directivos, $formData) !!}	
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	
	<div class="form-row">
		<div>
			{!! Form::label('presidente_id', 'Presidente:', array('class' => 'col-sm-3 col-xs-12 control-label ')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::select('presidente_id', $cboPresidente, null, array('class' => 'form-control input-sm', 'id' => 'presidente_id')) !!}
			</div>
		</div>
		<div>
			{!! Form::label('secretario_id', 'Secretario:', array('class' => 'col-sm-3 col-xs-12 control-label ')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::select('secretario_id', $cboSecretario, null, array('class' => 'form-control input-sm', 'id' => 'secretario_id')) !!}
			</div>
		</div>
		<div>
			{!! Form::label('tesorero_id', 'Tesorero:', array('class' => 'col-sm-3 col-xs-12 control-label ')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::select('tesorero_id', $cboTesorero, null, array('class' => 'form-control input-sm', 'id' => 'tesorero_id')) !!}
			</div>
		</div>
		<div>
			{!! Form::label('vocal_id', 'Vocal:', array('class' => 'col-sm-3 col-xs-12 control-label ')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::select('vocal_id', $cboVocal, null, array('class' => 'form-control input-sm', 'id' => 'vocal_id')) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('estado', 'Estado:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
			<div class="col-sm-9 col-xs-12">
				{!! Form::select('estado', $cboEstado, null, array('class' => 'form-control input-xs', 'id' => 'estado')) !!}
			</div>
		</div>

		<div class="row">
			<div class="col-sm-6 col-xs-12">
				{!! Form::label('periodoi', 'Periodo Inicio:', array('class' => '')) !!}
				{!! Form::date('periodoi', null, array('class' => 'form-control input-xs', 'id' => 'periodoi')) !!}
			</div>
			<div class="col-sm-6 col-xs-12">
				{!! Form::label('periodof', 'Periodo Fin:', array('class' => '')) !!}
				{!! Form::date('periodof', null, array('class' => 'form-control input-xs', 'id' => 'periodof')) !!}
			</div>
		</div>
		<div class="form-group col-12" >
			{!! Form::label('descripcion', 'Descripción: ', array('class' => 'descrip')) !!}
			{!! Form::textarea('descripcion', null, array('class' => 'form-control input-sm','rows' => 4, 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción')) !!}
		</div>
	</div>
	<div class="form-group">
		<div class="col-lg-12 col-md-12 col-sm-12 text-right">
			{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
			{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
		</div>
	</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		
		var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
		var fecha = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
		var fechab = (fechaActual.getFullYear()) +"-12-30";

		$('#periodoi').val(fecha);
		$('#periodof').val(fechab);
		configurarAnchoModal('450');
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');

		$('#presidente_id').select2({
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


        $('#presidente_id').change(function(){
            $.get("directivos/"+$(this).val()+"",function(response, facultad){
                var persona = response[0];
                var numCreditos = response[1];
                var numAcciones = response[2];

                if(persona.length>0){
                    
                }else{
                    $("#presidente_id").val(0);
                }
            });
        });

		//secretario
		$('#secretario_id').select2({
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


        $('#secretario_id').change(function(){
            $.get("directivos/"+$(this).val()+"",function(response, facultad){
                var persona = response[0];
                var numCreditos = response[1];
                var numAcciones = response[2];

                if(persona.length>0){
                    
                }else{
                    $("#secretario_id").val(0);
                }
            });
        });

		//tesorero_id
		$('#tesorero_id').select2({
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


        $('#tesorero_id').change(function(){
            $.get("directivos/"+$(this).val()+"",function(response, facultad){
                var persona = response[0];
                var numCreditos = response[1];
                var numAcciones = response[2];

                if(persona.length>0){
                    
                }else{
                    $("#tesorero_id").val(0);
                }
            });
        });
		//vocal_id
		$('#vocal_id').select2({
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


        $('#vocal_id').change(function(){
            $.get("directivos/"+$(this).val()+"",function(response, facultad){
                var persona = response[0];
                var numCreditos = response[1];
                var numAcciones = response[2];

                if(persona.length>0){
                    
                }else{
                    $("#vocal_id").val(0);
                }
            });
        });
		
	}); 
</script>
@else
<h3 class="text-warning">Ya existe una relacion de directivos para este periodo, Gracias!.</h3>
@endif