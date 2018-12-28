<div class="card-box table-responsive crbox">
    <div class="row m-b-30" id="selectfilas">
        <div class="col-sm-12">
            {!! Form::open(['route' => $ruta["listarhistorico"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad]) !!}
            {!! Form::hidden('page', 1, array('id' => 'page')) !!}
            
            {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
            {!! Form::hidden('persona_id', $persona_id, array('id' => 'persona_id')) !!}
            <div class="form-group" >
                {!! Form::label('filas', 'Filas a mostrar:')!!}
                {!! Form::selectRange('filas', 1, 30, 5, array('class' => 'form-control input-xs d-none d-sm-block', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
            </div>
            <div class="form-group" >
            {!! Form::label('cboanio', 'Año:')!!}
            {!! Form::select('cboanio', $cboanio, null, array('class' => 'form-control input-xs', 'id' => 'cboanio', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
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