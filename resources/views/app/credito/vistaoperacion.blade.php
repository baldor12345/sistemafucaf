<script type="text/javascript">
    // var rutareportecuotas = "{{ URL::route($ruta['generareportecuotasPDF'], array())}}";
    // function imprimirpdf(){
    //     window.open(rutareportecuotas+"/{{ $credito->id }}", "Cuotas de Credito", "width=700, height=800, left=50, top=20");
    //}
 </script>
 <div id="divMensajeError{!! $entidad_cuota !!}"></div>
 <div class="card-box table-responsive crbox">
    <div class="alert alert-warning">
        <label><strong>¡Aviso!</strong> Antes de realizar alguna operación, asegurese primero de realizar sus pagos de cuotas pendientes, para no tener posibles errores. ¡Gracias!</label>
    </div>
</div>

 <div class="card-box table-responsive crbox">
     <div class="form-row lbldatos">
         <div class="form-group col-12 col-md-12 col-sm-12 lbldatos">
             {!! Form::label('', 'Socio o Cliente: '.$persona->nombres.' '.$persona->apellidos, array('id'=>'cliente','class' => '')) !!}
         </div>
     </div>
 </div>
 <div class="row" >
     <div class="col-sm-12">
         <div class="card-box table-responsive crbox">
             <div class="row m-b-30" id="selectfilas">
                 <div class="col-sm-12">
                     {!! Form::open(['route' => $ruta["listardetallecuotas"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaCuotas']) !!}
                     {!! Form::hidden('page', 1, array('id' => 'page')) !!}
                     {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
                     {!! Form::hidden('credito_id', $credito->id, array('id' => 'credito_id')) !!}
                     {!! Form::hidden('persona_id', $persona->id, array('id' => 'persona_id')) !!}
                     {!! Form::hidden('opcion', '', array('id' => 'opcion')) !!}
                     {!! Form::hidden('montototal', 0, array('id' => 'montototal')) !!}
                     {!! Form::hidden('interes_total', 0, array('id' => 'interes_total')) !!}
                     {!! Form::hidden('capital_total', 0, array('id' => 'capital_total')) !!}
                     {!! Form::hidden('anio', $anioactual, array('id' => 'anio')) !!}
                     {!! Form::hidden('mes', $mesactual, array('id' => 'mes')) !!}
                     <div class="form-group">
						{!! Form::label('fechaop', 'Fecha Actual:', array('class' => '')) !!}
						{!! Form::date('fechaop', $fecha_pordefecto, array('class' => 'form-control input-sm', 'id' => 'fechaop',  'onchange' => 'listarcuotasalafecha()')) !!}
					</div>
                     {!! Form::close() !!}
                 </div>

             </div>
             <div class="form-group col-12 col-md-12 col-sm-12">
                {!! Form::label('accioncredito', 'Operación a realizar: ', array('class' => '')) !!}
                {!! Form::select('accioncredito', $cboacciones, 0, array('class' => 'form-control input-sm', 'id' => 'accioncredito',  'onchange' => 'realizaoperacion(this)')) !!}
             </div>
         </div>

         <div class="card-box crbox">
             <div id="cuotas_pendiente">

             </div>
         </div>

         <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
             &nbsp;
             {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad_cuota, 'onclick' => 'cerrarModal();')) !!}
         </div>
         
     </div>
 </div>
 <script type="text/javascript">
     $(document).ready(function() {
         configurarAnchoModal('650');
         $('.lbldatos').css({'padding':'0px','margin':'2px 0px'});
         $('.crbox').css({'padding':'5px 15px'});
         $('.contbtn').css({'padding': '10px 5'});
        // buscar('{{ $entidad_cuota }}');
         init(IDFORMBUSQUEDA+'{{ $entidad_cuota }}', 'B', '{{ $entidad_cuota }}');
         listarcuotasalafecha();
         
     });

     function listarcuotasalafecha(){
        $("#montototal").val(0);
        var fechadate = new Date($('#fechaop').val());
       
        $("#anio").val(fechadate.getFullYear());
        $("#mes").val(parseInt(fechadate.getMonth()) + 1);
       
         if($('#accioncredito').val() == '1' || $('#accioncredito').val() == '2' || $('#accioncredito').val() == '3'){
            $.ajax({
                url: "creditos/cuotasalafecha",
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                type: 'GET',
                data: $('#formBusquedaCuotas').serialize(),
                beforeSend: function(){
                },
                success: function(res){
                    var tabla="";
                    $('#cuotas_pendiente').empty();
                    switch ($('#accioncredito').val()) {
                        case '1':
                            var length = Object.keys(res).length;
                            tabla = "<label>Cuotas a pagar a la fecha: </label>";
                            if(length >0){
                                tabla += '<table class="table table-bordered table-striped table-condensed table-hover"><thead>'+
                                '<tr><th>Fecha</th><th>Numero</th><th>Monto S/.</th><th>Mora S/.</th><th>TOTAL</th><th>Operación</th></tr></thead><tbody>';
                                for(i=0;i<length;i++){
                                    tabla += "<tr><td>"+res[i].mes+"-"+res[i].anio+"</td><td>"+res[i].numero_cuota+"</td><td>"+(parseFloat(res[i].parte_capital)+parseFloat(res[i].interes)).toFixed(1)+"</td><td>"+res[i].interes_mora+"</td><td>"+(parseFloat(res[i].parte_capital)+parseFloat(res[i].interes)+parseFloat(res[i].interes_mora)).toFixed(1)+"</td>"+
                                    '<td><button class="btn btn-success btn-xs" cuota_id="'+res[i].cuota_id+'" onclick ="abirmodal(this);"><i class="fa fa-money fa-lg"></i> Pagar</button></td>';
                                }
                                tabla += "</tbody></table>";
                            
                            }else{
                                tabla += "<div>No se encontraron cuotas pendientes ...</div>";
                            }
                            $('#cuotas_pendiente').html(tabla);
                            $('#opcion').val(1);
                            break;
                        case '2':
                            var length = Object.keys(res).length;
                            tabla = "<label>Cuotas a pagar a la fecha: </label>";
                            if(length >0){
                                tabla += '<table class="table table-bordered table-striped table-condensed table-hover"><thead>'+
                                '<tr><th>Fecha</th><th>Numero</th><th>Monto S/.</th><th>Mora S/.</th><th>TOTAL</th><th>Operación</th></tr></thead><tbody>';
                                for(i=0;i<length;i++){
                                    tabla += "<tr><td>"+res[i].mes+"-"+res[i].anio+"</td><td>"+res[i].numero_cuota+"</td><td>"+(parseFloat(res[i].parte_capital)+parseFloat(res[i].interes)).toFixed(1)+"</td><td>"+res[i].interes_mora+"</td><td>"+(parseFloat(res[i].parte_capital)+parseFloat(res[i].interes)+parseFloat(res[i].interes_mora)).toFixed(1)+"</td>"+
                                    '<td><button class="btn btn-success btn-xs" cuota_id="'+res[i].cuota_id+'" onclick ="abirmodal(this);"><i class="fa fa-money fa-lg"></i> Pagar Interes</button></td>';
                                }
                                tabla += "</tbody></table>";
                            
                            }else{
                                tabla += "<div>No se encontraron cuotas pendientes ...</div>";
                            }
                            $('#cuotas_pendiente').html(tabla);
                            break;
                        case '3':
                            var length = Object.keys(res).length;
                            tabla = "<label>Amortizar cuotas: </label>";
                            if(length >0){
                                tabla += '<table class="table table-bordered table-striped table-condensed table-hover"><thead>'+
                                '<tr><th>Fecha</th><th>Numero</th><th>Monto S/.</th><th>Mora S/.</th><th>TOTAL</th><th>Operación</th></tr></thead><tbody>';
                                for(i=0;i<length;i++){
                                    tabla += "<tr><td>"+res[i].mes+"-"+res[i].anio+"</td><td>"+res[i].numero_cuota+"</td><td>"+(parseFloat(res[i].parte_capital)+parseFloat(res[i].interes)).toFixed(1)+"</td><td>"+res[i].interes_mora+"</td><td>"+(parseFloat(res[i].parte_capital)+parseFloat(res[i].interes)+parseFloat(res[i].interes_mora)).toFixed(1)+"</td>"+
                                    '<td><button disabled="true" id="btnchek'+i+'" numbtn='+i+' class="btn btn-light btn-sm btnamortizar" marcado="0" monto_cuota="'+(parseFloat(res[i].parte_capital)+parseFloat(res[i].interes))+'" parte_capital="'+res[i].parte_capital+'" anio_mes="'+res[i].anio+'-'+res[i].mes+'" cuota_id="'+res[i].cuota_id+'" onclick ="btnchek(this);"><i class="fa fa-check fa-lg" style="color:white"></i></button></td>';
                                }
                                tabla += "</tbody></table>";
                            
                            }else{
                                tabla += "<div>No se encontraron cuotas pendientes ...</div>";
                            }
                            tabla +='<div><label monto=0 id="lblMonto">Monto total S/.: </label></div>';
                            tabla += '<button class="btn btn-success" onclick="amortisarcuotas();">Amortizar</button>';
                            $('#cuotas_pendiente').html(tabla);
                            if(length > 0){
                                $('#btnchek0').attr('disabled',false);
                            }
                            break;
                        default:
                            
                    }

                    //$('#cuotas_pendiente').html(tabla);
                }
            }).fail(function(){
                mostrarMensaje ("Error de consulta..", "ERROR");
            });
         }
        
     }

    function abirmodal(btn){
        if($('#accioncredito').val() == '1'){
            ruta = "{{  URL::route($ruta['vistapagocuota'], array())}}" + "/"+$(btn).attr('cuota_id')+"/"+"SI/Credito";
        }else{
            ruta = "{{  URL::route($ruta['vistapagocuota'], array())}}" + "/"+$(btn).attr('cuota_id')+"/"+"SI/2";
        }
        
        console.log("RUTA: "+ruta);
        modal(ruta, "Pago de Cuota");
        
        $('#modal'+(contadorModal-1)).on('hidden.bs.modal', function (e) {
            listarcuotasalafecha();
        });
    }

     function realizaoperacion(select){
         var numoperacion = $(select).val();
         console.log("numero: "+numoperacion);
        switch (numoperacion) {
           
            case '1':
            $('#opcion').val(1);
                listarcuotasalafecha();
                break;
            case '2':
            $('#opcion').val(2);
                listarcuotasalafecha();
                break;
            case '3':
            $('#opcion').val(3);
                listarcuotasalafecha();
                break;
            case '4':
            cancelarTodo();
                break;
            case '5':
                ampliar_disminuir_cuotas();
                break;

            default:
                
        }

     }

     function btnchek(btn){
		var anio_mes = $("#anio").val()+"-"+parseInt($("#mes").val());
		if( $(btn).attr("marcado") == '0'){
            $(btn).attr("marcado",'1');
            $(btn).removeClass( "btn-light" ).addClass("btn-primary");
           // if($(btn).attr("numbtn")>0){
                $('#btnchek'+(parseInt($(btn).attr("numbtn")) + 1)).attr('disabled', false);
                $('#btnchek'+(parseInt($(btn).attr("numbtn")) - 1)).attr('disabled', true);
                console.log("anio_mes: "+$(btn).attr('anio_mes')+" = "+anio_mes);
           /* }else{
              
                $('#btnchek'+(parseInt($(btn).attr("numbtn")) + 1)).attr('disabled', false); 
            }*/
            $(btn).removeClass( "btn-light" ).addClass("btn-primary");
            
            if($(btn).attr('anio_mes') == anio_mes){
                var monto = parseFloat($("#montototal").val());
                $("#montototal").val(monto+ parseFloat($(btn).attr('monto_cuota')));
                $('#lblMonto').html("Monto total S/.: "+parseFloat($("#montototal").val()).toFixed(1));
            }else{
                var monto = parseFloat($("#montototal").val());
                $("#montototal").val(monto+ parseFloat($(btn).attr('parte_capital')));
                $('#lblMonto').html("Monto total S/.: "+parseFloat($("#montototal").val()).toFixed(1));
            }
           // $('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"});   
		}else{
            $(btn).attr("marcado",'0');
            $(btn).removeClass( "btn-primary" ).addClass("btn-light");
            
            $('#btnchek'+(parseInt($(btn).attr("numbtn")) + 1)).attr('disabled', true);
            $('#btnchek'+(parseInt($(btn).attr("numbtn")) - 1)).attr('disabled', false);
            console.log("anio_mes: "+$(btn).attr('anio_mes')+" = "+anio_mes);

            if($(btn).attr('anio_mes') == anio_mes){
                var monto = parseFloat($("#montototal").val());
                $("#montototal").val(monto- parseFloat($(btn).attr('monto_cuota')));
                $('#lblMonto').html("Monto total S/.: "+parseFloat($("#montototal").val()).toFixed(1));
            }else{
                var monto = parseFloat($("#montototal").val());
                $("#montototal").val(monto - parseFloat($(btn).attr('parte_capital')));
                $('#lblMonto').html("Monto total S/.: "+parseFloat($("#montototal").val()).toFixed(1));
            }
        }
	}

    function amortisarcuotas(){
        var parametros = $('#formBusquedaCuotas').serialize();
        var i=0;
        var anio_mes = $("#anio").val()+"-"+parseInt($("#mes").val());
        var id_cuotap =0;
        $('.btnamortizar').each(function() {
            if($(this).attr('marcado') == '1'){
                if($(this).attr('anio_mes') == anio_mes){
                    id_cuotap = $(this).attr('cuota_id');
                }
                parametros += "&cuota_id"+i+"="+$(this).attr('cuota_id');
			    i++;
            }
		});
        parametros += "&cuotap="+id_cuotap;
        parametros += "&monto_suma="+parseFloat($("#montototal").val())+"&cantidadmarcados="+i;
        $.ajax({
            url: "creditos/amortizarcuotas",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: parametros,
            beforeSend: function(){
            },
            success: function(res){
                console.log(res);
                if(res[0] == 'OK'){
                    listarcuotasalafecha();
                    buscar("Credito");
                    mostrarMensaje ("Amortizacion realizada", "OK");
                   
                }else{
                    mostrarMensaje(res, 'ERROR');
                }
            }
            }).fail(function(){
                mostrarMensaje ("Error de consulta..", "ERROR");
            });
        
    }

    function cancelarTodo(){
        var parametros = "credito_id={{ $credito->id }}&persona_id={{ $persona->id }}&fechaoperacion="+$('#fechaop').val();
        $.ajax({
            url: "creditos/obtenermontototal",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: parametros,
            beforeSend: function(){
            },
            success: function(res){
                $("#montototal").val(res[0]);
                $("#capital_total").val(res[1]);
                $("#interes_total").val(res[2]);

                var tabla = '<div class="alert alert-success"><label>Total a pagar S/.: '+(parseFloat(res).toFixed(1))+'</label></div>'
                tabla += '<button class="btn btn-primary" onclick="pagar_credito_total();">Pagar Todo</button>';
                $('#cuotas_pendiente').html(tabla);
                
            }
            }).fail(function(){
                mostrarMensaje ("Error de consulta..", "ERROR");
        });
    }
    function pagar_credito_total(){
        var parametros = $('#formBusquedaCuotas').serialize();
        
        var anio_mes = $("#anio").val()+"-"+parseInt($("#mes").val());
        var id_cuotap =0;
       
        parametros += "&monto_suma="+parseFloat($("#montototal").val());
        console.log('Parametros: '+parametros);
        $.ajax({
            url: "creditos/pagarcreditototal",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: parametros,
            beforeSend: function(){
            },
            success: function(res){
                console.log(res);
                if(res == 'OK'){
                    $('#cuotas_pendiente').empty();
                    listarcuotasalafecha();
                    buscar('Credito');
                    mostrarMensaje ("Amortizacion realizada", "OK");

                    $('#cuotas_pendiente').html("<div><h3>Credito Cancelado.</h3></div>");
                    cerrarModal();
                }else{
                    mostrarMensaje(res, 'ERROR');
                }
            }
            }).fail(function(){
                mostrarMensaje ("Error de consulta..", "ERROR");
            });
    }


    function RoundDecimal(numero, decimales) {
		numeroRegexp = new RegExp('\\d\\.(\\d){' + decimales + ',}');   // Expresion regular para numeros con un cierto numero de decimales o mas
		if (numeroRegexp.test(numero)) {         // Ya que el numero tiene el numero de decimales requeridos o mas, se realiza el redondeo
			return Number(numero.toFixed(decimales));
		} else {
			return Number(numero.toFixed(decimales)) === 0 ? 0 : numero;  // En valores muy bajos, se comprueba si el numero es 0 (con el redondeo deseado), si no lo es se devuelve el numero otra vez.
		}
	}

    function ampliar_disminuir_cuotas(){
        var parametros = "fechaop="+$('#fechaop').val()+"&credito_id="+$('#credito_id').val();

        $.ajax({
            url: "creditos/datos_ampliar_reducir_cuotas",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: parametros,
            beforeSend: function(){
            },
            success: function(res){
                    $('#cuotas_pendiente').empty();
                    $('#cuotas_pendiente').html('<div class="">'
                    +'<div class="form-group col-12 col-md-12 col-sm-12">'
                    +'<ul><li>N° Cuotas pendientes: '+res[1]+'</li><li>Saldo deudor: '+res[0]+'</li></ul></div>'
                    +'<div class="form-group col-12 col-md-12 col-sm-12"><label >Nuevo N° cuotas pendientes: *</label>'
                    +'<input type="text" class="form-control input-sm" name="nuevo_numCuotas" id="nuevo_numCuotas" onkeypress="return filterFloat(event,this);"></div>'
                    +'<button class="btn btn-primary" onclick="guardarAmpDisCuotas(this);">Guardar</button></div>');
                 
            }
        }).fail(function(){
                mostrarMensaje ("Error de consulta..", "ERROR");
        });

    }

    function guardarAmpDisCuotas(btn){
        var parametros = "fechaop="+$('#fechaop').val()+"&credito_id="+$('#credito_id').val()+"&nuevo_num_cuotas="+$('#nuevo_numCuotas').val();
        $(btn).button('Loading ...');
        $.ajax({
            url: "creditos/ampliar_reducir_cuotas",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: parametros,
            beforeSend: function(){
            },
            success: function(res){
                if(res == 'OK'){
                    $('#cuotas_pendiente').empty();
                    $('#cuotas_pendiente').html('<div class="alert alert-success">Datos modificados correctamente</div>');
                }else{
                    $('#divMensajeError{{ $entidad_cuota }}').html('<div class="alert alert-danger">'+res+'</div>')
                }
                
                 
            }
        }).fail(function(){
            $(btn).removeClass('disabled');
            $(btn).removeAttr('disabled');
            $(btn).html('Guardar');
                mostrarMensaje ("Error de consulta..", "ERROR");
        });
    }
 </script>