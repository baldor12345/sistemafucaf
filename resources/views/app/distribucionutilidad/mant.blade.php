
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
				<td  rowspan="1" colspan="2">{{ $fsocial }}</td>
				<td  rowspan="1" colspan="5"></td>
			</tr>
			<tr>
				<td>Intereses</td>
				<td>{{ $intereses }}</td>
				<td  rowspan="1" colspan="1">I. Pag. Acum.</td>
				<td  rowspan="1" colspan="1">{{ $ipag_acum }}</td>
				<td  rowspan="1" colspan="1">F Social 10%</td>
				<td  rowspan="1" colspan="1">{{ (($intereses + $otros) -  $duanterior) - (($gastadmacumulado + $ipag_acum + $otros_acum) - $gast_du_anterior )*0.1 }}</td>
			</tr>
			<tr>
				<td>Otros</td>
				<td>{{ $otros }}</td>
				<td  rowspan="1" colspan="1">Otros Acum.</td>
				<td  rowspan="1" colspan="1">{{ $otros_acum }}</td>
				<td  rowspan="1" colspan="3">R Social 10%</td>
				<td  rowspan="1" colspan="3">{{ (($intereses + $otros) -  $duanterior) - (($gastadmacumulado + $ipag_acum + $otros_acum) - $gast_du_anterior )*0.1 }}</td>
			</tr>
			<tr>
				<td>Total acumulado</td>
				<td>{{ $intereses + $otros }}</td>
				<td  rowspan="1" colspan="1">TOTAL ACUMULADO</td>
				<td  rowspan="1" colspan="1">{{ ($gastadmacumulado + $ipag_acum + $otros_acum) }}</td>
			</tr>
			<tr>
				<td>U.B DU Anterior</td>
				<td>{{ $duanterior }}</td>
				<td  rowspan="1" colspan="1">Gast. DU Anterior</td>
				<td  rowspan="1" colspan="1">{{ $gast_du_anterior }}</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>Utilidad Bruta DU ACTUAL</td>
				<td>{{ ($intereses + $otros) -  $duanterior }}</td>
				<td>menos</td>
				<td>Gast. DU ACTUAL</td>
				<td>{{ ($gastadmacumulado + $ipag_acum + $otros_acum) - $gast_du_anterior }}</td>
				<td>=</td>
				<td>{{ (($intereses + $otros) -  $duanterior) - (($gastadmacumulado + $ipag_acum + $otros_acum) - $gast_du_anterior ) }}</td>
				<td>menos</td>
				<td>TOTAL</td>
				<td>{{ ((($intereses + $otros) -  $duanterior) - (($gastadmacumulado + $ipag_acum + $otros_acum) - $gast_du_anterior )*0.1)*2 }}</td>
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
				@foreach ($acciones_mensual as $num_acciones)
					<td>{{ $num_acciones }}</td>
				@endforeach
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
				<td>0</td><td>{{ $sumatotal }}</td>
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

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('nombres', 'Nombres:*', array('class' => '')) !!}
			{!! Form::text('nombres', null, array('class' => 'form-control input-xs', 'id' => 'nombres', 'placeholder' => 'Ingrese Nombres', 'maxlength' => '50')) !!}

    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('apellidos', 'Apellidos:*', array('class' => '')) !!}
			{!! Form::text('apellidos', null, array('class' => 'form-control input-xs', 'id' => 'apellidos', 'placeholder' => 'Ingrese Apellidos', 'maxlength' => '50')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4 col-sm-4">
			{!! Form::label('fecha_nacimiento', 'Fecha de Nacimiento:*', array('class' => '',  'id' => 'datosApoderado')) !!}
			{!! Form::date('fecha_nacimiento', null, array('class' => 'form-control input-xs fech', 'id' => 'fecha_nacimiento' , 'placeholder' => '','onchange'=>'evaluarFecha();')) !!}
    </div>
    <div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('sexo', 'Sexo:*', array('class' => '')) !!}
			{!! Form::select('sexo', $cboSexo, null, array('class' => 'form-control input-xs', 'id' => 'sexo')) !!}
    </div>
	<div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('estado_civil', 'Estado Civil:*', array('class' => '')) !!}
			{!! Form::select('estado_civil', $cboEstadoCivil, null, array('class' => 'form-control input-xs', 'id' => 'estado_civil')) !!}
    </div>
