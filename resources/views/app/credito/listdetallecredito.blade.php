
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
            $nombremes = array('1'=>'Ene',
            '1'=>'Ene',
            '01'=>'Ene',
            '2'=>'Feb',
            '02'=>'Feb',
            '3'=>'Mar',
            '03'=>'Mar',
            '4'=>'Abr',
            '04'=>'Abr',
            '5'=>'May',
            '05'=>'May',
            '6'=>'Jun',
            '06'=>'Jun',
            '7'=>'Jul',
            '07'=>'Jul',
            '8'=>'Ago',
            '08'=>'Ago',
            '9'=>'Sep',
            '09'=>'Sep',
            '10'=>'Oct',
            '11'=>'Nov',
            '12'=>'Dic',);
        ?>
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>{{  round($saldo_restante,1)}}</td><td>--</td><td></td><td></td>
        </tr>
        @foreach ($lista as $key => $value)
            
            <tr>
                <td>{{  $nombremes[date('m',strtotime($value->fecha_programada_pago))]."-". date('Y',strtotime($value->fecha_programada_pago)) }}</td>
                <td>{{$value->numero_cuota}}/{{$credito->periodo}}</td>
                <td>{{  round($value->interes + $value->parte_capital,1)}}</td>
                <td>{{  round($value->parte_capital,1)}}</td>
                <td>{{  round($value->interes,1)}}</td>
                <td>{{  ($value->fecha_pago != null)?Date::parse($value->fecha_pago)->format('d/m/Y'):"" }}</td>
                <td>{{  round($value->interes_mora,1)}}</td>
                <td>{{  round($value->parte_capital + $value->interes + $value->interes_mora,1)}}</td>
                <td>{{  round($value->saldo_restante,1)}}</td>
                @if($value->estado == '1')
                <td>P  @if($value->interes_mora != 0) <button class="btn btn-danger btn-sm"></button>@endif</td>
                <td >{!! Form::button('<i class="fa fa-check fa-lg"></i> Pagado', array('class' => 'btn btn-light btn-xs', 'id' => 'btnGuardar', 'onclick' => '')) !!}</td>
                {{-- <td >{!! Form::button('<i class="fa fa-check fa-lg"></i> Recibo', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnrecibo', 'onclick' => 'generaRecibo(\''.URL::route($ruta["generarecibopagocuotaPDF"], array($value->id)).'\')')) !!}</td>--}}
                <td >{!! Form::button('<i class="fa fa-check fa-lg"></i> Recibo', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnrecibo', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta["generarecibopagocuotaPDF"], array($value->id)).'\',\''.'1000'.'\',\''.'Voucher de Pago Cuota'.'\')')) !!}</td>
                @else
                <td> @if($value->estado == 'I')P <button class="btn btn-warning btn-sm"></button>@endif</td>
                <td>{!! Form::button('<i class="fa fa-check fa-lg"></i> Pagar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnpago', 'onclick' => 'modal(\''.URL::route($ruta["vistapagocuota"], array($value->id, 'SI','nan')).'\',  \''.$titulo_pagocuota.'\')')) !!}</td>
                <td>{!! Form::button('<i class=""></i> ......', array('class' => 'btn btn-light btn-xs', 'id' => '', 'onclick' => '')) !!}</td>
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