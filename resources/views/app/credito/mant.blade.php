
<div id="divinfo"></div>
<div id="divInfo{!! $entidad !!}"></div>
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($credito, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! Form::hidden('numcreditos', 0, array('id' => 'numcreditos')) !!}
{!! Form::hidden('estado', 'I', array('id' => 'estado')) !!}
<div class="form-row">
    <div class="form-group col-6 col-md-6 col-sm-12">
        {!! Form::label('selectnom', 'Socio o Cliente: ', array('class' => 'cliente')) !!}
        {!! Form::select('selectnom', $cboPers, null, array('class' => 'form-control input-sm', 'id' => 'selectnom')) !!}
        <input type="hidden" id="persona_id" name="persona_id" value="" tipocl=''>
    </div>

    <div class="form-group col-6 col-md-6 col-sm-12" style="margin-left: 10px">
        {!! Form::label('selectaval', 'Aval: ', array('class' => 'aval')) !!}
        {!! Form::select('selectaval', $cboPers, 0, array('class' => 'form-control input-sm', 'id' => 'selectaval')) !!}
        <input type="hidden" id="pers_aval_id", name="pers_aval_id" value="0" tipoavl=''>
    </div>

    <div class="form-group col-6 col-md-6 col-sm-12">
        {!! Form::label('valor_credito', 'Valor de Credito: *', array('class' => 'valor_cred')) !!}
        {!! Form::text('valor_credito', null, array('class' => 'form-control input-xs', 'id' => 'valor_credito', 'placeholder' => 's/.','onkeypress'=>'return filterFloat(event,this);')) !!}
    </div>
    <div class="form-group col-6 col-md-6 col-sm-12" style="margin-left: 10px">
        {!! Form::label('tasa_interes', 'Interes mensual (%):', array('class' => 'tasa_int')) !!}
        {!! Form::text('tasa_interes', ($configuraciones->tasa_interes_credito*100).'', array('class' => 'form-control input-xs', 'id' => 'tasa_interes', 'placeholder' => 'Ingrese el interes mensual %','onkeypress'=>'return filterFloat(event,this);', 'readonly')) !!}
    </div>

    <div class="form-group col-6 col-md-6 col-sm-12">
        {!! Form::label('periodo', 'Periodo (N° Meses): *', array('class' => 'period')) !!}
        {!! Form::text('periodo', null, array('class' => 'form-control input-xs', 'id' => 'periodo', 'placeholder' => 'Ingrese Numero de meses', 'onkeypress'=>'return filterFloat(event,this);')) !!}
    </div>

    <div class="form-group col-6 col-md-6 col-sm-12" style="margin-left: 10px">
        {!! Form::label('fechacredito', 'Fecha: *', array('class' => 'fechacred')) !!}
        {!! Form::date('fechacredito', $fecha_pordefecto, array('class' => 'form-control input-xs', 'id' => 'fechacredito')) !!}
    </div>
    <div class="form-group col-12" >
        {!! Form::label('descripcion', 'Descripción: ', array('class' => 'descrip')) !!}
        {!! Form::textarea('descripcion', null, array('class' => 'form-control input-sm','rows' => 4, 'id' => 'descripcion', 'placeholder' => 'Ingrese descripción')) !!}
    </div>
    <div class="form-group col-6 col-md-6 col-sm-6" >
        {!! Form::button('<i class="fa fa-check fa-lg"></i>Ver Cronograma Cuotas', array('class' => 'btn btn-success btn-sm', 'id' => 'btnCronograma', 'onclick' => 'generarCronograma();')) !!}
    </div>
</div>

<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardarCredito(\''.$entidad.'\', \''.URL::route($ruta["generarecibocreditoPDF"], array()).'\')')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>

