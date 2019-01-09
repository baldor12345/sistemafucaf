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
        <td>{!! Form::select('asistencia'.$value->id, $cboAsistencia, $value->asistencia, array('class' => 'form-control input-xs', 'id' => 'asistencia'.$value->id, 'onchange' => 'cambiartardanza('. $value->id .');')) !!}</td>
        
        <?php if($value->estado == 'A'){?>
            <td style='color:green;font-weight: bold;'>-</td>
        <?php } if($value->estado == 'P'){?>
            <td style='color:green;font-weight: bold;'>Pagó</td>
        <?php } if($value->estado == 'N'){?>
            <td style='color:red;font-weight: bold;'>No Pagó</td>
        <?php }?>

        <?php if($value->asistencia != 'A'){ if($value->estado == 'N'){?>
            <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'modal (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', this);','class' => 'btn btn-xs btn-warning')) !!}</td>
        <?php } if($value->estado == 'P'){?>
        <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'modal (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', this);','class' => 'btn btn-xs btn-warning', 'disabled')) !!}</td>
        <?php }}else{ if($value->asistencia == 'A'){?>
        <td>{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'modal (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', this);','class' => 'btn btn-xs btn-warning', 'disabled')) !!}</td>
        <?php }}?>
        
        
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
        var asistencia = $('#asistencia' + idpersona).val();
        $.ajax({
            url: 'persona/cambiartardanza?idpersona='+idpersona+"&asistencia="+asistencia,
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
    function evaluartardanza(){
        console.log("fue precionado el valor "+ $('#tardanza').val());
    }
</script>