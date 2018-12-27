
<div id="divMensajeError{!! $entidad !!}"></div>
<div class="card-box table-responsive crbox">
    <div class="row m-b-30" id="selectfilas">
        <div class="col-sm-12">
            {!! Form::open(['route' => $ruta["buscarahorro"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaDetalleahorro']) !!}
            {!! Form::hidden('page', 1, array('id' => 'page')) !!}
            
            {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
            {!! Form::hidden('persona_id', $persona->id, array('id' => 'persona_id')) !!}
            <div class="form-group" >
                {!! Form::label('filas', 'Filas a mostrar:')!!}
                {!! Form::selectRange('filas', 1, 30, 5, array('class' => 'form-control input-xs d-none d-sm-block', 'onchange' => 'buscar(\''.'Detalleahorro'.'\')')) !!}
            </div>
            <div class="form-group" >
            {!! Form::label('estado', 'Estado:')!!}
            {!! Form::select('estado', $cboestado, 'P', array('class' => 'form-control input-xs', 'id' => 'estado','onchange' => 'buscar(\''.'Detalleahorro'.'\')')) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    <div class="form-group col-12" style="height: 15px">
        <h4>Depositos de ahorros: </h4>
    </div>
    <div id="listado{{ $entidad1 }}"></div>
    
    <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
        &nbsp;
        {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad1, 'onclick' => 'cerrarModal();')) !!}
    </div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		configurarAnchoModal('850');
        buscar("{{ $entidad1 }}");
    });

    

</script>