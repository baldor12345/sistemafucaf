
<div id="divMensajeError{!! $entidad_cuota !!}"></div>
{!! Form::model($credito, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('id_cuota', $cuota->id, array('id' => 'id_cuota')) !!}
{!! Form::hidden('id_credito', $cuota->credito_id, array('id' => 'id_credito')) !!}
{!! Form::hidden('id_cliente', $credito2->persona_id, array('id' => 'id_cliente')) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-row">
    <div class="form-group col-12 col-md-12 col-sm-12" >
        {!! Form::label('fecha_pago', 'Fecha de pago: *', array('class' => '')) !!}
        {!! Form::date('fecha_pago', null, array('class' => 'form-control input-xs', 'id' => 'fecha_pago')) !!}
    </div>
    <div class="form-check form-group col-12 col-md-12 col-sm-12">
        {!! Form::checkbox('imprimir_voucherpago', '0', false, array('class' => 'form-check-input', 'id' => 'imprimir_voucherpago')) !!}
        {!! Form::label('imprimir_voucherpago', 'IMPRIMIR VOUCHER:', array('class' => 'form-check-label')) !!}
    </div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Pagar Cuota', array('class' => 'btn btn-success btn-sm', 'id' => 'btnPagarcuota', 'onclick' => 'guardarPagoCuota(\''.$entidad_cuota.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad_cuota, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>

{!! Form::close() !!}
<script>
    $(document).ready(function(){
        var fechaActual = new Date();
        var day = ("0" + fechaActual.getDate()).slice(-2);
        var month = ("0" + (fechaActual.getMonth()+1)).slice(-2);
        var fechaactualc = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
        $('#fecha_pago').val(fechaactualc);

        init(IDFORMMANTENIMIENTO+'{!! $entidad_cuota !!}', 'M', '{!! $entidad_cuota !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad_cuota !!} :input[id="fechapago"]').focus();
		configurarAnchoModal('450');

        $("#imprimir_voucherpago").change(function(event) {
            var checkbox = event.target;
            if (checkbox.checked) {
                $("#imprimir_voucherpago").val(1);
            } else {
                $("#imprimir_voucherpago").val(0);
            }
        });

        $("#modal"+(contadorModal - 1)).on('hidden.bs.modal', function () {
            $('.modal' + (contadorModal-2)).css('pointer-events','auto'); 
        });

    });

    
    function guardarPagoCuota (entidad, idboton) {
        var idformulario = IDFORMMANTENIMIENTO + entidad;
        var data         = submitForm(idformulario);
        var respuesta    = '';
        var btn = $(idboton);
        btn.button('loading');
        data.done(function(msg) {
            respuesta = msg;
        }).fail(function(xhr, textStatus, errorThrown) {
            respuesta = 'ERROR';
        }).always(function() {
            btn.button('reset');
            if(respuesta === 'ERROR'){
            }else{
                if (respuesta === 'OK') {
                    cerrarModal();
                    buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
                    buscar('{{ $entidad_credito }}');
                    if($("#imprimir_voucherpago").val() == 1){
                        var rutarecibopagocuota = "{{ URL::route($ruta['generarecibopagocuotaPDF'], array($cuota->id))}}";
                        //window.open(rutarecibopagocuota+"/{{ $cuota->id }}", "Voucher credito", "width=700, height=500, left=50, top=20");
                        var anchoModal = '700';
                        modalrecibopdf(rutarecibopagocuota,anchoModal);
                    }  
                } else {
                    mostrarErrores(respuesta, idformulario, entidad);
                }
            }
        });
    }

</script>