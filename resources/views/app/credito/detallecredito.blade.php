
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($credito, array('class' => 'form-horizontal' , 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}

    <div class="form-row">
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Cliente: '.$credito->nombres.' '.$credito->apellidos, array('id'=>'cliente','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Monto S/.: '.$credito->valor_credito, array('id'=>'montocredito','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Interes mensual (%): '.$credito->tasa_interes, array('id'=>'interesmes','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Tiempo (Meses): '.$credito->periodo, array('id'=>'tiempomeses','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'N° de cuotas de pago: '.$credito->periodo, array('id'=>'numcuotaspago','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Fecha de inicio: '.$credito->fechai, array('id'=>'fechainicio','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Fecha de caducidad: '.$fechacaducidad, array('id'=>'fechacaduca','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-12">
            {!! Form::label('', 'Descripcion: '.$credito->descripcion, array('id'=>'descripcredito','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-12">
            <h3>Cuotas de pago</h3>
        </div>
    </div>

    <table id="example1" class="table table-striped table-bordered table-sm table-fixed">
        <thead style="width: 99%; display: block;">
            <tr style="width: 99%; display: block;">
                <th style='width: 15%;;' class='text-center'>#</th>
                <th style='width: 15%;'>INTERES</th>
                <th style='width: 15%;' class='text-center'>CAPITAL</th>
                <th style='width: 15%;' class='text-center'>CUOTA S/.</th>
                <th style='width: 15%;' class='text-center'>FECHA DE PAGO</th>
                <th style='width: 15%;' class='text-center'>SITUACION</th>
                <th style='width: 10%;' colspan="1">OPERACIONES</th>
            </tr>
        </thead>
    
        <tbody  style="width: 100%; height: 200px; overflow-y: scroll; display: block;">
            <?php
                $contador = 1;
            ?>
            @foreach ($lista as $key => $value)
                <tr>
                    <td style='width: 15%;'>{{$contador}}</td>
                    <td style='width: 15%;'>{{$value->interes}}</td>
                    <td style='width: 15%;'>{{$value->parte_capital}}</td>
                    <td style='width: 15%;'>{{$value->interes + $value->parte_capital}}</td>
                    <td style='width: 15%;'>{{$value->fecha_programada_pago}}</td>
                    @if($value->estado != 0 )
                    <td style='width: 15%;'>Pagado</td>
                    <td style='width: 10%;'><button type="button" idcuota=0 class='btn btn-light' idevento='{{$value->id}}'>Cancelado</button></td>
                    @else
                    <td style='width: 15%;'>Pendiente</td>
                    <td style='width: 10%;'>{!! Form::button('Pagar ', array('class' => 'btn  btn-danger btncuota','id'=>''.$value->id, "idcuota"=>$value->id)) !!}</td>
                    @endif
                </tr>
                <?php
                    $contador ++;
                ?>
            @endforeach
        </tbody>
        
        <tfoot>
            
        </tfoot>
    </table>
    <div class="form-group">
        <div class="col-lg-12 col-md-12 col-sm-12 text-right">
            &nbsp;
            {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
        </div>
    </div>
{!! Form::close() !!}
<style type="text/css">

</style>

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('750');
    });
    $('.btncuota').each(function (){
            var idbt = $(this).attr('id');
			 $(this).click(function (){
                var array_tr =  this.parentElement.parentElement.children;
                console.log("fila: "+array_tr[3].innerText);
                if($('#'+idbt).attr("idcuota") != 0){
                $.ajax({
                    url: 'creditos/pagarcuota',
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    type: 'GET',
                    data: 'id_cuota='+$(this).attr('idcuota')+"&id_cliente={{$credito->persona_id}}",
                    beforeSend: function(){
                        
                    },
                    success: function(res){
                        array_tr[5].innerHTML = 'Pagado';
                        buscar('{{ $entidad }}');
                        $('#'+idbt).html("Cancelado");
                        $('#'+idbt).attr("idcuota", 0);
                        $('#'+idbt).removeClass("btn-danger btncuota");
                        $('#'+idbt).addClass( "btn-light" );
                    }
                }).fail(function(){
                    
                });
            }
			 });
         });

</script>