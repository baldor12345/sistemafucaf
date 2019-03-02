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
    @if($value->estado == 'N')
    <tr>
        <td>{{ $contador }}</td>
        <td style="font-size:12px;">{{ $value->persona->codigo}}</td>
        <td style="font-size:12px;">{{ $value->persona->apellidos.'  '.$value->persona->nombres }} </td>
        <td style="font-size:12px;">{{ Date::parse($value->fecha )->format('d').' de '.$Month[intval(Date::parse($value->fecha)->format('m'))].' del '.Date::parse($value->fecha )->format('Y') }} </td>
        <?php
            $cboasist = array();
            if($value->asistencia == 'T'){
                $cboasist['T'] = 'Tardanza';
                $cboasist['J'] = 'Tardanza Justificada';
            }else{
                $cboasist['F'] = 'Falta';
                $cboasist['J'] = 'Falta Justificada';
            }
        ?>
        @if($value->asistencia != 'J')
        <td style="font-size:12px;">{!! Form::select('asistencia'.$value->id, $cboasist, $value->asistencia, array('class' => 'form-control input-xs', 'style'=>'font-size:12px', 'id' => 'asistencia'.$value->id, 'onchange' => 'modalJustificarFalta(\''.URL::route($ruta["cargarjustificar"], array($value->id, 'listar'=>'SI', 'indiceCbo'=>''.$value->asistencia)).'\', \''.$titulo_justificar.'\',this);')) !!}</td>
        @else
        <td style="font-size:12px;">{!! Form::select('asistencia'.$value->id, $cboasist, $value->asistencia, array('class' => 'form-control input-xs', 'style'=>'font-size:12px', 'id' => 'asistencia'.$value->id, 'onchange' => 'modalJustificarFalta(\''.URL::route($ruta["cargarjustificar"], array($value->id, 'listar'=>'SI', 'indiceCbo'=>''.$value->asistencia)).'\', \''.$titulo_justificar.'\',this);','disabled')) !!}</td>
        @endif
        @if($value->asistencia != 'J')
        <td style='color:red;font-weight: bold; font-size:12px' >No Pagó</td>
        @else
        <td style='color:green;font-weight: bold; font-size:12px' >Justificada</td>
        @endif

        @if($value->asistencia != 'J')
        <td style="font-size:12px;">{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'abrirmodalpagomulta (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', \''.$idCaja.'\');','class' => 'btn btn-xs btn-warning')) !!}</td>
        @else
        <td style="font-size:12px;">{!! Form::button('<div class="glyphicon glyphicon-pencil"></div> Pagar Multa', array('onclick' => 'abrirmodalpagomulta (\''.URL::route($ruta["cargarpagarmulta"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', \''.$idCaja.'\');','class' => 'btn btn-xs btn-warning', 'disabled')) !!}</td>
        @endif
        
    </tr>
 @endif
    <?php
        $contador = $contador + 1;
    ?>
    @endforeach
</tbody>
</table>
@endif
<script>

    function modalJustificarFalta(ruta,tituloModal, comboSelet){
        if($(comboSelet).val() == 'J'){
            modal(ruta, tituloModal);
        }
    }
    /*
    function cambiartardanza(idpersona) {
        bootbox.confirm("Por favor tenga la amabilidad de confirmar la justificacion de la asistencia, Gracias!", function(result){ 
            if(result){
                var asistencia = $('#asistencia' + idpersona).val();
    
                $.ajax({
                    url: 'controlpersona/cambiartardanza?idpersona='+idpersona+"&asistencia="+asistencia,
                    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    type: 'GET',
                    beforeSend: function(){
                        
                    },
                    success: function(res){
                        mostrarMensaje ("Asistencia!", "OK");
                        buscar("{{$entidad}}");
                    }
                }).fail(function(){
                    alert('Ocurrió un error');
                });
            }
            $('#modal'+(contadorModal - 1)).css({ "overflow-y": "scroll"}); 
            buscar("{{$entidad}}");  
		});

    }
    */

    function abrirmodalpagomulta(controlador, titulo, idcaja){
		if(idcaja !=0){
			modal(controlador, titulo);
		}else{
			bootbox.confirm({
				title: "Mensaje de error",
				message: "Caja no aperturada",
				buttons: {
					cancel: {
						label: 'Cancelar'
					},
					confirm: {
						label: 'Aceptar'
					}
				},
				callback: function (result) {
					if(result){
						
					}
				}
			});

		}
		
	}
    /*
    function abrirmodaljustificar(controlador, titulo, idcaja){
        if(idcaja !=0){
			modal(controlador, titulo);
		}else{
			bootbox.confirm({
				title: "Mensaje de error",
				message: "Caja no aperturada",
				buttons: {
					cancel: {
						label: 'Cancelar'
					},
					confirm: {
						label: 'Aceptar'
					}
				},
				callback: function (result) {
					if(result){
						
					}
				}
			});

		}
    }*/
    
</script>