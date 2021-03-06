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
		<tr>
			<td>{{ $contador }}</td>
			<td>{{ $value->titulo }}</td>
			<td>{{ round($value->utilidad_distribuible, 1) }}</td>
			@if( $value->porcentaje_distribuido < 100)
				<td>
					{!! Form::button('<i class="fa fa-file-pdf-o"></i>'.$value->porcentaje_distribuido.'%', array('class' => 'btn btn-danger btn-sm','data-dismiss'=>'modal', 'id' => 'btnreporte', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta["distutilcreadoPDF"], array(date("Y", strtotime($value->fechai)), $value->porcentaje_distribuido, $value->id)).'\')')) !!}
				</td>
			@else
				<td>
					{!! Form::button('<i class="fa fa-file-pdf-o"></i>'.$value->porcentaje_distribuido.'%', array('class' => 'btn btn-success btn-sm','data-dismiss'=>'modal', 'id' => 'btnreporte', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta["distutilcreadoPDF"], array(date("Y", strtotime($value->fechai)), $value->porcentaje_distribuido, $value->id)).'\')')) !!}
				</td>
			@endif
			<td>
			{!! Form::button('<i class="glyphicon glyphicon-eye-open"></i> Ver', array('class' => 'btn btn-info waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnVer', 'onclick' => 'modal(\''.URL::route($ruta["verdistribucion"], array($value->id)).'\', \''.$value->titulo.'\', this);')) !!}
			</td>
			<!-- <td>{!! Form::button('<i class="fa fa-file-pdf-o"></i>PDF', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnreporte', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta["reportedistribucionPDF"], array($value->id)).'\')')) !!}</td> -->
			<td>{!! Form::button('<i class="fa fa-file-pdf-o"></i> Recibos', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnreporte', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta["listaSociosReciboDistribucionPDF"], array($value->id)).'\')')) !!}</td>
		</td>
		</tr>
		<?php
		$contador = $contador + 1;
		?>
		@endforeach
	</tbody>
</table>
@endif