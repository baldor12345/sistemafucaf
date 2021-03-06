
<div id="divMensajeError{!! $entidad_cuota !!}1"></div>
{!! Form::model($credito, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('id_cuota', $cuota->id, array('id' => 'id_cuota')) !!}
{!! Form::hidden('id_credito', $cuota->credito_id, array('id' => 'id_credito')) !!}
{!! Form::hidden('id_cliente', $credito2->persona_id, array('id' => 'id_cliente')) !!}
{!! Form::hidden('valor_mora', $cuota->interes_mora, array('id' => 'valor_mora')) !!}

{!! Form::hidden('partecapital', $cuota->parte_capital, array('id' => 'partecapital')) !!}
{!! Form::hidden('cuotainteres', $cuota->interes, array('id' => 'cuotainteres')) !!}
{!! Form::hidden('cuotamora', $cuota->interes_mora, array('id' => 'cuotamora')) !!}
{!! Form::hidden('total', ($cuota->interes + $cuota->parte_capital+ $cuota->interes_mora), array('id' => 'total')) !!}


<div class="form-row">
    <div class="card-box table-responsive crbox">
     
        {!! Form::label('nombreSC', (trim($persona->tipo) =='S'?"Socio: ":"Cliente ").$persona->apellidos." ".$persona->nombres, array('class' => 'alert alert-success col-12 col-sm-12 col-xs-12') )!!}
        {!! Form::label('detalle', 'Detalles: ', array('class' => '')) !!}
        <ul class="">
            <li>{!! Form::label('partecapital', 'Parte capital: s/. '.$cuota->parte_capital, array('class' => 'psrtcap')) !!}</li>
          
            <li>{!! Form::label('cuotainteres', 'Interes: s/.'.$cuota->interes, array('class' => 'interesss')) !!}</li>
            
            <li>{!! Form::label('cuotamora', 'Interes Mora: s/.'.$cuota->interes_mora, array('class' => 'morass')) !!}</li>
          
            <li>{!! Form::label('comision', 'Comision: s/.'.$configuraciones->valor_recibo, array('class' => 'comisi')) !!}</li>
            {{-- {!! Form::text('cuotamora', $cuota->interes_mora, array('class' => 'form-control input-xs', 'id' => 'cuotamora', 'placeholder' => 's/.')) !!} --}}
        </ul>
        <div class="form-group col-12 col-md-12 col-sm-12">
            {!! Form::label('total', 'Total: s/.'.($cuota->interes + $cuota->parte_capital+ $cuota->interes_mora + $configuraciones->valor_recibo), array('class' => 'tol label-md ','style'=>'color: black;')) !!}
        </div>
       
        <div class="form-group col-12 col-md-12 col-sm-12">
            {!! Form::label('fecha_pago', 'Fecha de pago: *', array('class' => '')) !!}
            {!! Form::date('fecha_pagoc', $fechapago, array('class' => 'form-control input-sm', 'id' => 'fecha_pagoc')) !!}
        </div>

        <div class = "form-group">
            <div class="col-4 col-md-4 col-sm-12">
                <div class="form-group col-md-12">
                    {!! Form::label('monto_recibido_p', 'Monto Recibido: ', array('class' => '')) !!}
                    {!! Form::text('monto_recibido_p', null, array('class' => 'form-control input-sm ', 'id' => 'monto_recibido_p', 'placeholder' => 's/.','onkeypress'=>'return filterFloat(event,this);')) !!}
                </div>
            </div>
   
            <div class=" col-4 col-md-4 col-sm-12" >
                <div class="form-group col-md-12" >
                    {!! Form::label('monto_pago_p', 'Monto Pago s/.:', array('class' => '')) !!}
                    {!! Form::text('monto_pago_p', ($cuota->interes + $cuota->parte_capital+ $cuota->interes_mora + $configuraciones->valor_recibo), array('class' => 'form-control input-sm', 'id' => 'monto_pago_p', 'placeholder' => '', 'readonly')) !!}
                </div>
            </div>

            <div class=" col-4 col-md-4 col-sm-12" >
                <div class="form-group col-md-12" >
                    {!! Form::label('monto_dif_p', 'Diferencia s/.:', array('class' => '')) !!}
                    {!! Form::text('monto_dif_p', 0, array('class' => 'form-control input-sm', 'id' => 'monto_dif_p', 'placeholder' => '', 'readonly')) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-lg-12 col-md-12 col-sm-12 text-right">
    {!! Form::button('<i class="fa fa-check fa-lg"></i> Pagar Cuota', array('class' => 'btn btn-success btn-sm', 'id' => 'btnPagarcuota', 'onclick' => 'guardarPagoCuota(\''.$entidad_cuota.'\', this)')) !!}
    &nbsp;
    {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad_cuota, 'onclick' => 'cerrarModal();')) !!}
