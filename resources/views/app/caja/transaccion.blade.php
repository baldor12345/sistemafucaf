<?php 
use App\Persona;
use App\Acciones;
use App\Configuraciones;
use Illuminate\Support\Facades\DB;
?>
<script>
function cargarselect2(entidad){
	var select = $('#tipo_id1').val();
	if(select == ''){
		$('#concepto_id1').html('<option value="" selected="selected">Todo</option>');
		return false;
	}
	route = 'caja/cargarselecttransaccion/' + select + '?entidad='+entidad+'&t=si';
	$.ajax({
		url: route,
		headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
		type: 'GET',
		beforeSend: function(){
			$('#concepto_id1').html('<option value="" selected="selected">Todo</option>');
		},
		success: function(res){
			$('#concepto_id1').html(res);
		}
	});
}
</script>

<div class="row">
	<div class="card-box table-responsive">
		{!! Form::open(['route' => $ruta["buscartransaccion"], 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaTransaccion']) !!}
		<div class="row">
			<div class="col-sm-12">
				{!! Form::hidden('page', 1, array('id' => 'page')) !!}
				{!! Form::hidden('idcaja', $id, array('id' => 'idcaja')) !!}
				{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
				<div class="form-group">
					{!! Form::label('tipo_id1', 'Tipo:', array('class' => 'input-sm')) !!}
					{!! Form::select('tipo_id1', $cboTipo1, null, array('class' => 'form-control input-sm', 'id' => 'tipo_id1','onchange'=>'cargarselect2("concepto")')) !!}
				</div>
				<div class="form-group">
					{!! Form::label('concepto_id1', 'Concepto:', array('class' => 'input-sm')) !!}
					{!! Form::select('concepto_id1', $cboConceptos1, null, array('class' => 'form-control input-sm', 'id' => 'concepto_id1')) !!}
				</div>
				<div class="form-group">
					{!! Form::label('filas', 'Filas a mostrar:')!!}
					{!! Form::selectRange('filas', 1, 30, 10, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
				</div>
								
				{!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-sm', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
				<a target="_blank" href="{{ route('generarresumenPDF', array('id' => $id)) }}" class="btn btn-primary waves-effect waves-light btn-sm" ><i class="glyphicon glyphicon-download-alt" ></i> Resumen</a>
			</div>
		</div>
		{!! Form::close() !!}
	
		<div id="listado{{ $entidad }}"></div>
	

		<table class="table-bordered table-striped table-condensed" align="center">
			<thead>
				<tr>
					<th class="text-center" colspan="2"><font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 13px;">Resumen de Caja</font></font></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 13px;">Ingresos :</font></font></th>
					<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 13px;">{{ round($ingresos,1) }}</font></font></th>
				</tr>

				<tr>
					<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 13px;">Egresos :</font></font></th>
					<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 13px;">{{ round($egresos,1) }}</font></font></th>
				</tr>
				<tr>
					<th><font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 13px;">Saldo :</font></font></th>
					<th class="text-right"><font style="vertical-align: inherit;"><font style="vertical-align: inherit; font-size: 13px;">{{ round($diferencia,1) }}</font></font></th>
				</tr>
			</tbody>
		</table>


	</div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	configurarAnchoModal('1250');
	buscar("{{ $entidad }}");
	init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
}); 
</script>
