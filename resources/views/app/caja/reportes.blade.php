<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
    {!! Form::open(['route' => $ruta["generarreportes"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formMantenimientoCaja']) !!}
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
            <input type="month" name="mes" id="mes" step="1" min="2008-12" max="2050-12" value="2019-01" >
        </div>
    </div>

    
    {!! Form::close() !!}
    
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn" id='oculto' style="display:none;">
        {!! Form::button('<i class="fa fa-check fa-lg"></i> Ingreso', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'reporteingreso(\''.$entidad.'\', \''.URL::route($ruta["reporteingresosPDF"], array()).'\')')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn" id='oculto2' style="display:none;">
        {!! Form::button('<i class="fa fa-check fa-lg"></i> Egreso', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'reporteingreso(\''.$entidad.'\', \''.URL::route($ruta["reporteegresosPDF"], array()).'\')')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('400');

        

        $('#tipo').change(function(){
            var valorCambiado =$(this).val();
            if(valorCambiado == 'I'){
                document.getElementById('oculto').style.display = 'block';
                document.getElementById('oculto2').style.display = 'none';
            }else{
                document.getElementById('oculto').style.display = 'none';
                document.getElementById('oculto2').style.display = 'block';
            }
        });
                
    });

    function reporteingreso(entidad, rutarecibo) {
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
                    modalrecibopdf(rutarecibo+"/"+respuesta[1], '100', 'recibo credito');    
                } else {
                    mostrarErrores(respuesta, idformulario, entidad);
                }
            }
        });
    }

    function reporteegreso(entidad, rutarecibo) {
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
                    modalrecibopdf(rutarecibo+"/"+respuesta[1], '100', 'recibo credito');
                    if (listar === 'SI') {
                        if(typeof entidad2 != 'undefined' && entidad2 !== ''){
                            entidad = entidad2;
                        }
                        buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
                    }        
                } else {
                    mostrarErrores(respuesta, idformulario, entidad);
                }
            }
        });
    }

</script>