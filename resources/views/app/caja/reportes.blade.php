<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
    {!! Form::open(['route' => $ruta["generarreportes"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formCaja']) !!}
    {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}


    <div class="form-group">
        {!! Form::label('monto_inicio', 'Mes:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
        <div class="col-sm-8 col-xs-12">
            <input type="month">
        </div>
    </div>
    {!! Form::hidden('caja_id', null, array('id' => 'caja_id')) !!}

    <div class="form-group">
        {!! Form::label('monto_cierre', 'Monto Cierre:', array('class' => 'col-sm-4 col-xs-12 control-label')) !!}
        <div class="col-sm-8 col-xs-12">
            {!! Form::date('monto_cierre', null, array('class' => 'form-control input-xs', 'id' => 'monto_cierre', 'placeholder' => 'Ingrese titulo')) !!}
        </div>
    </div>
    
    {!! Form::close() !!}
    
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
    {!! Form::button('<i class="glyphicon glyphicon-remove"></i> Reaperturar ', array('class' => 'btn btn-success btn-sm', 'id' => 'btnRetirar', 'onclick' => 'guardarreapertura(\''.$entidad.'\', this)')) !!}
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

    });

    function guardarreapertura(id){
        $.ajax({
            url: 'caja/generarreportes',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: $('#formCaja').serialize(),
            beforeSend: function(){
                
            },
            success: function(res){
                
                mostrarMensaje ("Reapertura Exitosa!", "OK");
                //buscar("{{$entidad}}");
                cerrarModal();
        
            }
        }).fail(function(){
            mostrarMensaje ("Error de servidor", "ERROR")
        });
    }
    

</script>