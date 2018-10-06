<?php 
use App\Menuoptioncategory;
use App\Usertype;
use App\Menuoption;
use App\OperacionMenu;
use App\Operacion;

$categoriasPadre = Menuoptioncategory::whereNull('menuoptioncategory_id')->get();
$asignados       = array();
$opciones        = Usertype::find($tipousuario->id)->menuoptions;

foreach ($opciones as $key => $value) {
	$asignados[] = $value->id;
}

$operacionesasignadas = array();
$operaciones 	 = Usertype::find($tipousuario->id)->operacionmenu;
foreach($operaciones as $key => $value){
	$operacionesasignadas[] = $value->id;
}

$categorias_asignadas = array();
foreach($opciones as $key => $value){
	if(!in_array($value->menuoptioncategory_id,$categorias_asignadas)){
		$categorias_asignadas[] = $value->menuoptioncategory_id;
	}
}

/*
function generarArbol($idcategoria, $nivel, $asignados){
	$sangria = '';
	for ($i=0; $i < pow(2, $nivel); $i++) { 
		$sangria .= '&nbsp;';
	}
	$categorias = Menuoptioncategory::where('menuoptioncategory_id', '=', $idcategoria)->orderBy('order', 'ASC')->get();
	$opcionmenus = Menuoption::where('menuoptioncategory_id', '=', $idcategoria)->orderBy('order', 'ASC')->get();
?>
	@foreach($opcionmenus as $key => $opcionmenu)
		@if(in_array($opcionmenu->id, $asignados))
			{!! $sangria !!}
			@if(strtoupper($opcionmenu->name) === 'SEPARADOR')
				{!! Form::label('condicion'.$opcionmenu->id, '<< SEPARADOR >>') !!}
			@else
				{!! Form::label('condicion'.$opcionmenu->id, $opcionmenu->name) !!}
			@endif
			{!! Form::checkbox('condicion[]', '', Input::old('condicion'.$opcionmenu->id, true), array('id' => 'condicion'.$opcionmenu->id,'class' => 'pull-right', 'onchange' => 'cambiarEstado(this, \''.'estado'.$opcionmenu->id.'\');')) !!}
			{!! Form::hidden('estado[]', Input::old('estado'.$opcionmenu->id, 'H'), array('id' => 'estado'.$opcionmenu->id)) !!}
			{!! Form::hidden('idopcionmenu[]', $opcionmenu->id, array('id' => 'idopcionmenu'.$opcionmenu->id)) !!}
			{!! '<br>' !!}
		@else
			{!! $sangria !!}
			@if(strtoupper($opcionmenu->name) === 'SEPARADOR')
				{!! Form::label('condicion'.$opcionmenu->id, '<< SEPARADOR >>') !!}
			@else
				{!! Form::label('condicion'.$opcionmenu->id, $opcionmenu->name) !!}
			@endif
			{!! Form::checkbox('condicion[]', '', Input::old('condicion'.$opcionmenu->id, false), array('id' => 'condicion'.$opcionmenu->id,'class' => 'pull-right', 'onchange' => 'cambiarEstado(this, \''.'estado'.$opcionmenu->id.'\');')) !!}
			{!! Form::hidden('estado[]', Input::old('estado'.$opcionmenu->id, 'I'), array('id' => 'estado'.$opcionmenu->id)) !!}
			{!! Form::hidden('idopcionmenu[]', $opcionmenu->id, array('id' => 'idopcionmenu'.$opcionmenu->id)) !!}
			{!! '<br>' !!}
		@endif
	@endforeach

	@foreach($categorias as $key => $categoria)
		{!! $sangria !!}
		{!! "<b><u><span class='text-info'>".$categoria->name."</span></u></b>" !!}
		{!! '<br>' !!}
		<?php generarArbol($categoria->id, $nivel+1, $asignados); ?>
	@endforeach
<?php }*/ ?>
{!! Form::open(array('route' => array('tipousuario.guardaroperaciones', $tipousuario->id), 'id' => 'formMantenimiento'.$entidad)) !!}
	{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
	<div class="form-group border">
	<div class="accordion" id="accordionExample">
		@foreach($categoriasPadre as $key => $categoria)
		@if(in_array($categoria->id,$categorias_asignadas))
		<div class="card">
			{!!"<div class=\"card-header\" id=\"$categoria->id\">"!!}
				<h5 class="mb-0">
					{!!"<button class=\"btn btn-primary\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapse$categoria->id\" aria-expanded=\"false\" aria-controls=\"collapse$categoria->id\">"!!}
					{!! $categoria->name!!}
					</button>
				</h5>
			</div>
			<?php 
                $sangria = '';
                $nivel = 2;
            	for ($i=0; $i < pow(2, $nivel); $i++) { 
            		$sangria .= '&nbsp;';
            	}
            	$categorias = Menuoptioncategory::where('menuoptioncategory_id', '=', $categoria->id)->orderBy('order', 'ASC')->get();
            	$opcionmenus = Menuoption::where('menuoptioncategory_id', '=', $categoria->id)->orderBy('order', 'ASC')->get();
            ?>
			{!!"<div id=\"collapse$categoria->id\" class=\"collapse\" aria-labelledby=\"$categoria->id\" data-parent=\"#accordionExample\">"!!}
			<div class="card-body">

				@foreach($opcionmenus as $key => $opcionmenu)
					
					@if(in_array($opcionmenu->id, $asignados))
					{!!"<div class=\"accordion\" id=\"accordionmenu$opcionmenu->id\">"!!}
						<div class="card">

							{!!"<div class=\"card-header\" id=\"menu$opcionmenu->id\">"!!}
								<h5 class="mb-0">
									{!!"<button class=\"btn btn-info\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapsemenu$opcionmenu->id\" aria-expanded=\"false\" aria-controls=\"collapsemenu$opcionmenu->id\">"!!}
									{!! $opcionmenu->name!!}
									</button>
								</h5>
							</div>

							{!!"<div id=\"collapsemenu$opcionmenu->id\" class=\"collapse\" aria-labelledby=\"menu$opcionmenu->id\" data-parent=\"#accordionmenu$opcionmenu->id\">"!!}
								<div class="card-body">

									<ul class="list-group list-group-flush">

										<?php
										$operaciones_menu = OperacionMenu::where('menuoption_id', '=', $opcionmenu->id)->orderBy('operacion_id', 'ASC')->get();
										?>

										@foreach($operaciones_menu as $key => $value)
											<?php
											$operacion = Operacion::find($value->operacion_id);
											?>
											@if(in_array($value->id,$operacionesasignadas))
											<li class="list-group-item">
												{!! Form::label('condicion'.$value->id, $operacion->nombre) !!}
												{!! Form::checkbox('condicion[]', '', Input::old('condicion'.$value->id, true), array('id' => 'condicion'.$value->id,'class' => 'pull-right', 'onchange' => 'cambiarEstado(this, \''.'estado'.$value->id.'\');')) !!}
												{!! Form::hidden('estado[]', Input::old('estado'.$value->id, 'H'), array('id' => 'estado'.$value->id)) !!}
												{!! Form::hidden('idoperacionmenu[]', $value->id, array('id' => 'idoperacionmenu'.$value->id)) !!}
											</li>
											@else
											<li class="list-group-item">
												{!! Form::label('condicion'.$value->id, $operacion->nombre) !!}
												{!! Form::checkbox('condicion[]', '', Input::old('condicion'.$value->id, false), array('id' => 'condicion'.$value->id,'class' => 'pull-right', 'onchange' => 'cambiarEstado(this, \''.'estado'.$value->id.'\');')) !!}
												{!! Form::hidden('estado[]', Input::old('estado'.$value->id, 'I'), array('id' => 'estado'.$value->id)) !!}
												{!! Form::hidden('idoperacionmenu[]', $value->id, array('id' => 'idoperacionmenu'.$value->id)) !!}
											</li>
											@endif
										@endforeach

									</ul>	

								</div>
							</div>

						</div>
					</div>
					@endif
				@endforeach
				
			</div>
			</div>
			
		</div>
		@endif
		@endforeach
	</div>
	</div>
	<div class="form-group text-center">
		{!! Form::button('Guardar', array('class' => 'btn btn-success btn-sm', 'id' => 'btnGuardar', 'onclick' => 'guardar(\''.$entidad.'\', this)')) !!}
		{!! Form::button('Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal((contadorModal - 1));')) !!}
	</div>
{!! Form::close() !!}

<script type="text/javascript">
$(document).ready(function() {
	init(IDFORMMANTENIMIENTO+'{!! $entidad !!}');
}); 
function cambiarEstado (elemento, id) {
	if (elemento.checked) {
		$('#'+id).val('H');
	} else{
		$('#'+id).val('I');
	};
}
</script>