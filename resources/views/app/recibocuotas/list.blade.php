
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
		$nomMeses = array(
        '1'=>'Ene',
        '2'=>'Feb',
        '3'=>'Mar',
        '4'=>'Abr',
        '5'=>'May',
        '6'=>'Jun',
        '7'=>'Jul',
        '8'=>'Ago',
        '9'=>'Sep',
        '10'=>'Oct',
        '11'=>'Nov',
        '12'=>'Dic');
		$contador = $inicio + 1;
		?>
		@foreach ($lista as $key => $value)
		<?php
		$interes_ganado =0;
		
		if($value->fecha_iniciomora != null){
			// $fecha_init = date("Y-m-d", strtotime($value->fecha_iniciomora));
			// $fecha_inicial = new DateTime($fecha_init);
			 $fecha_fin =null;
			if($value->fecha_pago == null){
				$fecha_fin= date("Y-m-d", strtotime($fecha_actual));
			}else{
				$fecha_fin= date("Y-m-d", strtotime($value->fecha_pago));
			}
			// $fecha_final = new DateTime($fecha_fin);
			// $diferencia = $fecha_inicial->diff( $fecha_final);
			// $numeroDias = $diferencia->format('%R%a días');
			// echo("numerodias: ".$numeroDias);


			//******************************************

			$anio_menor= date("Y", strtotime($value->fecha_iniciomora));
			$mes_menor= date("m", strtotime($value->fecha_iniciomora));

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
				$interes_ganado = $num_meses*($value->tasa_interes_mora/100) * ($value->parte_capital + $value->interes);
			}
		}
			 
		?>
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->nombres.'  '.$value->apellidos }}</td>
			<td>{{ $value->numero_cuota.'/'.$value->periodo }}</td>
			<td>{{ round($value->parte_capital +  $value->interes, 1) }}</td>
			<td>{{ round($interes_ganado, 1) }}</td>
			<td>{{ round($value->parte_capital +  $value->interes + $interes_ganado, 1) }}</td>
			@if($value->estado=='m')
			<td style="color: red;">Moroso</td>
			@else
			<td>-</td>
			@endif
			<td>{{ $nomMeses[$value->mes].'-'.$value->anio }}</td>
			<td>{!! Form::button('<i class="fa fa-file-pdf-o fa-lg"></i> Recibo', array('class' => 'btn btn-warning btn-xs', 'id' => 'btnrecibo', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta["generarecibopagocuotaPDF"], array($value->cuota_id)).'\',\''.'1000'.'\',\''.'Voucher de Pago Cuota'.'\')')) !!}</td>
			<td>{!! Form::button('<i class="fa fa-money fa-lg"></i> Pagar', array('class' => 'btn btn-success btn-xs', 'id' => 'btnpago2', 'onclick' => 'modal(\''.URL::route($ruta["vistapagocuota"], array($value->cuota_id, 'SI','ReciboCuota', 'valor_moratorio'=>$interes_ganado, 'fechaselect'=>$fecha_actual)).'\', \''.$tituloPagoCuota.'\')')) !!}</td>
			@if($value->estado == 'm')
			<td>{!! Form::button('<i class="fa fa-lg"></i> Mora aplicada', array('class' => 'btn btn-light btn-xs', 'id' => 'btnmora')) !!}</td>
			@else
			<td>{!! Form::button('<i class="fa fa-lg"></i> Aplicar mora', array('class' => 'btn btn-danger btn-xs', 'id' => 'btnmora',  'onclick' => 'modal(\''.URL::route($ruta["vistaaplicarmora"], array($value->cuota_id, 'listar'=>'SI','fecha_iniciomora'=>''.$fecha_actual )).'\', \''.'Aplicar Mora'.'\')')) !!}</td>
			@endif

		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif