
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
            function calcular_mora($cuota, $fechaFinal){
                $interes_mora =0;
		
                if($cuota->fecha_iniciomora != null){
                    $fecha_fin =null;
                    $fecha_fin= date("Y-m-d", strtotime($fechaFinal));
                    // if($cuota->fecha_pago == null){
                    //     $fecha_fin= date("Y-m-d", strtotime($fecha_actual));
                    // }else{
                    //     $fecha_fin= date("Y-m-d", strtotime($cuota->fecha_pago));
                    // }
                    //******************************************

                    $anio_menor= date("Y", strtotime($cuota->fecha_iniciomora));
                    $mes_menor= date("m", strtotime($cuota->fecha_iniciomora));

                    $anio_mayor= date("Y", strtotime($fecha_fin));
                    $mes_mayor= date("m", strtotime($fecha_fin));
                    $num_meses = 0;
                    if($anio_mayor == $anio_menor){
                        $num_meses = $mes_mayor - $mes_menor;
                    }else if($anio_mayor > $anio_menor){
                        $diferencia_anios = $anio_mayor - $anio_menor;
                        $num_meses = 12 - $mes_menor + (12 * ($diferencia_anios - 1)) + $mes_mayor;
                    }
                    //******************************************
                    
                    if($num_meses>0){
                        // $interes_mora = $num_meses*($cuota->tasa_interes_mora/100) * ($cuota->parte_capital + $cuota->saldo_restante);
                        $interes_mora = $num_meses*($cuota->tasa_interes_mora/100) * ($cuota->saldo_restante);
                    }
                }
                return $interes_mora;

            }

            $contador = 1;
           
            if(!($opcion == "vigentes" || $opcion == "cancelados")){
                $saldo_restante = $credito->valor_credito;
            }
            
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
        @if($opcion == "vigentes")
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>{{  round($saldo_restante,1)}}</td><td>--</td>
        
        </tr>
            @foreach ($lista as $key => $value)
                <?php
                 $value->interes_mora = calcular_mora($value, $fecha_actual);
                    // if($value->estado == 'm'){
                    //     $fecha_inicio = date("Y-m-d", strtotime($value->fecha_iniciomora));
                    //     $fecha_fin = date("Y-m-d", strtotime($fecha_actual));
                    //     $fecha_inicial = new DateTime($fecha_inicio);
                    //     $fecha_final = new DateTime($fecha_fin);
                    //     $diferencia = $fecha_inicial->diff( $fecha_final);
                    //     $numeroDias = $diferencia->format('%R%a días');
                        
                    //     if($numeroDias>0){
                    //         // $interes_ganado = $numeroDias*($value->tasa_interes_mora/100) * ($value->parte_capital + $value->interes);
                    //         // $value->interes_mora += $interes_ganado;
                    //         //********************num meses ***************
                    //         $anio_menor= date("Y", strtotime($fecha_inicio));
                    //         $mes_menor= date("m", strtotime($fecha_inicio));
                    //         $anio_mayor= date("Y", strtotime($fecha_fin));
                    //         $mes_mayor= date("m", strtotime($fecha_fin));
                    //         $num_meses = 0;
                    //         if($anio_mayor == $anio_menor){
                    //             $num_meses = $mes_mayor - $mes_menor;
                    //         }else if($anio_mayor > $anio_menor){
                    //             $diferencia_anios = $anio_mayor - $anio_menor;
                    //             $num_meses = 12 - $mes_menor + (12 * ($diferencia_anios - 1)) + $mes_mayor;
                    //         }
                    //         $value->interes_mora += $value->saldo_restante * $num_meses*($value->tasa_interes_mora/100);
                    //     }
                    // }
                ?>
                <tr>
                    <td>{{  $nombremes[date('m',strtotime($value->fecha_programada_pago))]."-". date('Y',strtotime($value->fecha_programada_pago)) }}</td>
                    <td>{{  $value->numero_cuota}}/{{$credito->periodo}}</td>
                    <td>{{  round($value->interes + $value->parte_capital,1)}}</td>
                    <td>{{  round($value->parte_capital,1)}}</td>
                    <td>{{  round($value->interes,1)}}</td>
                    <td>{{  ($value->mesp != null?($nombremes[$value->mesp]." - ".$value->aniop):"")}}</td>
                    <td>{{  round($value->interes_mora,1)}}</td>
                    <td>{{  round($value->parte_capital + $value->interes + $value->interes_mora,1)}}</td>
                    <td>{{  round($value->saldo_restante,1)}}</td>
                   
                    <td> @if($value->estado == 'I') <button class="btn btn-warning btn-sm"></button>@endif @if($value->estado =='m')<button class="btn btn-danger btn-sm"></button>@endif</td>
                    {{-- <td>{!! Form::button('<i class="fa fa-check fa-lg"></i> Pagar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnpago', 'onclick' => 'modal(\''.URL::route($ruta["vistapagocuota"], array($value->id, 'SI','nan')).'\',  \''.$titulo_pagocuota.'\')')) !!}</td> --}}
                 
                </tr>
                <?php
                $saldo_restante -= $value->parte_capital;
                $contador ++;
                ?>
            @endforeach
        @elseif($opcion == "cancelados")
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>{{  round($saldo_restante,1)}}</td><td>--</td><td></td>
        </tr>
            @foreach ($lista as $key => $value)
                <?php
                    $saldo_restante -= $value->cuota_parte_capital;
                ?>
                <tr>
                   
                    <td>{{  $contador }}</td>
                    <td>{{  explode(':',$value->descripcion)[1]}}/{{$credito->periodo}}</td>
                    <td>{{  round($value->cuota_interes + $value->cuota_parte_capital,1)}}</td>
                    <td>{{  round($value->cuota_parte_capital,1)}}</td>
                    <td>{{  round($value->cuota_interes,1)}}</td>
                    {{-- <td>{{  ($value->fecha != null)?Date::parse($value->fecha)->format('d/m/Y'):"" }}</td> --}}
                    <td>{{  $nombremes[date('m',strtotime($value->fecha))]." - ".date('Y',strtotime($value->fecha))}}</td>
                    <td>{{  round($value->cuota_mora,1)}}</td>
                    <td>{{  round($value->monto,1)}}</td>
                    <td>{{  round($saldo_restante,1)}}</td>
                    <td>{{ $value->descripcion }}</td>
                    <td >{!! Form::button('<i class="fa fa-check fa-lg"></i> Recibo', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnrecibo', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta["generarecibopagocuotaPDF2"], array($value->id)).'\',\''.'1000'.'\',\''.'Voucher de Pago Cuota'.'\')')) !!}</td>
                  
                </tr>
                <?php
                $contador ++;
                ?>
            @endforeach
        @elseif($opcion == "todo")
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>{{  round($saldo_restante,1)}}</td><td>--</td><td></td>
        </tr>
            @foreach ($lista as $key => $value)
                <?php
                if($value->estado != '1'){
                    $valor_mora = 0;
                    $valor_mora = calcular_mora($value, $fecha_actual);
                    $value->interes_mora = $valor_mora;
                }

                    // if($value->estado == 'm'){
                    //     $fecha_inicio = date("Y-m-d", strtotime($value->fecha_iniciomora));
                    //     $fecha_fin = date("Y-m-d", strtotime($fecha_actual));
                    //     $fecha_inicial = new DateTime($fecha_inicio);
                    //     $fecha_final = new DateTime($fecha_fin);
                    //     $diferencia = $fecha_inicial->diff( $fecha_final);
                    //     $numeroDias = $diferencia->format('%R%a días');
                        
                    //     if($numeroDias>0){
                    //         $interes_ganado = $numeroDias*($value->tasa_interes_mora/100) * ($value->parte_capital + $value->interes);
                    //         $value->interes_mora += $interes_ganado;
                    //         //********************num meses ***************
                    //         $anio_menor= date("Y", strtotime($fecha_inicio));
                    //         $mes_menor= date("m", strtotime($fecha_inicio));
                    //         $anio_mayor= date("Y", strtotime($fecha_fin));
                    //         $mes_mayor= date("m", strtotime($fecha_fin));
                    //         $num_meses = 0;
                    //         if($anio_mayor == $anio_menor){
                    //             $num_meses = $mes_mayor - $mes_menor;
                    //         }else if($anio_mayor > $anio_menor){
                    //             $diferencia_anios = $anio_mayor - $anio_menor;
                    //             $num_meses = 12 - $mes_menor + (12 * ($diferencia_anios - 1)) + $mes_mayor;
                    //         }
                    //         $value->interes += $value->interes * $num_meses;
                    //     }
                    // }
                ?>
                <tr>
                
                    <td>{{  $contador }}</td>
                    <td>{{  $value->numero_cuota}}/{{$credito->periodo}}</td>
                    <td>{{  round($value->interes + $value->parte_capital,1)}}</td>
                    <td>{{  round($value->parte_capital,1)}}</td>
                    <td>{{  round($value->interes,1)}}</td>
                    <td>{{  $nombremes[$value->mes]." - ".$value->anio }}</td>
                    <td>{{  round($value->interes_mora,1)}}</td>
                    <td>{{  round($value->interes + $value->parte_capital + $value->interes_mora,1)}}</td>
                    <td>{{  round($value->saldo_restante,1)}}</td>
                    @if($value->estado == 'm')
                        <td><button class="btn btn-danger btn-sm"></button></td>
                    @elseif($value->fecha_iniciomora != null)
                        @if($value->fecha_pago == null)
                        <td><button class="btn btn-danger btn-sm"></button></td>
                        @else
                        <td>P<button class="btn btn-danger btn-sm"></button></td>
                        @endif
                    @elseif($value->estado == '1')
                        <td>P</td>
                    @else
                        <td></td>
                    @endif
                    <td></td>
                </tr>
                <?php
                $contador ++;
                ?>
            @endforeach
        @endif
       
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