
<div id="divMensajeError1"></div>
<div class="form-row">
    <div class="card-box table-responsive crbox">
        <div class="form-group col-12 col-md-12 col-sm-12" >
            {!! Form::label('fecha_i', 'Fecha de pago: *', array('class' => '')) !!}
            {!! Form::date('fecha_i', $fecha_inicio, array('class' => 'form-control input-xs', 'id' => 'fecha_i')) !!}
        </div>
        <div class="form-group col-12 col-md-12 col-sm-12" >
            {!! Form::label('fecha_f', 'Fecha de pago: *', array('class' => '')) !!}
            {!! Form::date('fecha_f', $fecha_inicio, array('class' => 'form-control input-xs', 'id' => 'fecha_f')) !!}
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 text-right">
    {!! Form::button('<i class="fa fa-check fa-lg"></i> Imprimir', array('class' => 'btn btn-success btn-sm', 'id' => 'btnReporteCred', 'onclick' => 'imprimirReporte()')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelarReport', 'onclick' => 'cerrarModal();')) !!}
</div>

<script>
    $(document).ready(function(){
      
		configurarAnchoModal('450');

        $("#modal"+(contadorModal - 1)).on('hidden.bs.modal', function () {
            $('.modal' + (contadorModal-2)).css('pointer-events','auto'); 
        });
    });
    function imprimirReporte(){
        
        var rutareportecuotas = "{{ URL::route($ruta['reportecreditos'], array()) }}";
        var fechai = $('#fecha_i').val();
        var fechaf = $('#fecha_f').val();

        rutareportecuotas += "?fechainicio="+fechai+"&fechafinal="+fechaf;
        console.log("RUTALS: "+rutareportecuotas);
        modalrecibopdf(ruta, 200,'200');
    }

</script>