<div id="infoaccion"></div>
<div class="card-box table-responsive crbox">
    {!! Form::open(['route' => $ruta["guardarjustificar"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formControlPersona']) !!}
    {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

    <div class="form-group">
        <label for="descripcion">Ingrese Justificacion:</label>
        <textarea class="form-control" name="descripcion" rows="5" id="descripcion" placeholder="ingresar justificacion..."></textarea>
    </div>
    {!! Form::hidden('control_id', $id, array('id' => 'control_id')) !!}
    {!! Form::close() !!}
    
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
    {!! Form::button('<i class="glyphicon glyphicon-pencil"></i> Registrar justificacion', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarjust', 'onclick' => 'guardarjustificacion(\''.$entidad.'\', this)')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('400');
        
    });

    function guardarjustificacion(id){
        var descrip = $('#descripcion').val();
        
        if(descrip == ''){
            document.getElementById("infoaccion").innerHTML = "<div class='alert alert-danger' role='danger'><span >Llenar campo justificacion, gracias!</span></div>";
			$('#infoaccion').show();
        }else{
            $.ajax({
                url: 'controlpersona/guardarjustificar',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                data: $('#formControlPersona').serialize(),
                beforeSend: function(){
                    $('#btnGuardarjust').button('loading');
                },
                success: function(res){
                    
                    mostrarMensaje ("Justificacion Realizada!", "OK");
                    buscar('{{$entidad}}');
                    cerrarModal();
            
                }
            }).fail(function(){
                mostrarMensaje ("Error de servidor", "ERROR");
                $('#btnGuardarjust').removeClass('disabled');
                $('#btnGuardarjust').removeAttr('disabled');
                $('#btnGuardarjust').html('<i class="fa fa-check fa-lg"></i>Guardar');
            });
        }
    }
    function cerrarModal(){
        buscar('{{$entidad}}');
    }
    
</script>