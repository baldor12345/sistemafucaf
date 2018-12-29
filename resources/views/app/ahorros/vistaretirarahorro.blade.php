<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
    <div class="row m-b-30" id="selectfilas">
        <div class="col-sm-12">
            {!! Form::open(['route' => $ruta["retiro"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formRetiro']) !!}
            {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
            
            <div class="form-group col-12 col-md-12 col-sm-12" >
                {!! Form::label('cliente', 'Cliente: '.$persona->nombres.' '.$persona->apellidos)!!}
            </div>
             <div class="form-group col-12 col-md-12 col-sm-12" >
                {!! Form::label('maxretiro', 'Máximo a retirar: '.$ahorro->capital)!!}
                {!! Form::hidden('ahorro_id', $ahorro->id, array('id' => 'ahorro_id')) !!}
                {!! Form::hidden('persona_id', $persona->id, array('id' => 'persona_id')) !!}
            </div>
            <div class="form-group col-12 col-md-12 col-sm-12">
                {!! Form::label('montoretiro1', 'Monto a retirar S/.: *', array('class' => '')) !!}
                &nbsp;
                {!! Form::text('montoretiro', null, array('class' => 'form-control input-md input-number', 'id' => 'montoretiro', 'placeholder' => 'Monto a retirar S/.')) !!}
            </div>
            <div class="form-group col-12 col-md-12 col-sm-12">
                    {!! Form::label('fechar', 'Fecha de retiro: *', array('class' => '')) !!}
                    {!! Form::date('fechar', null, array('class' => 'form-control input-xs', 'id' => 'fechar')) !!}
                </div>
            <div class="form-group col-12 col-md-12 col-sm-12">
            {!! Form::label('comision', 'Comision por voucher S/.: 0.10')!!}
            </div>
           
            <div class="form-group col-12 col-md-12 col-sm-12">
            {!! Form::label('totalretirar', 'Total a retirar S/.: ',array('id' => 'totalretirar'))!!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    
    <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
        {!! Form::button('<i class="glyphicon glyphicon-remove"></i> Retirar ', array('class' => 'btn btn-success btn-sm', 'id' => 'btnRetirar', 'onclick' => 'retirar(\''.$persona->id.'\', \''.URL::route($ruta["generareciboretiroPDF"], array()).'\')')) !!}
        &nbsp;
        {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('450');
        $("input[name=montoretiro]").keyup(function(event){
            var monto = $('#montoretiro').val();
            console.log("monto: "+monto);
            if(monto != ''){
                console.log("monto paso: "+monto);
			    $('#totalretirar').html('Total a retirar S/.: '+(monto - 0.10));
            }
    	});
    });

    function retirar(id,rutarecibo){
        
        if($('#montoretiro').val() != ''){
            if(isNaN($('#montoretiro').val()) == false){
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
                        window.open(rutarecibo, "Voucher retiro ahorro", "width=400, height=500, left=200, top=100");
				
                    }
                    }).fail(function(){
                        mostrarMensaje ("Error de servidor", "ERROR")
                    });
                }else{
                    var mensaje = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>El monto que desea retirar supera el valor máximo.!</strong></div>';
                    $('#divMensajeErrorRetiro').html(mensaje);
                }
            }else{
                var mensaje = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Valor de monto no válido.!</strong></div>';
                $('#divMensajeErrorRetiro').html(mensaje);
            }
        }else{
            var mensaje = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Campo monto vacio.!</strong></div>';
            $('#divMensajeErrorRetiro').html(mensaje);
        }
    }

</script>