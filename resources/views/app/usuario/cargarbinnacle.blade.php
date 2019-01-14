<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
    {!! Form::open(['route' => $ruta["generarreporte"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formMantenimientoUsuario']) !!}
    {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

    <div class="form-group ">
        {!! Form::label('desde', 'Desde:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::date('desde', null, array('class' => 'form-control input-xs', 'id' => 'desde', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
        </div>
    </div>

    <div class="form-group ">
        {!! Form::label('hasta', 'Hasta:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::date('hasta', null, array('class' => 'form-control input-xs', 'id' => 'hasta', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
        </div>
    </div>

    
    {!! Form::close() !!}
    
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
        {!! Form::button('<i class="fa fa-check fa-lg"></i> Generar Bitacora', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'reportebitacora(\''.$entidad.'\', \''.URL::route($ruta["binnaclePDF"], array()).'\')')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>

<script type="text/javascript">
	$(document).ready(function() {
        configurarAnchoModal('400');
        var fechaActual = new Date();
		var day = ("0" + fechaActual.getDate()).slice(-2);
		var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
        var fechai = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
        $('#desde').val(fechai);
        $('#hasta').val(fechai);
                
    });

    function reportebitacora(entidad, rutarecibo) {
        var idformulario = IDFORMMANTENIMIENTO + entidad;
        var data         = submitForm(idformulario);
        var respuesta    = null;
        var listar       = 'NO';
        if ($(idformulario + ' :input[id = "listar"]').length) {
            var listar = $(idformulario + ' :input[id = "listar"]').val()
        };
        data.done(function(msg) {
            respuesta = msg;
        }).fail(function(xhr, textStatus, errorThrown) {
            respuesta = 'ERROR';
        }).always(function() {
            
            if(respuesta[0] === 'ERROR'){
            }else{
                
                if (respuesta[0] === 'OK') {
                    modalrecibopdf(rutarecibo+"/"+respuesta[1]+"/"+respuesta[2], '100', 'recibo credito');    
                } else {
                    mostrarErrores(respuesta, idformulario, entidad);
                }
            }
        });
    }

</script>