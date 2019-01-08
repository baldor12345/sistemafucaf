<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
    {!! Form::open(['route' => $ruta["generarreportes"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formCaja']) !!}
    {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

    <div class="form-group">
        {!! Form::label('tipo', 'Tipo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-sx', 'id' => 'tipo')) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('mes', 'Mes:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            <input type="month" name="mes" id="mes"  size="26">
        </div>
    </div>
    {!! Form::hidden('caja_id', null, array('id' => 'caja_id')) !!}
    
    {!! Form::close() !!}
    
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
    {!! Form::button('<i class="glyphicon glyphicon-remove"></i> Generar ', array('class' => 'btn btn-success btn-sm', 'id' => 'btnRetirar', 'onclick' => 'guardarreapertura(\''.$entidad.'\', this)')) !!}
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

    function guardarreapertura(){
        $.ajax({
            url: 'caja/generarreportes',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: $('#formCaja').serialize(),
            beforeSend: function(){
                
            },
            success: function(res){
                
                //mostrarMensaje ("Reapertura Exitosa!", "OK");
                //buscar("{{$entidad}}");
                cerrarModal();
        
            }
        }).fail(function(){
            mostrarMensaje ("Error de servidor", "ERROR")
        });
    }
    

</script>