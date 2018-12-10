
<div id="divMensajeError{!! $entidad !!}"></div>
<div class="card-box table-responsive crbox">
    <div class="form-row lbldatos">
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Cliente: '.$credito->nombres.' '.$credito->apellidos, array('id'=>'cliente','class' => '')) !!}
        </div>
    </div>
    <div class="form-row lbldatos">
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Monto S/.: '.$credito->valor_credito, array('id'=>'montocredito','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Interes mensual (%): '.$credito->tasa_interes, array('id'=>'interesmes','class' => '')) !!}
        </div>
    </div>
    <div class="form-row lbldatos">
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Tiempo (Meses): '.$credito->periodo, array('id'=>'tiempomeses','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'N° de cuotas de pago: '.$credito->periodo, array('id'=>'numcuotaspago','class' => '')) !!}
        </div>
    </div>
    <div class="form-row lbldatos">
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Fecha de inicio: '.$credito->fechai, array('id'=>'fechainicio','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Fecha de caducidad: '.$fechacaducidad, array('id'=>'fechacaduca','class' => '')) !!}
        </div>
    </div>
    <div class="form-row lbldatos">
        <div class="form-group col-12 lbldatos">
            {!! Form::label('', 'Descripcion: '.$credito->descripcion, array('id'=>'descripcredito','class' => '')) !!}
        </div>
    </div>
    
</div>
<div class="row" >
    <div class="col-sm-12">
        <div class="card-box table-responsive crbox">
            <div class="row m-b-30" id="selectfilas">
                <div class="col-sm-12">
					{!! Form::open(['route' => $ruta["buscarcuota"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusquedaCuota']) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
                    {!! Form::hidden('idcredito', $idcredito, array('id' => 'idcredito')) !!}
					<div class="form-group" >
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 5, array('class' => 'form-control input-xs d-none d-sm-block', 'onchange' => 'buscar(\''.'Cuota'.'\')')) !!}
					</div>
					{!! Form::close() !!}
                </div>
            </div>
            <div class="form-group col-12" style="height: 15px">
                <h4>CUOTAS DE PAGO: </h4>
            </div>
			<div id="listado{{ $entidad }}"></div>
            
            <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
                &nbsp;
                {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
        configurarAnchoModal('1000');
        $('#selectfilas').hide();
        $('#selectfilas').css({'height':'0px'});
        $('.lbldatos').css({'padding':'0px','margin':'2px 0px'});
        $('.crbox').css({'padding':'0px 15px 0px 15px','margin':'10px 0px 0px 10px'});
        $('.contbtn').css({'padding': '10px 0'});
        buscar('{{ $entidad }}');
		init(IDFORMBUSQUEDA+'{{ $entidad }}', 'B', '{{ $entidad }}');
    });

</script>