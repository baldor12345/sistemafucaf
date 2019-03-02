
<div id="divMensajeError1"></div>
<div class="form-row">
    <div class="card-box table-responsive crbox">
        <div class="form-group" >
            {!! Form::label('tipo_reporte', 'Seleccione Reporte:') !!}
            {!! Form::select('tipo_reporte', array('1'=>'Total', '2'=>'Ahorros Activos a la fecha'), '1', array('class' => 'form-control input-xs', 'id' => 'tipo_reporte','onchange' => 'selectTipo(this)')) !!}
        </div>
        <div class="form-group" id="select_fechaRport">
            {!! Form::label('anio_inicio', 'Seleccione Año:') !!}
            {!! Form::select('anio_inicio', $anios, ($anio_actual), array('class' => 'form-control input-xs', 'id' => 'anio_inicio')) !!}
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 text-right">
    {!! Form::button('<i class="fa fa-check fa-lg"></i> Imprimir', array('class' => 'btn btn-success btn-sm', 'id' => 'btnReporteAh', 'onclick' => 'imprimirReporte()')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelarAh', 'onclick' => 'cerrarModal();')) !!}
</div>

<script>
    $(document).ready(function(){
      
		configurarAnchoModal('450');

        $("#modal"+(contadorModal - 1)).on('hidden.bs.modal', function () {
            $('.modal' + (contadorModal-2)).css('pointer-events','auto'); 
        });
    });
    function selectTipo(select){
        if($(select).val() == '1'){
            $('#select_fechaRport').show();
        }else{
            $('#select_fechaRport').hide();
        }
    }

    function imprimirReporte(){
        
        var rutareportecuotas = "{{ URL::route($ruta['reporteahorros'], array()) }}";
        rutareportecuotas += "?anio_inicio="+$('#anio_inicio').val()+"&tipo_reporte="+$('#tipo_reporte').val();
        
        imprimirpdf(rutareportecuotas);
    }

</script>