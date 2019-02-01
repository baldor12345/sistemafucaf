<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
    {!! Form::open(['route' => $ruta["guardarpagarmulta"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formControlPersona']) !!}
    {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

    <div class="form-group">
        {!! Form::label('concepto_id', 'Concepto:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::select('concepto_id', $cboMulta, null, array('class' => 'form-control input-sx', 'id' => 'concepto_id')) !!}
        </div>
    </div>


    <div class="form-group">
        {!! Form::label('monto', 'Monto:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::text('monto', (5.00), array('class' => 'form-control input-xs', 'id' => 'monto', 'placeholder' => 'Ingrese titulo')) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('fecha_pago', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::date('fecha_pago', null, array('class' => 'form-control input-xs', 'id' => 'fecha_pago', 'placeholder' => 'Ingrese titulo')) !!}
        </div>
    </div>
    {!! Form::hidden('caja_id', $caja_id, array('id' => 'caja_id')) !!}
    {!! Form::hidden('control_id', $id, array('id' => 'control_id')) !!}
    {!! Form::close() !!}
    
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
    {!! Form::button('<i class="glyphicon glyphicon-remove"></i> Pagar Multa ', array('class' => 'btn btn-success btn-sm', 'id' => 'btnRetirar', 'onclick' => 'guardarpagarmulta(\''.$entidad.'\', this)')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>
<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('400');
        var fechaActual = new Date();
        var day = ("0" + fechaActual.getDate()).slice(-2);
        var month = ("0" + (fechaActual.getMonth()+1)).slice(-2);
        var fechaactualr = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
        var fecha_caja = '{{ $fecha_caja }}';
        if(fecha_caja != 0){
            $('#fecha_pago').val(fecha_caja);
        }else{
            $('#fecha_pago').val(fechaactualr);
        }
        
        
    });

    function guardarpagarmulta(id){
        $.ajax({
            url: 'controlpersona/guardarpagarmulta',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: $('#formControlPersona').serialize(),
            beforeSend: function(){
                
            },
            success: function(res){
                
                mostrarMensaje ("Pago Realizado!", "OK");
                buscar("{{$entidad}}");
                cerrarModal();
        
            }
        }).fail(function(){
            mostrarMensaje ("Error de servidor", "ERROR")
        });
    }
    
</script>