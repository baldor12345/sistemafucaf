
<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($ahorros, array('class' => 'form-horizontal' , 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off')) !!}

    <div class="form-row">
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Cliente: '.$persona->nombres.' '.$persona->apellidos, array('id'=>'cliente','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Monto Inicial S/.: '.$ahorros->importe, array('id'=>'montoahorros','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Interes mensual (%): '.$ahorros->interes, array('id'=>'interesmes','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Periodo: '.$ahorros->periodo.' meses', array('id'=>'periodo','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Monto final: '.$montofinal, array('id'=>'montofinal','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Fecha de inicio: '.$ahorros->fecha_inicio, array('id'=>'fecha_inicio','class' => '')) !!}
        </div>
        <div class="form-group col-6 col-md-6 col-sm-6">
            {!! Form::label('', 'Fecha de retiro: '.$ahorros->fecha_fin, array('id'=>'fecha_final','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-12">
            {!! Form::label('', 'Descripcion: '.$ahorros->descripcion, array('id'=>'descripcion','class' => '')) !!}
        </div>
    </div>
    <div class="form-row">
        <div class="form-group col-12">
            {!! Form::label('', 'Estado: '.($ahorros->estado == 'P'?'Pendiente':'Retirado').'', array('id'=>'estado','class' => '')) !!}
        </div>
    </div>

    <div class="form-group">
        <div class="col-lg-12 col-md-12 col-sm-12 text-right">
            @if($ahorros->estado == 'P')
            <div id="btnRetiro">
            {!! Form::button('<i class="glyphicon glyphicon-remove"></i> Retirar ', array('class' => 'btn btn-danger btn-sm', 'id' => 'btnRetirar', 'onclick' => 'retirar(\''.$ahorros->id.'\')')) !!}
            </div>
            @endif
            &nbsp;
            {!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Close', array('class' => 'btn btn-warning btn-sm','data-dismiss'=>'modal', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
        </div>
    </div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
        console.log("Ide de la ventana: "+$(this).attr("id"));
		configurarAnchoModal('650');
    });

    function retirar(id){
        
        $.ajax({
            url: 'ahorros/retiro',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            type: 'GET',
            data: 'id_ahorro='+id,
            beforeSend: function(){
                
            },
            success: function(res){
                console.log("Ingreso-..s");
                $('#estado').text("Estado: Retirado");
                $('btnRetiro').hide();
            }
        }).fail(function(){
            
        });
    }

</script>