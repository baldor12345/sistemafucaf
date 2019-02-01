@if($caja_id !=  0)
<div id="divinforetiro"></div>
<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
        <div class="col-sm-12">
            {!! Form::open(['route' => $ruta["retiro"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formRetiro']) !!}
            {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
            
            <div class="form-group col-12 col-md-12 col-sm-12" >
                {!! Form::label('cliente', 'Cliente: '.$persona->nombres.' '.$persona->apellidos)!!}
            </div>
             <div class="form-group col-12 col-md-12 col-sm-12" >
                {!! Form::label('maxretiro', 'Máximo a retirar: '.round($ahorro->capital,1))!!}
                {!! Form::hidden('ahorro_id', $ahorro->id, array('id' => 'ahorro_id')) !!}
                {!! Form::hidden('persona_id', $persona->id, array('id' => 'persona_id')) !!}
            </div>
            <div class = "form-group">
                <div class="form-group col-12 col-md-12 col-sm-12">
                    {!! Form::label('montoretiro', 'Monto a retirar S/.: *', array('class' => '')) !!}
                </div>
                 <div class="form-group col-12 col-md-12 col-sm-12">
                    {!! Form::text('montoretiro', null, array('class' => 'form-control input-md', 'id' => 'montoretiro', 'placeholder' => 'Monto a retirar S/.','onkeypress'=>'return filterFloat(event,this);')) !!}
                </div>
            </div>
            <br>
            <br>
            <div class = "form-group">
                <div class="form-group col-12 col-md-12 col-sm-12">
                {!! Form::label('fechar', 'Fecha de retiro: *', array('class' => '')) !!}
                </div>
                <div class="form-group col-12 col-md-12 col-sm-12">
                {!! Form::date('fechar', $fecha_pordefecto, array('class' => 'form-control input-md', 'id' => 'fechar')) !!}
                </div>
            </div>
            <br>
            <br>
            {!! Form::close() !!}
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
        $('#divinforetiro').html('<div class="alert bg-warning" role="alert"><strong>SALDO EN CAJA (S/.): </strong>{{ $saldo_en_caja }}</div>');
    });

    function retirar(id,rutarecibo){
        
        if($('#montoretiro').val() != ''){
            if(isNaN($('#montoretiro').val()) == false && $('#montoretiro').val() > 0){
                if($('#montoretiro').val() <= {{ round($ahorro->capital,1) }}){
                    if($('#montoretiro').val() <= {{ round($saldo_en_caja,1) }}){
                        $.ajax({
                        url: 'ahorros/retiro',
                        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        type: 'GET',
                        data: $('#formRetiro').serialize(),
                        beforeSend: function(){
                            
                        },
                        success: function(res){
                            if(res == 'OK'){
                                mostrarMensaje ("Retiro exitoso", "OK");
                                buscar("{{$entidad}}");
                                cerrarModal();
                                imprimirpdf(rutarecibo);
                            // window.open(rutarecibo, "Voucher retiro ahorro", "width=400, height=500, left=200, top=100");
                            }else{
                                var mensaje = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>&times;</button><strong>"+res+"</strong></div>";
                            $('#divMensajeErrorRetiro').html(mensaje);
                            }
                        }
                        }).fail(function(){
                            mostrarMensaje ("Error de servidor", "ERROR")
                        });
                    }else{
                        var mensaje = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>El monto que desea retirar supera el valor del saldo actual en Caja.!</strong></div>';
                        $('#divMensajeErrorRetiro').html(mensaje);
                    }
                    
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
@else
<div class="alert alert-danger">¡Caja no aperturada, aperture primero.! </div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
   
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>
@endif