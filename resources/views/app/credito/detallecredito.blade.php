
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

    <table id="example1" class="table table-bordered table-striped table-condensed table-hover">
        <thead>
            <tr>
                <th style='width: 5%' class='text-center'>#</th>
                <th style=''>INTERES</th>
                <th class='text-center'>CAPITAL</th>
                <th class='text-center'>CUOTA S/.</th>
                <th class='text-center'>FECHA DE PAGO</th>
                <th class='text-center'>SITUACION</th>
                <th colspan="1">OPERACIONES</th>
            </tr>
        </thead>
        <tbody>
            <?php
                $contador = 1;
            ?>
            @foreach ($lista as $key => $value)
                <tr>
                    <td>{{$contador}}</td>
                    <td>{{$value->interes}}</td>
                    <td>{{$value->parte_capital}}</td>
                    <td>{{$value->interes + $value->parte_capital}}</td>
                    <td>{{$value->fecha_programada_pago}}</td>
                    @if($value->estado != 0)
                    <td>Pagado</td>
                    <td><button type="button" class='btn btn-light' idevento='{{$value->id}}'>Cancelado</button></td>
                    @else
                    <td>Pendiente</td>
                    <td><button type="button" class='btnpagar btn btn-danger' idevento='{{$value->id}}'>Pagar</button></td>
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
            {!! Form::button('<i class="fa fa-check fa-lg"></i> boton', array('class' => 'btn btn-success btn-sm', 'id' => '', 'onclick' => '#')) !!}
            &nbsp;
            {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
        </div>
    </div>
{!! Form::close() !!}


<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('650');
    });

</script>