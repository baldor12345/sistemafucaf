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
			<td>{{ $value->persona->apellidos.'  '.$value->persona->nombres }}</td>
			<td align ="center">{{ $value->capital }}</td>
			<td align="center">{{ $value->inicio }}</td>
			<td align="center">{{ $value->fin }}</td>
			<td align="center">{{ $value->num_acciones }}</td>
			<td>{{ $value->codigo }}</td>
			@if($value->semestre <= 6)
			<td align="center">Primer</td>
			@else
			<td align="center">Segundo</td>
			@endif
			<td align="center">{{ date('Y', strtotime($value->fechai)) }}</td>
			<td>{{ $cboMonth[intval(date('m', strtotime($value->fechai)))].' - '.$cboMonth[intval(date('m', strtotime($value->fechaf)))] }}</td>
			<td align="center">{!! Form::button('<div class="glyphicon glyphicon-download-alt"></div> Descargar', array('onclick' => 'abrirmodalpagomulta (\''.URL::route($ruta["cargarpagarcontribucion"], array($value->id, 'listar'=>'SI')).'\', \''.$titulo_pagarmulta.'\', \''.$idCaja.'\');','class' => 'btn btn-xs btn-warning')) !!}</td>
		</tr>

		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif
<script>
function abrirmodalpagomulta(controlador, titulo, idcertificado){
		if(idcertificado !=0){
			modal(controlador, titulo);
		}else{
			bootbox.confirm({
				title: "Mensaje de error",
				message: "Tenga amabilidad de aperturar caja para seguir con el proceso, gracias!",
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

</script>