</div>
<?php
	if($persona != null){
		echo "<input type='hidden' id='fechaTempNac' value='".Date::parse($persona->fecha_nacimiento )->format('d/m/Y')."'>";
	}else{
		echo "<input type='hidden' id='fechaTempNac' value=''>";
	}
?>

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('personas_en_casa', 'Personas en casa:', array('class' => '')) !!}
			{!! Form::number('personas_en_casa', null, array('class' => 'form-control input-xs ', 'id' => 'personas_en_casa', 'placeholder' => '', 'min'=>'1', 'max'=>'20')) !!}
    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('direccion', 'Direccion:*', array('class' => '')) !!}
			{!! Form::text('direccion', null, array('class' => 'form-control input-xs', 'id' => 'direccion', 'placeholder' => 'Ingrese direccion')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('ocupacion', 'Ocupacion:', array('class' => '')) !!}
			{!! Form::text('ocupacion', null, array('class' => 'form-control input-xs', 'id' => 'ocupacion', 'placeholder' => 'Ingrese ocupacion')) !!}
    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('email', 'E-mail:', array('class' => '')) !!}
			{!! Form::text('email', null, array('class' => 'form-control input-xs', 'id' => 'email', 'placeholder' => 'Ingrese email')) !!}
    </div>
</div>

