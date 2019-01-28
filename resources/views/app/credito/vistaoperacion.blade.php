<script type="text/javascript">
    // var rutareportecuotas = "{{ URL::route($ruta['generareportecuotasPDF'], array())}}";
    // function imprimirpdf(){
    //     window.open(rutareportecuotas+"/{{ $credito->id }}", "Cuotas de Credito", "width=700, height=800, left=50, top=20");
    //}
 </script>
 <div id="divMensajeError{!! $entidad_cuota !!}"></div>
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
                     
					<div class="form-group">
						{!! Form::label('anio', 'Año:', array('class' => 'input-sm')) !!}
						{!! Form::select('anio', $anios, $anioactual, array('class' => 'form-control input-sm', 'id' => 'anio')) !!}
					</div>
					<div class="form-group">
						{!! Form::label('mes', 'Mes:', array('class' => 'input-sm')) !!}
						{!! Form::select('mes', $meses, $mesactual, array('class' => 'form-control input-sm', 'id' => 'mes','onchange'=>'listarcuotasalafecha()')) !!}
					</div>
                     {!! Form::close() !!}
                 </div>

             </div>
             <div class="form-group col-12 col-md-12 col-sm-12">
                {!! Form::label('accioncredito', 'Operación a realizar: ', array('class' => '')) !!}
                {!! Form::select('accioncredito', $cboacciones, 0, array('class' => 'form-control input-sm', 'id' => 'accioncredito',  'onchange' => 'realizaoperacion(this)')) !!}
             </div>
             <div id="cuotas_pendiente"></div>
             
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
         $('.crbox').css({'padding':'0px 15px 0px 15px','margin':'10px 0px 0px 10px'});
         $('.contbtn').css({'padding': '10px 0'});
        // buscar('{{ $entidad_cuota }}');
         init(IDFORMBUSQUEDA+'{{ $entidad_cuota }}', 'B', '{{ $entidad_cuota }}');
         listarcuotasalafecha();
         
     });
     function listarcuotasalafecha(){
        $.ajax({
            url: "creditos/cuotasalafecha",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: $('#formBusquedaCuotas').serialize(),
            beforeSend: function(){
            },
            success: function(res){
                var length = Object.keys(res).length;
                
                var tabla = "<label>Cuotas a pagar a la fecha: </label>";
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
            }
        }).fail(function(){
            mostrarMensaje ("Error de consulta..", "ERROR");
        });
     }

    function abirmodal(btn){
        ruta = "{{  URL::route($ruta['vistapagocuota'], array())}}" + "/"+$(btn).attr('cuota_id')+"/"+"'NO'/'ReciboCuota'";
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
            
               

                break;
            case 2:
                
                break;
            case 3:
                
                break;
            case 4:
               
                break;
            case 5:
                
                break;

            default:
                
        }
     }
   
 </script>