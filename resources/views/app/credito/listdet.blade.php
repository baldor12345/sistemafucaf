

@if(count($lista) == 0)
    <h3 class="text-warning">No se encontraron resultados.</h3>
@else
    {!! $paginacion or '' !!}
    <script type="text/javascript">
        var rutarecibopagocuota = "{{ URL::route($ruta['generarecibopagocuotaPDF'], array())}}";
        var rutareportecuotas = "{{ URL::route($ruta['generareportecuotasPDF'], array())}}";
        function imprimirpdf(){
            window.open(rutareportecuotas+"/"+"{{ $credito->credito_id }}", "Cuotas de Credito", "width=700, height=800, left=50, top=20");
        }
        console.log('RUTA: '+rutarecibopagocuota);
   </script>
   <div class="form-group">
   {!! Form::button('<i class="fa fa-check fa-lg"></i> Imprimir PDF', array('class' => 'btn btn-success btn-sm', 'id' => 'btnImprimirpdf', 'onclick' => 'imprimirpdf();')) !!}
   </div>
   <table id="example1" class="table table-bordered table-striped table-condensed table-hover">
        <thead>
            <tr>
                @foreach($cabecera as $key => $value)
                    <th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif> {!! $value['valor'] !!}</th>
                @endforeach
            </tr>
        </thead>
    
        <tbody>
            <?php
                $contador = 1;
                $saldo_restante = $credito->valor_credito;
            ?>
            <tr>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>{{$saldo_restante}}</td><td>--</td><td></td><td></td>
            </tr>
            @foreach ($lista as $key => $value)
                <tr>
                    <td>{{ Date::parse($value->fecha_programada_pago)->format('d/m/Y')}}</td>
                    <td>{{$value->numero_cuota}}/{{$credito->periodo}}</td>
                    <td>{{$value->interes + $value->parte_capital}}</td>
                    <td>{{$value->parte_capital}}</td>
                    <td>{{$value->interes}}</td>
                    <td>{{($value->fecha_pago != null)?Date::parse($value->fecha_pago)->format('d/m/Y'):""}}</td>
                    <td>{{$value->interes_mora}}</td>
                    <td>{{$value->parte_capital + $value->interes + $value->interes_mora}}</td>
                    <td>{{$value->saldo_restante}}</td>
                    @if($value->estado != 0 )
                    <td >P</td>
                    <td ><button type="button" idcuota=0 class='btn btn-light btn-xs'>Pagado</button></td>
                    <td ><button type="button" id='recbtnct{{$value->id}}' idcuota='{{$value->id}}' class='btn btn-success btn-xs btnrecibo'>Recibo</button></td>
                    @else
                    <td ></td>
                    <td >{!! Form::button('Pagar ', array('class' => 'btn  btn-danger btn-xs btncuota','id'=>'btnct'.$value->id, "idcuota"=>''.$value->id)) !!}</td>
                    <td ><button type="button" id='recbtnct{{$value->id}}' idcuota='{{$value->id}}' class='btn btn-light btn-xs'>. . . . . .</button></td>
                    @endif
                </tr>
                <?php
                $saldo_restante -= $value->parte_capital;
                    $contador ++;
                ?>
            @endforeach
        </tbody>
        
        <tfoot>
        </tfoot>
    </table>

    <script type="text/javascript">
	$(document).ready(function() {
        $('.pagination').css({
            'padding':'0px',
            'margin':'3px 0px'
        });
    });

