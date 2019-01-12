
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($persona, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}

<div class="form-row">

	<table class="table">
		<thead>
			<tr>
				<th colspan="8">PASO 1: Se calcula las utilidades</th>
				<th rowspan="2">UTILIDAD BRUTA</th>
				<th rowspan="1" colspan="7"></th>
				<th rowspan="2" colspan="1">GASTOS</th>
				
				<th rowspan="1" colspan="7"></th>
				<th rowspan="2">UTILIDAD NETA</th>
				<th rowspan="1" colspan="7"></th>
				<th rowspan="2">Reservas</th>
				<th rowspan="1" colspan="7"></th>
				<th rowspan="2">UTILIDAD Distribuible</th>

			</tr>
			<tr>
				<th rowspan="2" colspan="1">Gastos Acumulados</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td  rowspan="2" colspan="2">U. B. Acumulada</td>
				<td  rowspan="1" colspan="5"></td>
				<td  rowspan="1" colspan="1">G. Adm. Acum.</td>
				<td  rowspan="1" colspan="1">{{ $gastadmacumulado }}</td>
				<td  rowspan="1" colspan="5"></td>
				<td  rowspan="1" colspan="2">F Social 10%</td>
				<td  rowspan="1" colspan="2">{{ (($intereses + $otros) -  $du_anterior) - (($gastadmacumulado + $int_pag_acum + $otros_acum) - $gast_du_anterior )*0.1 }}</td>
				<td  rowspan="1" colspan="5"></td>
			</tr>
			<tr>
				<td>Intereses</td>
				<td>{{ $intereses }}</td>
				<td  rowspan="1" colspan="1">I. Pag. Acum.</td>
				<td  rowspan="1" colspan="1">{{ $int_pag_acum }}</td>
			</tr>
			<tr>
				<td>Otros</td>
				<td>{{ $otros }}</td>
				<td  rowspan="1" colspan="1">Otros Acum.</td>
				<td  rowspan="1" colspan="1">{{ $otros_acumulados }}</td>
				<td  rowspan="1" colspan="3">R Legal 10%</td>
				<td  rowspan="1" colspan="3">{{ (($intereses + $otros) -  $du_anterior) - (($gastadmacumulado + $int_pag_acum + $otros_acum) - $gast_du_anterior )*0.1 }}</td>
			</tr>
			<tr>
				<td>Total acumulado</td>
				<td>{{ $intereses + $otros }}</td>
				<td  rowspan="1" colspan="1">TOTAL ACUMULADO</td>
				<td  rowspan="1" colspan="1">{{ ($gastadmacumulado + $int_pag_acum + $otros_acumulados) }}</td>
			</tr>
			<tr>
				<td>U.B DU Anterior</td>
				<td>{{ $du_anterior }}</td>
				<td  rowspan="1" colspan="1">Gast. DU Anterior</td>
				<td  rowspan="1" colspan="1">{{ $gast_du_anterior }}</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>Utilidad Bruta DU ACTUAL</td>
				<td>{{ ($intereses + $otros) -  $du_anterior }}</td>
				<td>menos</td>
				<td>Gast. DU ACTUAL</td>
				<td>{{ ($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior }}</td>
				<td>=</td>
				<td>{{ (($intereses + $otros) -  $du_anterior) - (($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior ) }}</td>
				<td>menos</td>
				<td>TOTAL</td>
				<td>{{ ((($intereses + $otros) -  $du_anterior) - (($gastadmacumulado + $int_pag_acum + $otros_acumulados) - $gast_du_anterior )*0.1)*2 }}</td>
				<td>{{ $utilidad_dist }}</td>
			</tr>
		</tfoot>
	</table>
</div>
<div class="form-row">
    <table>
		<thead>
			<tr><th rowspan="15">PASO 2: Se multiplica el N° de Acciones de cada me s por los meses que cada accion ha trabajado. Se obtiene las ACCIONES-MES y su total</th><th>{{ $acciones_mes }}</th></tr>
		</thead>
		<tbody>
			<tr><td rowspan="2"></td><td rowspan="12">{{ $anio }}</td><td>{{ $anio_actual }}</td><td></td></tr>
			<tr>
				<td>Meses</td>
				<td>E</td><td>F</td><td>M</td><td>A</td><td>M</td><td>J</td><td>J</td><td>A</td><td>S</td><td>O</td><td>N</td><td>D</td><td>E</td>
				<td>TOTAL</td>

			</tr>
			<tr>
				<td rowspan="2">Total mensual de Acc.</td>
				<?php
				$total_acc_mensual  =0;
				foreach ($acciones_mensual as $num_acciones){
					$total_acc_mensual += $num_acciones;
					echo("<td>".$num_acciones."</td>")
				}
				?>
				<td>0</td>
				<td>{{ $total_acc_mensual }}</td>
			</tr>
			<tr>
				<td rowspan="2">Meses "trabajados"</td>
				<td>12</td><td>11</td><td>10</td><td>9</td><td>8</td><td>7</td><td>6</td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td><td>0</td><td>---</td>
			</tr>
			<tr>
				<td rowspan="2">Acciones-mes</td>
				<?php
				$i=12;
				$sumatotal_acc_mes = 0;
				foreach ($acciones_mensual as $num_acciones){
					echo("<td>".($num_acciones * $i)."</td>");
					$sumatotal_acc_mes += $num_acciones * $i;
					$i--;
				}
				?>
				<td>0</td><td>{{ $sumatotal_acc_mes }}</td>
			</tr>
			<tr><td rowspan="16"></td></tr>
			{{-- PASO 3 --}}
			<tr>
				<td>PASO 3:</td>
				<td rowspan="2">Se divide la utilidad Distribuible: </td>
				<td rowspan="2">{{ $utilidad_dist }}</td>
				<td rowspan="2">entre el total de Acciones-Mes: </td>
				<td rowspan="2">{{ $sumatotal_acc_mes }}</td>
				<td></td>
				<td rowspan="5">El resultado es la UTILIDAD DE UNA ACCION EN UN MES: </td>
				<td>{{ $utilidad_dist/$sumatotal_acc_mes }}</td>
			</tr>
			<tr><td rowspan="16"></td></tr>
			{{-- PASO 4 --}}
			<tr>
				<td rowspan="2">PASO 4: </td>
				<td rowspan="4">Se multiplica esta utilidad.</td>
				<td rowspan="2">{{ $utilidad_dist/$sumatotal_acc_mes }}</td>
				<td rowspan="7">por el N° de meses que ha trabajado cada accion. Los resultados son las diferentes utilidades de una accion en un año.</td>
				<td></td>
			</tr>
			<tr><td rowspan="2"></td><td rowspan="12">{{ $anio }}</td><td>{{ $anio_actual }}</td><td></td></tr>
			<tr>
				<td>Meses</td>
				<td>E</td><td>F</td><td>M</td><td>A</td><td>M</td><td>J</td><td>J</td><td>A</td><td>S</td><td>O</td><td>N</td><td>D</td><td>E</td>
				<td>TOTAL</td>
			</tr>
			<tr>
				<td colspan="2">Utilidad de una acción</td>
				<td>En 1 mes</td>
				<td rowspan="14">{{ $utilidad_dist/$sumatotal_acc_mes }}</td>
			</tr>
			<tr>
				<td>En el año</td>
				<?php
				$factor = $utilidad_dist/$sumatotal_acc_mes;
					for ($i=12; $i >0 ; $i--) { 
						echo("<td>".($i * $factor)."</td>");
					}
				?>
			</tr>
			{{-- PASO 5 Y 6 --}}
			<tr>
				<td rowspan="15">PASO 5: Se multiplica cada una de las utilidades anuales por el número de acciones de cada socio en el mes respectivo. Los resultados son las utilidades del socio en cada uno de los meses</td>
			</tr>
			<tr><td rowspan="15">PASO 6: Se sumasn estas utilidades mensuales y se obtiene  la UTILIDAD TOTAL del socio en el año (última columna de la derecha).</td></tr>
			<tr><td rowspan="2" colspan="2">SOCIOS</td><td rowspan="12">{{ $anio }}</td><td>{{ $anio_actual }}</td><td colspan="2">TOTAL</td></tr>
			<tr>
				<td>E</td><td>F</td><td>M</td><td>A</td><td>M</td><td>J</td><td>J</td><td>A</td><td>S</td><td>O</td><td>N</td><td>D</td><td>E</td>
			</tr>
			<?php
				foreach ($lista_socios as $key => $socio) {
					
				}
			?>
		</tbody>
		<tfoot>

		</tfoot>
	</table>
</div>
{!! Form::close() !!}
<script type="text/javascript">
</script>