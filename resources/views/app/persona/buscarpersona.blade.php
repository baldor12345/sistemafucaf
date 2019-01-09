@if(count($lista) == 0)
<h3 class="text-warning">No se encontraron resultados.</h3>
@else
{!! $paginacion or '' !!}
<table id="example1" class="table table-bordered table-striped table-condensed table-hover">
<thead>
    <tr>
        @foreach($cabecera as $key => $value)
        <th @if((int)$value['numero'] > 1) colspan="{{ $value['numero'] }}" @endif>{!! $value['valor'] !!}</th>
        @endforeach
    </tr>
</thead>
<tbody>
    <?php
        $contador = $inicio + 1;
    ?>
    @foreach ($lista as $key => $value)
    <tr>
        <td>{{ $contador }}</td>
        <td>{{ $value->persona->nombres.' '.$value->persona->apellidos }} </td>
        @if($value->tardanza ==1)
        <td tardanza='{{ $value->tardanza }}'>{!! Form::checkbox('cambiartardanza', $value->tardanza, true, array('class' => 'custom-control-input', 'id' => 'cambiartardanza', 'onchange' => 'cambiartardanza('. $value->id .');','onclick'=>'evaluartardanza();')) !!}</td>
        @else
        <td tardanza='{{ $value->tardanza }}'>{!! Form::checkbox('cambiartardanza', $value->tardanza, false, array('class' => 'custom-control-input', 'id' => 'cambiartardanza', 'onchange' => 'cambiartardanza('. $value->id .');','onclick'=>'evaluartardanza();')) !!}</td>
        @endif
        @if($value->inasistencia ==1)
        <td inasistencia='{{ $value->inasistencia }}' >{!! Form::checkbox('cambiarfalta', $value->inasistencia, true, array('class' => 'custom-control-input', 'id' => 'cambiarfalta', 'onchange' => 'cambiarfalta('. $value->id .');')) !!}</td>
        @else
        <td inasistencia='{{ $value->inasistencia }}' >{!! Form::checkbox('cambiarfalta', $value->inasistencia, false, array('class' => 'custom-control-input', 'id' => 'cambiarfalta', 'onchange' => 'cambiarfalta('. $value->id .');')) !!}</td>
        @endif

        <?php 
            $inasistencia  = $value->estado;
            if($inasistencia === 'A' ){
                echo "<td style='color:green;font-weight: bold;'>-</td>";
            }elseif($inasistencia === 'N' ){
                echo "<td style='color:red;font-weight: bold;'>No Pagó</td>";
            }elseif($inasistencia === 'P'){
                echo "<td style='color:green;font-weight: bold;'>Pagó</td>";
            }
        ?>
        <?php if($value->inasistencia == 1){ if($value->estado === 'P'){ ?>
            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'modal (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', this);','class' => 'btn btn-xs btn-warning', 'disabled')) !!}</td>
        <?php }elseif($value->estado === 'N'){ ?>
            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'modal (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', this);','class' => 'btn btn-xs btn-warning')) !!}</td>
        <?php }else{ ?>
            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'modal (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', this);','class' => 'btn btn-xs btn-warning', 'disabled')) !!}</td>
        <?php }}?>
        <?php if($value->inasistencia == 2){ ?>
            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'modal (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', this);','class' => 'btn btn-xs btn-warning', 'disabled')) !!}</td>
        <?php }?>

    </tr>
    <?php
        $contador = $contador + 1;
    ?>
    @endforeach
</tbody>
</table>
@endif
<script>
    function cambiartardanza(idpersona) {
        $.ajax({
            url: 'persona/cambiartardanza?idpersona=' + idpersona ,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            beforeSend: function(){
                
            },
            success: function(res){
                mostrarMensaje ("Tardanza Aplicada!", "OK");
                buscar("{{$entidad}}");
            }
        }).fail(function(){
            alert('Ocurrió un error');
        });
    }

    function cambiarfalta(idpersona) {
        $.ajax({
            url: 'persona/cambiarfalta?idpersona=' + idpersona,
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            beforeSend: function(){
                
            },
            success: function(res){
                mostrarMensaje ("Falta Aplicada!", "OK");
                buscar("{{$entidad}}");
            }
        }).fail(function(){
            alert('Ocurrió un error');
        });
    }

    function evaluartardanza(){
        console.log("fue precionado el valor "+ $('#tardanza').val());
    }
</script>