function generar_recibo(nombrecli,codcli, direccioncli, fechafincuota,fechainicuota, numerocuota, partecapital, interes, interesmora, saldorestante, cuotasproximos, mes, fechacred, fechaactual,valorcuota){
    var recibo = "<div class='card-box table-responsive' style='padding-top: 0px; margin: 15px 0px;'>"+
    "<div class = 'row'>"+
        "<div class ='col col-md-8'>"+
            "<div class='row' style='padding: 0px; margin: 0px;'>"+
                "<h4 style='padding: 0px; margin: 0px;'>ESTADO DE CUENTA FUCAF</h4>"+
                "<p style='padding: 0px; margin: 0px;'>"+nombrecli+"</p>"+
                "<p style='padding: 0px; margin: 0px;'>"+direccioncli+"</p>"+
            "</div>"+
            "<div class='row' style='padding-top: 20px;'>"+
                "<div class='col col-md-4'  style='padding:0px; margin: 0px 0px;'>"+
                    "<h4>LINEA DE CREDITO</h4>"+
                    "<h4>TOTAL</h4>"+
                "</div>"+
                "<div class='col col-md-8' style='margin-right: 0px; padding-right: 3px;'>"+
                    "<ul style='padding: 0px; margin: 15px 0px;'>"+
                        "<li type='disc'>Incluye capital, inters, gastos de cuotas atrasadas</li>"+
                        "<li type='disc'>Línea de crédito para disposicion de efectivo</li>"+
                        "<li type='disc'>No incluye cuotas por vencer</li>"+
                        "<li type='disc'>Se podrá modificar por endeudamiento, comportamiento</li>"+
                    "</ul>"+
                "</div>"+
            "</div>"+
        "</div>"+
        "<div class ='col col-md-4' style='padding: 0px; margin: 15px 0px;'>"+
            "<table class='table table-bordered table-xs'>"+
                "<tr><td>Cod. Cliente FUCAF</td><td>"+codcli+"</td></tr>"+
                "<tr><td>Último dia de pago</td><td>"+fechafincuota+"</td></tr>"+
                "<tr><td>Periodo</td><td>DEL "+fechainicuota+" AL "+fechafincuota+"</td></tr>"+
                "<tr><td>Mes</td><td>"+mes+"</td></tr>"+
                ""+
            "</table>"+
        "</div>"+
    "</div>"+
    "<div class='row'>"+
        "<table class='table table-bordered table-sm'>"+
            "<thead>"+
                "<tr><th rowspan='2'>FECH. TRANSACCION</th><th rowspan='2'>FECHA PROCESO</th><th rowspan='2'>DESCRIPCION</th><th rowspan='2'>ESTABLECIMIENTO</th><th rowspan='2'>PAIS</th><th rowspan='2'>NRO CUOTA CARGADA</th><th colspan='2'>VALOR CUOTA (S/.)</th><th rowspan='2'>CARGO / ABONO (S/.)</th></tr>"+
                "<tr><th>CAPITAL</th><th>INTERES</th></tr>"+
            "</thead>"+
            "<tbody>"+
                "<tr><td>"+fechacred+"</td><td>"+fechaactual+"</td><td>PRESTAMO EFECTIVO</td><td>Local FUCAF</td><td>PERU</td><td>"+numerocuota+"</td><td>"+partecapital+"</td><td>"+interes+"</td><td>"+valorcuota+"</td></tr>"+
                "<tr><td>"+fechaactual+"</td><td>"+fechaactual+"</td><td>COMISIÓN POR RECIBO DE PAGO</td><td>PERU</td><td></td><td></td><td></td><td></td><td>0.2</td></tr>"+
                "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>"+
                "<tr><td></td><td></td><td>TEA CUOTA 30%</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>"+
                "<tr><td></td><td></td><td>TEA MORATORIO 36%</td><td></td><td></td><td></td><td></td><td></td><td></td></tr>"+
            "</tbody>"+
        "</table>"+
    "</div>"+
    "<div><button type='button' class='btn btn-danger' id='btnclosemdrecibo'>Cerrar</button></div>"+
"</div>";
    return recibo;
}
function abrirmodal(accion, msrecibo){
    var boxmodal = bootbox.dialog({
                            title: 'Estado de cuenta',
                            className: 'modalrecibo',
                            message: ''+msrecibo,
                            closeButton: true
                        });
        boxmodal.prop('id', 'modalrecibo');
        boxmodal.modal(accion);
        $('#btnclosemdrecibo').click(function(){
            boxmodal.modal("hide");
            $('#modal'+(contadorModal - 1)).css({
                                "overflow-y": "scroll"
                            }); 
        });
        $("#modalrecibo").on('hidden.bs.modal', function () {
            $('#modal'+(contadorModal - 1)).css({
                                "overflow-y": "scroll"
                            });   
        });
        var divModal = '.modalrecibo';
		$(divModal).children('.modal-dialog').css('width','auto');
		$(divModal).children('.modal-dialog').css('max-width', '1000px');
		$(divModal).children('.modal-dialog').css('margin-left','auto');
		$(divModal).children('.modal-dialog').css('margin-right','auto');
        
}
    $('.btnrecibo').each(function(){
        $(this).click(function(){
           var id_cuota = $(this).attr('idcuota');
            var array_tr =  this.parentElement.parentElement.children;
            var msrecibo = generar_recibo('{{$credito->nombres}}','fucaf001','direccion','fechafin','fechaini','nuemroucota','partecap','interes','interesmora','saldorest','cuotasprox','Diciembre-18');
            //abrirmodal("show", msrecibo);
            window.open(rutarecibopagocuota+"/"+id_cuota, "Voucher credito", "width=700, height=500, left=50, top=20");
                                            
        });
    });

    $('.btncuota').each(function (){

            var idbt = $(this).attr('id');
			 $(this).click(function (){
                var array_tr =  this.parentElement.parentElement.children;
                console.log("fila: "+array_tr[3].innerText);
                var msje ="", textbtnOk="",textbtnCancel="";;
                if({{$idcaja}} !=0){
                    msje = "<div class='alert alert-success'> ¿Esta seguro de realizar el pago? </div>";
                    textbtnOk = "<i class='fa fa-times'></i> Cancelar";
                    textbtnCancel = "<i class='fa fa-check'></i>Confirmar";
                }else{
                    msje = "<div class='alert alert-danger'><strong>¡Error!</strong> No hay una caja aperturada, porfavor aperture primero.</div>";
                    textbtnOk = "---";
                    textbtnCancel = "Ok";
                }
                var idcuota = $(this).attr("idcuota");
                if($('#'+idbt).attr("idcuota") != 0){
                    bootbox.confirm({
                        title: "Pago de cuota",
                        message: ""+msje,
                        buttons: {
                            cancel: {
                                label: ''+textbtnOk
                            },
                            confirm: {
                                label: ''+textbtnCancel
                            }
                        },
                        callback: function (result) { //
                            if(result & {{$idcaja}} != 0){
                                
                                $.ajax({
                                    url: 'creditos/pagarcuota',
                                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                    type: 'GET',
                                    data: 'id_cuota='+idcuota+'&id_cliente={{$credito->persona_id}}&id_caja={{$idcaja}}&id_crd={{$idcredito}}',
                                    beforeSend: function(){
                                        
                                    },
                                    success: function(res){
                                        console.log("respuesta: "+res);
                                       if(res == "OK"){
                                            array_tr[9].innerHTML = 'P';
                                            array_tr[5].innerHTML = '{{date('d/m/Y')}}';
                                            buscar('{{ $entidad1 }}');
                                            console.log("idbt: "+idbt);
                                            $('#'+idbt).html("Pagado");
                                            $('#'+idbt).addClass( "btn-light" );
                                            $('#rec'+idbt).html("Recibo");
                                            $('#'+idbt).attr("idcuota", 0);
                                            $('#'+idbt).removeClass("btn-danger btncuota");
                                            $('#rec'+idbt).addClass( "btnrecibo btn-success" );
                                            $('#rec'+idbt).click(function(){
                                                var array_tr =  this.parentElement.parentElement.children;
                                                var msrecibo = generar_recibo('{{$credito->nombres}}','fucaf001','direccion','fechafin','fechaini','nuemroucota','partecap','interes','interesmora','saldorest','cuotasprox','Diciembre-18');
                                                //abrirmodal("show", msrecibo);
                                                window.open(rutarecibopagocuota+"/"+idcuota, "Voucher credito", "width=700, height=500, left=50, top=20");
                                            });
                                            window.open(rutarecibopagocuota+"/"+idcuota, "Voucher credito", "width=700, height=500, left=50, top=20");
                                        }else{
                                            mostrarMensaje(res, 'ERROR');
                                        }
                                    }
                                }).fail(function(){
                                    
                                });
                            }
                            $('#modal'+(contadorModal - 1)).css({
                                "overflow-y": "scroll"
                            });  
                         
                        }
                    });
                }
			 });
         });

</script>
@endif