{!! Form::close() !!}
<script>
    $(document).ready(function() {
        $('#selectnom').select2({
            dropdownParent: $("#modal"+(contadorModal-1)),
            
            minimumInputLenght: 2,
            ajax: {
               
                url: "{{ URL::route($ruta['listpersonas'], array()) }}",
                dataType: 'json',
                delay: 250,
                data: function(params){
                    return{
                        q: $.trim(params.term)
                    };
                },
                processResults: function(data){
                    return{
                        results: data
                    };
                }
                
            }
        });


        $('#selectnom').change(function(){
            $('#selectaval').select2("val", "0");
            $.get("creditos/"+$(this).val()+"",function(response, facultad){
                var persona = response[0];
                var numCreditos = response[1];
                var numAcciones = response[2];

                if(persona.length>0){
                    $("#persona_id").val(persona[0].id);
                    var msj = "<div class='alert alert-success'><strong>¡Detalles: !</strong><ul><li>Nombre: "+persona[0].nombres+" "+persona[0].apellidos+"</li><li>Tipo: "+(persona[0].tipo.trim() == 'C'? "Cliente": "Socio")+"</li><li>Creditos activos: "+numCreditos+"</li><li>Acciones: "+numAcciones+"</li></ul> </div>";
                        $('#divInfo{{ $entidad }}').html(msj);
                        $('#divInfo{{ $entidad }}').show();
                        $('#numcreditos').val(numCreditos);
                    if( persona[0].tipo.trim() == 'S'){
                        $("#persona_id").attr('tipocl','S');
                    }else{
                        $("#persona_id").attr('tipocl','C');
                    }
                }else{
                    $("#persona_id").val(0);
                }
            });
        });

        $('#selectaval').select2({
            dropdownParent: $("#modal"+(contadorModal-1)),
            minimumInputLenght: 2,
            ajax: {
                url: "{{ URL::route($ruta['listpersonas'], array()) }}",
                dataType: 'json',
                delay: 250,
                data: function(params){
                    return{
                        q: $.trim(params.term)
                    };
                },
                processResults: function(data){
                    return{
                        results: data
                    };
                },
                cache: true
            }
        });
        $('#selectaval').change(function(){
            $.get("creditos/"+$(this).val()+"",function(response, facultad){
                var persona = response[0];
                var numCreditos = response[1];
                var numAcciones = response[2];
                $('#pers_aval_id').val('');
                if(persona.length>0){
                    $("#pers_aval_id").val(persona[0].id);
                    $('#pers_aval_id').attr('tipoavl', ''+(persona[0].tipo).trim());
                }else{
                    $("#pers_aval_id").val(0);
                    $('#pers_aval_id').attr('tipoavl', '0');
                }
                if($('#selectnom').val() == $('#selectaval').val()){
                    var msj = "<div class='alert alert-danger'><strong>¡Error!</strong> El aval no debe ser el mismo que el acreditado.!</div>";
                    $('#divMensajeError{{ $entidad }}').html(msj);
                    $('#divMensajeError{{ $entidad }}').show();
                }else{
                    $('#divMensajeError{{ $entidad }}').html("");
                    $('#divMensajeError{{ $entidad }}').hide();
                }
            });
        });
        $(".cliente").html('Socio o Cliente: <sup style="color: red;">Obligatorio</sup>');
        $(".aval").html('Socio o Cliente: <sup style="color: blue;">Opcional</sup>');
        $(".valor_cred").html('Valor de Crédito: <sup style="color: red;">Obligatorio</sup>');
        $(".period").html('Periodo (N° Meses): <sup style="color: red;">Obligatorio</sup>');
        $(".fechacred").html('Fecha: <sup style="color: red;">Obligatorio</sup>');
        $('.descrip').html(' Descripción: <sup style="color: blue;">Opcional</sup>');
        init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
        $(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="dnicliente"]').focus();
        configurarAnchoModal('650');
        $('#divinfo').html('<div class="alert bg-warning" role="alert"><strong>SALDO EN CAJA (S/.): </strong>{{ $saldo_en_caja }}</div>');
    
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
    function valid(){
       var res = true;
        if(isNaN($('#periodo').val()) || $('#periodo').val().length < 1 || $('#persona_id').val() == 0 || $('#valor_credito').val().length <= 0 || $('#periodo').val() <= 0 || $('#valor_credito').val() <= 0 || $('#selectnom').val() == '0'){
            res = false;
        }
        return  res;
    }

    function guardarCredito(entidad, rutarecibo) {
        var valida = true;
        var mensaje = "";
        if(valid()){
            if($('#numcreditos').val() == 1 ){
                if($('#periodo').val() > 1){
                    valida = false;
                    mensaje = "El periodo es mayor a 1, el socio solo puede tener un credito más a una sola cuota.!";
                }
            }else if($('#numcreditos').val() >= 2 ){
                mensaje = "¡El Socio o Cliente no puede obtener más créditos, ya cuenta con 2 créditos activos.!";
                valida = false;
            }
            if({{ $saldo_en_caja }} < $('#valor_credito').val()){
                console.log("saldo en caja: "+{{ $saldo_en_caja }});
                console.log("valor credito: "+$('#valor_credito').val());
                mensaje = "¡El monto de crédito ingresado, supera el saldo actual en caja!";
                valida = false;
            }

            if(valida){
                var val_aval = true;
                if($('#pers_aval_id').attr('tipoavl') != 'S' && $('#pers_aval_id').val() != 0){
                    val_aval = false;
                    var msj = "<div class='alert alert-danger'><strong>¡Error!</strong> El aval Ingresado no es un socio.! </div>";
                    $('#divMensajeError{{ $entidad }}').html(msj);
                    $('#divMensajeError{{ $entidad }}').show();
                }
                
                if(val_aval){
                    if($('#selectnom').val() != $('#selectaval').val()){
                        var idformulario = IDFORMMANTENIMIENTO + entidad;
                        var data = submitForm(idformulario);
                        var respuesta = null;
                        var listar = 'NO';
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
                    }else{
                        var msj = "<div class='alert alert-danger'><strong>¡Error!</strong> El aval no debe ser el mismo que el acreditado.!</div>";
                        $('#divMensajeError{{ $entidad }}').html(msj);
                        $('#divMensajeError{{ $entidad }}').show();
                    }
                    
                }
                
            }else{
                var msj = "<div class='alert alert-danger'><strong>¡Error!</strong> "+mensaje+"</div>";
                $('#divMensajeError{{ $entidad }}').html(msj);
                $('#divMensajeError{{ $entidad }}').show();
            }
        }else{
            var msj = "<div class='alert alert-danger'><strong>¡Error!</strong> Asegurese de rellenar los correctamente los campos, dni, valor credito (el valor debe ser mayor a '0'), periodo (el valor debe ser mayor a '0'), y fecha</div>";
                $('#divMensajeError{{ $entidad }}').html(msj);
                $('#divMensajeError{{ $entidad }}').show();
        }
    }

    function abrirmodal(datahtml){
        var boxmodal = bootbox.dialog({
                                title: 'Cronograma de Cuotas',
                                className: 'modalcronograma',
                                message: ''+datahtml,
                                closeButton: true
                            });
            boxmodal.prop('id', 'modalcronograma');
            boxmodal.modal('show');
            $('#btncerraCronograma').click(function(){
                boxmodal.modal("hide");
                $('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"}); 
            });
            $("#modalcronograma").on('hidden.bs.modal', function () {
                $('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});   
            });
            var divModal = '.modalcronograma';
            $(divModal).children('.modal-dialog').css('width','auto');
            $(divModal).children('.modal-dialog').css('max-width', '550px');
            $(divModal).children('.modal-dialog').css('margin-left','auto');
            $(divModal).children('.modal-dialog').css('margin-right','auto');
            
    }

    function generarCronograma(){
        var periodo= parseInt($('#periodo').val());
		var Monto= parseFloat($('#valor_credito').val());
		var Interes= parseFloat($('#tasa_interes').val());
		var CapitalInicial= parseFloat($('#valor_credito').val());
        if(isNaN(periodo)|| isNaN(Monto) || isNaN(Interes)){
            bootbox.alert("<div class='alert alert-danger'><strong>¡Error!</strong> Asegurese que los campos Monto, Interes mensual, Periodo y fecha, contengan valor para el debido cálculo. !</div>");
        }else{
            
            var mdCronograma = "<fieldset class='col-12'><table id='example1' class='table table-bordered table-striped table-condensed table-hover'><thead><tr>"+
                        "<th style='width: 5%' class='text-center'>#</th>"+
                        "<th style='width: 10%'>INTERES</th>"+
                        "<th style='width: 30%' class='text-center'>PARTE CAPITAL</th>"+
                        "<th style='width: 30%' class='text-center'>MONTO CUOTA</th>"+
                        "<th style='width: 25%' class='text-center'>FECHA DE PAGO</th>"+
                        "</tr></thead><tbody id='filasTcuotas'></tbody></table>"+
                        "<div class='form-row'><div class='form-group col-12' >"+
                        "<label for='' id='interesTotal' class=''>Interes total: </label>"+
                        "<label for='' id='capitalTotal' class=''>Capital total: </label>"+
                        "</div></div><div class='modal-footer'><button type='button' class='btn btn-secondary' id='btncerraCronograma' >Close</button></div>"+
                        "</fieldset>";
            
            abrirmodal(mdCronograma);
            $('#filasTcuotas').empty();
            
            var montInteres=0.0;
            var montCapital=0.0;
            var montCuota = 0.0;
            var fechac = new Date($('#fechacredito').val());
            fechac.setDate(fechac.getDate() + 1);
            var interesAcumulado=0.0;
            var capitalTotal = 0.0;
            var sumacuotas = 0.0;
            var fila='';
            //FORMULA: CUOTA = (Interes * CpitalInicial)/(1-  (1/ (1+InteresMensual)^NumeroCuotas)  );  Math.pow(7, 2);
            montCuota =((Interes/100) * CapitalInicial) / (1 - (Math.pow(1/(1+(Interes)/100), periodo)));
            var i=0;
            
            for(i=0; i<periodo; i++){
                fechac.setMonth(fechac.getMonth() + 1);
                var day = ("0" + fechac.getDate()).slice(-2);
                var month = ("0" + (fechac.getMonth() + 1)).slice(-2);
                montInteres =  (Interes/100)*CapitalInicial;
                interesAcumulado = montInteres + interesAcumulado;
                montCapital= (montCuota - montInteres);
                CapitalInicial = CapitalInicial - montCapital;
                capitalTotal += montCapital;
                sumacuotas += montCuota;
                fila = fila + "<tr>"
                        +"<td>"+(i+1)+"</td>"
                        +"<td>"+RoundDecimal(montInteres,1)+"</td>"
                        +"<td>"+RoundDecimal(montCapital,1)+"</td>"
                        +"<td>"+RoundDecimal(montCuota,1)+"</td>"
                        +"<td>"+fechac.getDate()+"/"+(fechac.getMonth()+1)+"/"+(fechac.getFullYear())+"</td>"
                        +"</tr>";
            }
            
            interesAcumulado = interesAcumulado;
            fila += "<tr><td>TOTAL</td><td>"+RoundDecimal(interesAcumulado,1)+"</td><td>"+
                    RoundDecimal(capitalTotal,1)+"</td><td>"+RoundDecimal(sumacuotas,1)+"</td></tr>";
            
            $("#filasTcuotas").append(fila);
            $('#interesToal').empty();
            $('#capitalTotal').empty();
            $('#interesTotal').text("Interes total: " +RoundDecimal(interesAcumulado,1));
            $('#capitalTotal').text("Total al finalizar: " + (RoundDecimal(capitalTotal,1)+RoundDecimal(interesAcumulado,1)));
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

</script>