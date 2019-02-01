
<div class="card-box table-responsive crbox">
    <div class="row m-b-30" id="selectfilas">
        <div class="col-sm-12">
            {!! Form::open(['route' => $ruta["listardetalleahorro"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
            {!! Form::hidden('page', 1, array('id' => 'page')) !!}
            
            {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
            {!! Form::hidden('persona_id', $persona->id, array('id' => 'persona_id')) !!}
            <div class="form-group" >
                {!! Form::label('fechainicio', 'Desde:')!!}
                {!! Form::date('fechainicio', $fecha_pordefecto, array('class' => 'form-control input-xs', 'id' => 'fechainicio',  'onchange' => 'buscar(\''.$entidad.'\')')) !!}
             </div>		
            <div class="form-group" >
                {!! Form::label('filas', 'Filas a mostrar:')!!}
                {!! Form::selectRange('filas', 1, 30, 5, array('class' => 'form-control input-xs d-none d-sm-block', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
            </div>
            <div class="form-group" >
            {!! Form::label('tipo', 'Tipo:')!!}
            {!! Form::select('tipo', $cbotipo, 'I', array('class' => 'form-control input-xs', 'id' => 'tipo','onchange' => 'buscar(\''.$entidad.'\')')) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div id="listado{{ $entidad }}"></div>
    
    <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
        &nbsp;
        {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('850');
        
        buscar("{{ $entidad }}");
    });
</script>