</div>
{!! Form::close() !!}
<?php
$fecha_pago = null;
?>
<script>
    $(document).ready(function(){
        init(IDFORMMANTENIMIENTO+'{!! $entidad_cuota !!}', 'M', '{!! $entidad_cuota !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad_cuota !!} :input[id="fecha_pagoc"]').focus();
		configurarAnchoModal('450');

        $("#imprimir_voucherpago").change(function(event) {
            var checkbox = event.target;
            if (checkbox.checked) {
                $("#imprimir_voucherpago").val(1);
            } else {
                $("#imprimir_voucherpago").val(0);
            }
        });

        $("#monto_recibido_p").on('keyup', function(){
           
            var monto_pago = RoundDecimal(parseFloat($('#monto_pago_p').val()), 1);
            var monto_recibido = RoundDecimal(parseFloat($(this).val()), 1) || 0;
            var monto_dif_p =RoundDecimal(monto_recibido - monto_pago, 1);
            $('#monto_dif_p').val(monto_dif_p);

        }).keyup();

        $("#modal"+(contadorModal - 1)).on('hidden.bs.modal', function () {
            $('.modal' + (contadorModal-2)).css('pointer-events','auto'); 
        });
    });

    function RoundDecimal(numero, decimales) {
        numeroRegexp = new RegExp('\\d\\.(\\d){' + decimales + ',}');   // Expresion regular para numeros con un cierto numero de decimales o mas
        if (numeroRegexp.test(numero)) {         // Ya que el numero tiene el numero de decimales requeridos o mas, se realiza el redondeo
            return Number(numero.toFixed(decimales));
        } else {
            return Number(numero.toFixed(decimales)) === 0 ? 0 : numero;  // En valores muy bajos, se comprueba si el numero es 0 (con el redondeo deseado), si no lo es se devuelve el numero otra vez.
        }
    }

    function guardarPagoCuota(entidad, idboton) {
        var fechap = ($('#fecha_pagoc').val()).split("-");
        var anio_mes = fechap[0]+"-"+fechap[1];
        var monto_diferencia = parseFloat($('#monto_dif_p').val());
        if(monto_diferencia >=0){
            if(anio_mes >= "{{ date('Y-m',strtotime($cuota->fecha_programada_pago)) }}"){

                var idformulario = IDFORMMANTENIMIENTO + entidad;
                var data = submitForm(idformulario);
                var respuesta  = '';
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
                            if("{{ $entidad_recibo }}" != "0" || "{{ $entidad_recibo }}" != "2"){
                                if("{{ $entidad_recibo }}" == "nan"){
                                    buscarCompaginado('', 'Accion realizada correctamente', entidad, 'OK');
                                    buscar('{{ $entidad_credito }}');
                                }else{
                                    buscar('{{ $entidad_recibo }}');
                                }
                            }
                        
                            var rutarecibopagocuota = "{{ URL::route($ruta['generarecibopagocuotaPDF'], array($cuota->id))}}";
                            //window.open(rutarecibopagocuota+"/{{ $cuota->id }}", "Voucher credito", "width=700, height=500, left=50, top=20");
                            var anchoModal = '700';
                            modalrecibopdf(rutarecibopagocuota,anchoModal);
                            
                        } else {
                            var msj = "<div class='alert alert-danger'><strong>¡Error!</strong> "+respuesta+"</div>";
                            $('#divMensajeError{{ $entidad_cuota }}1').html(msj);
                            $('#divMensajeError{{ $entidad_cuota }}1').show();
                        // mostrarErrores(respuesta, idformulario, entidad);
                        }
                    }
                });
            }else{
                var msj = "<div class='alert alert-danger'><strong>¡Error!</strong> La fecha seleccionada es menor a la fecha de pago.!</div>";
                $('#divMensajeError{{ $entidad_cuota }}1').html(msj);
                $('#divMensajeError{{ $entidad_cuota }}1').show();
            }
        }else{
            var msj = "<div class='alert alert-danger'><strong>¡Error!</strong>El monto recibido es menor al monto de pago.</div>";
                $('#divMensajeError{{ $entidad_cuota }}1').html(msj);
                $('#divMensajeError{{ $entidad_cuota }}1').show();
                $('#monto_recibido_p').focus();
        }
    }

</script>