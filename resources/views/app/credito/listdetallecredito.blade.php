
@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}

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
                <td >{!! Form::button('<i class="fa fa-check fa-lg"></i> Pagado', array('class' => 'btn btn-light btn-sm', 'id' => 'btnGuardar', 'onclick' => '')) !!}</td>
                {{--  <td >{!! Form::button('<i class="fa fa-check fa-lg"></i> Recibo', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnrecibo', 'onclick' => 'generaRecibo(\''.URL::route($ruta["generarecibopagocuotaPDF"], array($value->id)).'\')')) !!}</td>--}}
                <td >{!! Form::button('<i class="fa fa-check fa-lg"></i> Recibo', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnrecibo', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta["generarecibopagocuotaPDF"], array($value->id)).'\',\''.'1000'.'\',\''.'Voucher de Pago Cuota'.'\')')) !!}</td>
                
                @else
                <td ></td>
                <td >{!! Form::button('<i class="fa fa-check fa-lg"></i> Pagar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnpago', 'onclick' => 'modal(\''.URL::route($ruta["vistapagocuota"], array($value->id, 'SI')).'\',  \''.$titulo_pagocuota.'\')')) !!}</td>
                <td >{!! Form::button('<i class=""></i> ......', array('class' => 'btn btn-light btn-sm', 'id' => '', 'onclick' => '')) !!}</td>
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

    function generaRecibo(rutapago){
        window.open(rutapago, "Voucher credito", "width=700, height=500, left=50, top=20");
    }
    
</script>
@endif