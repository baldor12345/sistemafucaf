<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
    <div class="row m-b-30" id="selectfilas">
        <div class="col-sm-12">
            {!! Form::open(['route' => $ruta["retiro"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formRetiro']) !!}
            {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
            
            <div class="form-group col-12 col-md-12 col-sm-12" >
                {!! Form::label('cliente', 'Cliente: '.$persona->nombres.' '.$persona->apellidos)!!}
                {!! Form::label('maxretiro', 'Máximo a retirar: '.$ahorro->capital)!!}
                {!! Form::hidden('ahorro_id', $ahorro->id, array('id' => 'ahorro_id')) !!}
                {!! Form::hidden('persona_id', $persona->id, array('id' => 'persona_id')) !!}
            </div>
            <div class="form-group col-12 col-md-12 col-sm-12">
                {!! Form::label('montoretiro', 'Importe S/.: *', array('class' => '')) !!}
                {!! Form::text('montoretiro', null, array('class' => 'form-control input-xs', 'id' => 'montoretiro', 'placeholder' => 'Monto a retirar S/.')) !!}
            </div>
            
            {!! Form::close() !!}
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
        &nbsp;
        {!! Form::button('<i class="glyphicon glyphicon-remove"></i> Retirar ', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnRetirar', 'onclick' => 'retirar(\''.$persona->id.'\')')) !!}
        {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('650');
     
    });
    function retirar(id){
        if($('#montoretiro').val() <= {{ $ahorro->capital }}){
            $.ajax({
                url: 'ahorros/retiro',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                data: $('#formRetiro').serialize(),
                beforeSend: function(){
                    
                },
                success: function(res){
                    
                    mostrarMensaje ("Retiro exitoso", "OK");
                    buscar("{{$entidad}}");
                    cerrarModal();
                }
            }).fail(function(){
                mostrarMensaje ("Error de servidor", "ERROR")
                cerrarModal();
            });
        }else{
            var mensaje = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>El monto que desea retirar supera el valor máximo.!</strong></div>';
            $('#divMensajeErrorRetiro').html(mensaje);
        }
    }

</script>