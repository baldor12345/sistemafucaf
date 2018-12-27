
<div class="card-box table-responsive crbox">
    <div class="row m-b-30" id="selectfilas">
        <div class="col-sm-12">
            {!! Form::open(['route' => $ruta["retiro"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formRetiro']) !!}
            {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
            
            <div class="form-group" >
                {!! Form::label('Monto', 'Monto Deposito: '.$ahorro->importe)!!}
                {!! Form::label('interes', 'Interes: '.$interesMostrar)!!}
                {!! Form::label('total', 'Total a retirar: '.$totalretiro)!!}
                {!! Form::hidden('id_ahorro', $ahorro->id, array('id' => 'id_ahorro')) !!}
                {!! Form::hidden('montototal',  $interesMostrar, array('id' => 'montototal')) !!}
            </div>
            
            {!! Form::close() !!}
        </div>
    </div>
    <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
        &nbsp;
        {!! Form::button('<i class="glyphicon glyphicon-remove"></i> Retirar ', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnRetirar', 'onclick' => 'retirar(\''.$ahorro->id.'\')')) !!}
        {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('850');
        buscar("{{ $entidad }}");
    });
    function retirar(id){
        
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
                buscar("{{$entidad1}}");
                cerrarModal();
            }
        }).fail(function(){
            mostrarMensaje ("Error de servidor", "ERROR")
            cerrarModal();
        });
    }

</script>