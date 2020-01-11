<div class="card-box">
    {!! Form::open(['route' => $ruta["generarreport"], 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formMantenimientoAccion5']) !!}
        {!! Form::hidden('persona_id', $persona_id, array('id' => 'persona_id')) !!}
        {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
        <div class="form-group">
            {!! Form::label('anio', 'Año:', array('class' => 'input-md')) !!}
            {!! Form::select('anio', $anios, $anioactual, array('class' => 'form-control input-md', 'id' => 'anio')) !!}
        </div>
        <br/>
        <div class="form-group">
            {!! Form::label('monthi', 'Desde:', array('class' => 'input-md')) !!}
            {!! Form::select('monthi', $meses, '01', array('class' => 'form-control input-md', 'id' => 'monthi')) !!}
        </div>
        <div class="form-group">
            {!! Form::label('monthf', 'Hasta:', array('class' => 'input-md')) !!}
            {!! Form::select('monthf', $meses, $mesactual, array('class' => 'form-control input-md', 'id' => 'monthf')) !!}
        </div>
        
    {!! Form::close() !!}
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right">
    {!! Form::button('<i class="fa fa-check fa-lg"></i> Generar Reporte', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'reporteporperiodo(\''.$entidad.'\', \''.URL::route($ruta["reporteporperiodoPDF"], array()).'\')')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i>Cancelar', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>

<script type="text/javascript">
$(document).ready(function() {
    init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
	configurarAnchoModal('400');
}); 

function reporteporperiodo(entidad, rutarecibo) {
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
                    modalrecibopdf(rutarecibo+"/"+respuesta[1]+"/"+respuesta[2]+"/"+respuesta[3]+"/"+respuesta[4], '100', 'recibo credito');    
                } else {
                    mostrarErrores(respuesta, idformulario, entidad);
                }
            }
        });
    }


</script>
