@if($caja != null)

    @if($num_cuotas_porpagar<=0)
    <div id="divMensajeErrorRef"></div>
    <div class="card-box crbox">
        {!! Form::label('nombresoc', (trim($persona->tipo) =='S'?"Socio: ":"Cliente ").$persona->apellidos." ".$persona->nombres) !!}
    </div>
    <div class="row" >
        <div class="col-sm-12">
            <div class="card-box crbox">
                <div class="form-row">
                        {!! Form::label('saldo_rest', 'Monto deuda pendiente: S/.'.$saldo_restante, array('class' => '')) !!}
                        {!! Form::label('val_cu_actual', 'Valor de cuota actual: S/.'.round($cuota_siguiente->parte_capital + $cuota_siguiente->interes,1), array('class' => '')) !!}
                        {!! Form::label('num_cu', 'N° Cuotas pendientes Actual: '.$numero_cuotas_pendientes, array('class' => '')) !!}
                    
                    <div class="form-group">
                        {!! Form::label('fecha_Act', 'Fecha Actual:', array('class' => '')) !!}
                        {!! Form::date('fecha_Act', date('Y-m-d',strtotime($fecha_actual)), array('class' => 'form-control input-xs', 'id' => 'fecha_Act')) !!}
                    </div>
                    
                    <div class="form-group">
                        {!! Form::label('monto_amortizar', 'Monto Amortizar * ', array('class' => '')) !!}
                        {!! Form::text('monto_amortizar', 0, array('class' => 'form-control input-xs', 'id' => 'monto_amortizar', 'placeholder' => 'Ingrese monto de amortización', 'onkeypress'=>'return filterFloat(event,this);')) !!}
                        
                    </div>
                    <div class="form-group">
                        {!! Form::label('num_cu_nuevo', 'N° Cuotas nuevas pendientes: * ', array('class' => '')) !!}
                        {!! Form::text('num_cu_nuevo', $numero_cuotas_pendientes, array('class' => 'form-control input-xs', 'id' => 'num_cu_nuevo', 'placeholder' => 'Ingrese Numero de cuotas', 'onkeypress'=>'return filterFloat(event,this);')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('nuevo_valor_cuota', 'Nuevo valor de cuota: '.round($cuota_siguiente->parte_capital + $cuota_siguiente->interes,1), array('class' => '','id'=>'lblNuevovalcuota')) !!}
                        {!! Form::hidden('nuevo_saldo_rest', $saldo_restante, array('id' => 'nuevo_saldo_rest')) !!}
                        {!! Form::label('nuevo_saldo_restante', 'Nuevo valor de deuda: S/. '.round($saldo_restante, 1), array('class' => '', 'id'=>'lblNuevosaldorest')) !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
                {!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardarRef', 'onclick' => 'guardarRef(\''.$credito->id.'\', \''.$fecha_actual.'\', \''.$numero_cuotas_pendientes.'\', this)')) !!}
                &nbsp;
                {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelarRef', 'onclick' => 'cerrarModal();')) !!}
            </div>
        </div>
    </div>
    <script>
            $(document).ready(function() {
                $("#num_cu_nuevo").on('keyup', function(){
                   calcularCuota('{{ $saldo_restante }}', '{{ $credito->tasa_interes }}');
                }).keyup();
                $("#monto_amortizar").on('keyup', function(){
                   calcularCuota('{{ $saldo_restante }}', '{{ $credito->tasa_interes }}');
                }).keyup();
                configurarAnchoModal('450');
            });
            function filterFloat(evt,input){
                // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
                var key = window.Event ? evt.which : evt.keyCode;    
                var chark = String.fromCharCode(key);
                var tempValue = input.value+chark;
                if(key >= 48 && key <= 57){
                    if(filter(tempValue)=== false){
                        return false;
                    }else{       
                        return true;
                    }
                }else{
                    if(key == 8 || key == 13 || key == 0) {     
                        return true;              
                    }else if(key == 46){
                            if(filter(tempValue)=== false){
                                return false;
                            }else{       
                                return true;
                            }
                    }else{
                        return false;
                    }
                }
            }
            function filter(__val__){
                var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
                if(preg.test(__val__) === true){
                    return true;
                }else{
                return false;
                }
            }
        
            function guardarRef(credito_id, fecha, numero_cuotas_anterior, btn) {
                if(parseInt($('#num_cu_nuevo').val())>0 & $('#num_cu_nuevo').val() != ""){
                    if(parseFloat($('#nuevo_saldo_rest').val()) >= 0){
                        var parametros = "saldo_rest="+$('#nuevo_saldo_rest').val()+"&credito_id="+credito_id+"&num_cuotas_anterior_p="+numero_cuotas_anterior+"&num_cuotas_nuevas_p="+$('#num_cu_nuevo').val()+
                        "&monto_amortizar="+$('#monto_amortizar').val()+"&fecha_ref="+fecha
                        $(btn).button('loading');
                        $(btn).removeClass('disabled');
                        $(btn).removeAttr('disabled');
                        $(btn).html('<i class="fa fa-check fa-lg"></i> Guardar');
                        $.ajax({
                            url: "creditos/guardar_refinanciacion",
                            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            type: 'GET',
                            data: parametros,
                            beforeSend: function(){
                            },
                            success: function(res){
                                if(res == 'OK'){
                                    cerrarModal();
                                    buscar('Credito');
                                    mostrarMensaje ("Refinanciacion realizada", "OK");

                                }else{
                                    mostrarMensaje (""+res, "OK");
                                
                                }
                            }
                        }).fail(function(){
                            $(btn).removeClass('disabled');
                            $(btn).removeAttr('disabled');
                            $(btn).html('Guardar');
                                mostrarMensaje ("Error de consulta..", "ERROR");
                        });
                    }else{
                        $('#divMensajeErrorRef').html('<div class="alert alert-danger">El monto a amortizar es mayor a la deuda actual.</div>');
                        $('#monto_amortizar').focus();
                    }
                    
                }else{
                    $('#divMensajeErrorRef').html('<div class="alert alert-danger">El número de nuevas cuotas pendientes, debe ser numerico y mayor que 0.</div>');
                    $('#num_cu_nuevo').focus();
                }
            }
        
            function RoundDecimal(numero, decimales) {
                numeroRegexp = new RegExp('\\d\\.(\\d){' + decimales + ',}');   // Expresion regular para numeros con un cierto numero de decimales o mas
                if (numeroRegexp.test(numero)) {         // Ya que el numero tiene el numero de decimales requeridos o mas, se realiza el redondeo
                    return Number(numero.toFixed(decimales));
                } else {
                    return Number(numero.toFixed(decimales)) === 0 ? 0 : numero;  // En valores muy bajos, se comprueba si el numero es 0 (con el redondeo deseado), si no lo es se devuelve el numero otra vez.
                }
            }
            function calcularCuota(saldo_restante_ant, tasa_interes){
                //console.log(saldo_restante_ant+" - interes: "+tasa_interes);
                var monto_amortizar = parseFloat($('#monto_amortizar').val() == ""?0:$('#monto_amortizar').val());
                if(monto_amortizar >= 0){
                    var saldo_restante = RoundDecimal(parseFloat(saldo_restante_ant) - parseFloat(monto_amortizar), 1);
                    var num_cuotas_nuevas = parseInt($('#num_cu_nuevo').val()==""?0:$('#num_cu_nuevo').val());
                    var valor_cuota =RoundDecimal(((tasa_interes/100) * saldo_restante) / (1 - (Math.pow(1/(1+(tasa_interes)/100), num_cuotas_nuevas))), 1);
                    $('#lblNuevosaldorest').html('Nuevo valor de deuda: S/. '+ RoundDecimal(saldo_restante, 1));
                    $('#lblNuevovalcuota').html('Nuevo valor de cuota: '+RoundDecimal(valor_cuota, 1));
                    $('#nuevo_saldo_rest').val(RoundDecimal(saldo_restante, 1));
                }else{
                    $('#lblNuevosaldorest').html('El monto de amortizacion debe ser mayor o ygual a 0 ');
                }
            }
        </script>
    @else
        <div class="card-box table-responsive crbox">
            <div class="alert alert-warning">
                <label><strong>Cuenta con {{ $num_cuotas_porpagar }} cuota(s) pendientes a pagar a la fecha</strong> Asegurece de pagar las cuotas pendientes a la fecha, antes de realizar esta operacion</label>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
                &nbsp;
                {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelarRef', 'onclick' => 'cerrarModal();')) !!}
            </div>
        </div>
    @endif
@else
    <div class="card-box table-responsive crbox">
        <div class="alert alert-warning">
            <label><strong>Caja no aperturada</strong>, Asegurece de aperturar caja primero. </label>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
            &nbsp;
            {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelarRef', 'onclick' => 'cerrarModal();')) !!}
        </div>
    </div>
@endif