<div class="form-row">
    <div class="form-group col-md-4 col-sm-4">
			{!! Form::label('telefono_fijo', 'Telefono fijo:*', array('class' => '')) !!}
			{!! Form::text('telefono_fijo', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_fijo', 'placeholder' => 'Ingrese telefono fijo', 'maxlength' => '15')) !!}
    </div>
    <div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('telefono_movil1', 'Telefono movil 1:', array('class' => '')) !!}
			{!! Form::text('telefono_movil1', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_movil1', 'placeholder' => 'Ingrese telefono movil', 'maxlength' => '15' )) !!}
    </div>
	<div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('telefono_movil2', 'Telefono movil 2:', array('class' => '')) !!}
			{!! Form::text('telefono_movil2', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_movil2', 'placeholder' => 'Ingrese telefono movil', 'maxlength' => '15')) !!}
    </div>
</div>



<div class="form-row">
		<div class="form-group col-md-4 col-sm-4">
			{!! Form::label('ingreso_personal', 'Ingreso personal:*', array('class' => '')) !!}
			{!! Form::text('ingreso_personal', null, array('class' => 'form-control input-xs ', 'id' => 'ingreso_personal',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
		</div>
		<div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('ingreso_familiar', 'Ingreso familiar:*', array('class' => '')) !!}
			{!! Form::text('ingreso_familiar', null, array('class' => 'form-control input-xs ', 'id' => 'ingreso_familiar',  'onkeypress'=>'return filterFloat(event,this);', 'maxlength' => '8')) !!}
		</div>
		<div class="form-group col-md-4 col-sm-4" style="margin-left: 12.5px;">
			{!! Form::label('estado', 'Estado:*', array('class' => 'input-sm')) !!}
			{!! Form::select('estado', $cboEstado, null, array('class' => 'form-control input-sm', 'id' => 'estado')) !!}
		</div>
	</div>



<div class="form-row">
    <div class="form-group col-md-6 col-sm-6">
			{!! Form::label('tipo', 'Tipo:*', array('class' => 'input-sm')) !!}
			{!! Form::select('tipo', $cboTipo, null, array('class' => 'form-control input-sm', 'id' => 'tipo')) !!}
    </div>
    <div class="form-group col-md-6 col-sm-6" style="margin-left: 25px;">
			{!! Form::label('fechai', 'Fecha de Inicio:*', array('class' => '')) !!}
			{!! Form::date('fechai', null, array('class' => 'form-control input-xs', 'id' => 'fechai', 'placeholder' => 'Ingrese Fecha inicio...')) !!}
    </div>
</div>


<div class="form-row" id='oculto' style="display:none;">
		<legend>Datos del Apoderado:</legend>
		<div class="form-row">
			{!! Form::label('nombres_apoderado', 'Nombres y Apellidos del apoderado:', array('class' => '')) !!}
			{!! Form::text('nombres_apoderado', null, array('class' => 'form-control input-xs', 'id' => 'nombres_apoderado', 'placeholder' => 'Ingrese Nombres y apellidos del apoderado...')) !!}
		</div>

		<div class="form-row">
			<div class="form-group col-md-3 col-sm-3">
					{!! Form::label('dni_apoderado', 'DNI', array('class' => '')) !!}
					{!! Form::text('dni_apoderado', null, array('class' => 'form-control input-xs input-number', 'id' => 'dni_apoderado', 'placeholder' => 'Ingrese DNI', 'maxlength' => '8')) !!}
			</div>
			<div class="form-group col-md-3 col-sm-3" style="margin-left: 12.5px;">
					{!! Form::label('telefono_fijo_apoderado', 'Telefono', array('class' => '')) !!}
					{!! Form::text('telefono_fijo_apoderado', null, array('class' => 'form-control input-xs input-number', 'id' => 'telefono_fijo_apoderado', 'placeholder' => 'Ingrese telefono', 'maxlength' => '15')) !!}
			</div>
			<div class="form-group col-md-6 col-sm-6" style="margin-left: 12.5px;">
				{!! Form::label('direccion_apoderado', 'Direccion ', array('class' => '')) !!}
				{!! Form::text('direccion_apoderado', null, array('class' => 'form-control input-xs', 'id' => 'direccion_apoderado', 'placeholder' => 'Ingrese direccion')) !!}
			</div>
		</div>
</div>


<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		&nbsp;
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		$(IDFORMMANTENIMIENTO + '{!! $entidad !!} :input[id="usertype_id"]').focus();
		configurarAnchoModal('650');
		evaluarFecha();
	}); 

	$('.input-number').on('input', function () { 
    	this.value = this.value.replace(/[^0-9]/g,'');
	});

	function evaluarFecha(){
		//fecha actual
		if($('#fecha_nacimiento').val() !== ""){
			var fechaActual = new Date();
			var añoActual = fechaActual.getFullYear();
			var mesActual = fechaActual.getMonth();
			//fecha obtenida del formulario
			var valoresFechaSel = $('#fecha_nacimiento').val().split('-');
			if((añoActual-valoresFechaSel[0])<=18 ){
				console.log("es menor de edad= "+(añoActual-valoresFechaSel[0]));
				document.getElementById('oculto').style.display = 'block';
			}else{
				document.getElementById('oculto').style.display = 'none';
			}
		}else{
			
		}
		
	}

	var fechaActual = new Date();
	var day = ("0" + fechaActual.getDate()).slice(-2);
	var month = ("0" + (fechaActual.getMonth() + 1)).slice(-2);
	var fecha_nac = "1980" +"-"+month+"-"+day+"";
	var fecha_In = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";

	if($('#fechaTempNac').val() !== ""){
		// DD/MM/YYYY
		var valoresFechaNac = $('#fechaTempNac').val().split('/');
		var valoresFechaIni = $('#fechaTempIni').val().split('/');
		//yyy/MM/DD
		var fechaNac = valoresFechaNac[2] + "-" + valoresFechaNac[1] + "-" + valoresFechaNac[0];
		var fechaIni = valoresFechaIni[2] + "-" + valoresFechaIni[1] + "-" + valoresFechaIni[0];
		$('#fecha_nacimiento').val(fechaNac);
		$('#fechai').val(fechaIni);
	}else{
		$('#fecha_nacimiento').val(fecha_nac);
		$('#fechai').val(fecha_In);
	}



	//evaluar numeros 
	function filterFloat(evt,input){
		// Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
		var key = window.Event ? evt.which : evt.keyCode;    
		var chark = String.fromCharCode(key);
		var tempValue = input.value+chark;
		if(key >= 48 && key <= 57){
			if(filter(tempValue)=== false){
				return false;
			}else{       
				return true;
			}
		}else{
			if(key == 8 || key == 13 || key == 0) {     
				return true;              
			}else if(key == 46){
					if(filter(tempValue)=== false){
						return false;
					}else{       
						return true;
					}
			}else{
				return false;
			}
		}
	}
	function filter(__val__){
		var preg = /^([0-9]+\.?[0-9]{0,2})$/; 
		if(preg.test(__val__) === true){
			return true;
		}else{
		return false;
		}
		
	}
	
	
</script>