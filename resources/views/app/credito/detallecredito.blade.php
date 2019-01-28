<script type="text/javascript">
   // var rutareportecuotas = "{{ URL::route($ruta['generareportecuotasPDF'], array())}}";
   // function imprimirpdf(){
   //     window.open(rutareportecuotas+"/{{ $credito->id }}", "Cuotas de Credito", "width=700, height=800, left=50, top=20");
//}
</script>
<div id="divMensajeError{!! $entidad_cuota !!}"></div>
<div class="card-box table-responsive crbox">
    <div class="form-row lbldatos">
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Socio o Cliente: '.$persona->nombres.' '.$persona->apellidos, array('id'=>'cliente','class' => '')) !!}
        </div>
    </div>
    <div class="form-row lbldatos">
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Monto S/.: '.round($credito->valor_credito,1), array('id'=>'montocredito','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Tasa interes mensual (%): '.$credito->tasa_interes, array('id'=>'interesmes','class' => '')) !!}
        </div>
    </div>
    <div class="form-row lbldatos">
        <div class="form-group col-6 col-md-6 col-sm-6 lbldatos">
            {!! Form::label('', 'Periodo (Meses): '.$credito->periodo, array('id'=>'tiempomeses','class' => '')) !!}
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
            {!! Form::label('', 'Fecha final: '.$fechacaducidad, array('id'=>'fechacaduca','class' => '')) !!}
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
					{!! Form::open(['route' => $ruta["listardetallecuotas"] , 'method' => 'GET' ,'onsubmit' => 'return false;', 'class' => 'form-inline', 'role' => 'form', 'autocomplete' => 'off', 'id' => 'formBusqueda'.$entidad_cuota]) !!}
					{!! Form::hidden('page', 1, array('id' => 'page')) !!}
					{!! Form::hidden('accion', 'listar', array('id' => 'accion')) !!}
                    {!! Form::hidden('credito_id', $credito->id, array('id' => 'credito_id')) !!}
					<div class="form-group" >
						{!! Form::label('filas', 'Filas a mostrar:')!!}
						{!! Form::selectRange('filas', 1, 30, 5, array('class' => 'form-control input-xs d-none d-sm-block', 'onchange' => 'buscar(\''.$entidad_cuota.'\')')) !!}
                    </div>
                    <div class="form-group">
                    {!! Form::button('<i class="fa fa-check fa-lg"></i> Imprimir PDF', array('class' => 'btn btn-success btn-sm', 'id' => 'btnImprimirpdf', 'onclick' => 'modalrecibopdf(\''.URL::route($ruta['generareportecuotasPDF'], array($credito->id)).'\',\''.'1000'.'\', \''.'Reporte de Cuotas'.'\')')) !!}
                    </div>
					{!! Form::close() !!}
                </div>
            </div>
            <div class="form-group col-12" style="height: 15px">
                <h4>CUOTAS DE PAGO: </h4>
            </div>
            <div id="listado{{ $entidad_cuota }}"></div>
        </div>
        </div class="card-box crbox">
            <ul>
                <li>P: Pagado</li>
                <li><button class="btn btn-danger btn-sm"></button>: Moroso</li>
                <li><button class="btn btn-warning btn-sm"></button>: Pagado solo interes</li>
            </ul>
        </div>
        
        <div class="col-lg-12 col-md-12 col-sm-12 text-right contbtn">
            &nbsp;
            {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cerrar', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad_cuota, 'onclick' => 'cerrarModal();')) !!}
        </div>
        
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
        configurarAnchoModal('1050');
        $('.lbldatos').css({'padding':'0px','margin':'2px 0px'});
        $('.crbox').css({'padding':'0px 15px 0px 15px','margin':'10px 0px 0px 10px'});
        $('.contbtn').css({'padding': '10px 0'});
        buscar('{{ $entidad_cuota }}');
		init(IDFORMBUSQUEDA+'{{ $entidad_cuota }}', 'B', '{{ $entidad_cuota }}');
    });
  

</script>