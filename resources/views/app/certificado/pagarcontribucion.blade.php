<div id="divMensajeErrorCertificado"></div>
<div class="card-box table-responsive crbox">
    {!! Form::open(['route' => $ruta["guardarpagarcontribucion"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formMantenimientoCertificado']) !!}
    {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

    <div class="form-group">
        {!! Form::label('concepto_id', 'Concepto:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::select('concepto_id', $cboCertificado, null, array('class' => 'form-control input-sx', 'id' => 'concepto_id')) !!}
        </div>
    </div>


    <div class="form-group">
        {!! Form::label('monto', 'Monto:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::text('monto', (0.5), array('class' => 'form-control input-xs', 'id' => 'monto', 'placeholder' => 'Ingrese titulo')) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('fecha_pago', 'Fecha:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::date('fecha_pago', null, array('class' => 'form-control input-xs', 'id' => 'fecha_pago', 'placeholder' => 'Ingrese titulo')) !!}
        </div>
    </div>
    {!! Form::hidden('caja_id', $caja_id, array('id' => 'caja_id')) !!}
    {!! Form::hidden('certificado_id', $id, array('id' => 'certificado_id')) !!}
    {!! Form::close() !!}
    
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
    {!! Form::button('<i class="glyphicon glyphicon-remove"></i> Pagar y Generar Reporte ', array('class' => 'btn btn-success btn-sm', 'id' => 'btnRetirar', 'onclick' => 'guardarpagarmulta(\''.$entidad.'\', \''.URL::route($ruta["reportecertificadoPDF"], array()).'\')')) !!}
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

    function guardarpagarmulta(entidad, rutarecibo) {
        console.log("hola");
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
                    cerrarModal();
                    modalrecibopdf(rutarecibo+"/"+respuesta[1], '100', 'recibo accion');
                    buscar('{{ $entidad }}');      
                } else {
                    mostrarErrores(respuesta, idformulario, entidad);
                }
            }
        });
        
	}
    
</script>