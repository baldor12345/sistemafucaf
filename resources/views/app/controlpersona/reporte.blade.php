<div id="divMensajeErrorRetiro"></div>
<div class="card-box table-responsive crbox">
    {!! Form::open(['route' => null , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-horizontal', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formMantenimientoControlPersona']) !!}
    {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}

    <div class="form-group">
        {!! Form::label('tipo2', 'Tipo:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            {!! Form::select('tipo2', $cboTipo, null, array('class' => 'form-control input-sx', 'id' => 'tipo2')) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('desde', 'Desde:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            <input type="month" name="desde" id="desde" step="1" min="2008-12" max="2050-12" value='{{ $date_first }}' >
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('hasta', 'Hasta:', array('class' => 'col-sm-3 col-xs-12 control-label')) !!}
        <div class="col-sm-9 col-xs-12">
            <input type="month" name="hasta" id="hasta" step="1" min="2008-12" max="2050-12" value='{{ $date_last }}' >
        </div>
    </div>
    {!! Form::close() !!}
    
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn" id='oculto' style="display:none;">
    {!! Form::button('<i class="fa fa-check fa-lg"></i> Generar Reporte', array('class' => 'btn btn-success btn-xs', 'id' => 'btnGuardar', 'onclick' => 'reporteasistencia(\''.$entidad.'\', \''.URL::route($ruta["generarreporteasistenciaPDF"], array()).'\')')) !!}					
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i>Cancelar y Cerrar', array('class' => 'btn btn-danger btn-xs','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>
<div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn" id='oculto1' style="display:none;">
    {!! Form::button('<i class="fa fa-check fa-lg"></i> Generar Reporte', array('class' => 'btn btn-success btn-xs', 'id' => 'btnGuardar', 'onclick' => 'reporteasistenciajustificada(\''.$entidad.'\', \''.URL::route($ruta["generarreportejustificadaPDF"], array()).'\')')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar y Cerrar', array('class' => 'btn btn-danger btn-xs','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
</div>



<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('400');

        $('#tipo2').change(function(){
            var valorCambiado =$(this).val();
            if(valorCambiado == 'I'){
                document.getElementById('oculto').style.display = 'block';
                document.getElementById('oculto1').style.display = 'none';
            }
            if(valorCambiado == 'E'){
                document.getElementById('oculto').style.display = 'none';
                document.getElementById('oculto1').style.display = 'block';
            }
        });
                
    });

    function reporteasistencia(entidad, rutarecibo) {

        var desde = $('#desde').val();
        var hasta = $('#hasta').val();
        var tipo = $('#tipo').val();
        modalrecibopdf(rutarecibo+"/"+desde+"/"+hasta, '100', 'recibo credito');
    }

    function reporteasistenciajustificada(entidad, rutarecibo) {

        var desde = $('#desde').val();
        var hasta = $('#hasta').val();
        var tipo = $('#tipo').val();
        modalrecibopdf(rutarecibo+"/"+desde+"/"+hasta, '100', 'recibo credito');
    }

    

    

</script>