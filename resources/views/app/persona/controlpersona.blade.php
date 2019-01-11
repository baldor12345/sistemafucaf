<div class="row">
    <div class="col-sm-12">
        <div class="card-box table-responsive">
            {!! Form::open(['route' => $ruta["buscarpersona"], 'method' => 'GET', 'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaControlPersona']) !!}
            <div class="row m-b-30">
                <div class="col-sm-12">
                    {!! Form::hidden('page', 1, array('id' => 'page')) !!}
                    {!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
                    <div class="form-group">
                        {!! Form::label('fecha', 'Fecha de Reunion:', array('class' => 'input-xs')) !!}
                        {!! Form::date('fecha', '', array('class' => 'form-control input-sm', 'id' => 'fecha')) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('filas', 'Filas a mostrar:')!!}
                        {!! Form::selectRange('filas', 1, 30, 6, array('class' => 'form-control input-xs', 'onchange' => 'buscar(\''.$entidad.'\')')) !!}
                    </div>
                                    
                    {!! Form::button('<i class="glyphicon glyphicon-search"></i> Buscar', array('class' => 'btn btn-success waves-effect waves-light m-l-10 btn-md', 'id' => 'btnBuscar', 'onclick' => 'buscar(\''.$entidad.'\')')) !!}
                </div>
            </div>
            {!! Form::close() !!}
        
            <div id="listado{{ $entidad }}"></div>
        </div>
    </div>
</div>
</div>

<div class="form-group text-center">
{!! Form::button('Cerrar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCerrar', 'onclick' => 'cerrarModal();')) !!}
</div>
<script type="text/javascript">
    $(document).ready(function() {
        configurarAnchoModal('800');
        init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
        var fechaActual = new Date();
        var day = ("0" + fechaActual.getDate()).slice(-2);
        var month = ("0" + (fechaActual.getMonth()+1)).slice(-2);
        var fechaactualr = (fechaActual.getFullYear()) +"-"+month+"-"+day+"";
        $('#fecha').val(fechaactualr);
        buscar("{{ $entidad }}");
    }); 
</script>
