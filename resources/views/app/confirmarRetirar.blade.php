<div id="divMensajeError{!! $entidad !!}"></div>
{!! Form::model($modelo, $formData) !!}
{!! Form::hidden('listar', $listar, array('id' => 'listar')) !!}
{!! $mensaje or '<blockquote><p class="text-danger">¿Esta seguro retirar el ahorro?</p></blockquote>' !!}
<div class="form-group">
	<div class="col-lg-12 col-md-12 col-sm-12 text-right">
		{!! Form::button('<i class="glyphicon glyphicon-remove"></i> '.$boton, array('class' => 'btn btn-danger btn-sm', 'id' => 'btnGuardar', 'onclick' => 'retirar(\''.$modelo->id.'\')')) !!}
		{!! Form::button('<i class="fa fa-exclamation fa-lg"></i> Cancelar', array('class' => 'btn btn-warning btn-sm', 'id' => 'btnCancelar'.$entidad, 'onclick' => 'cerrarModal();')) !!}
	</div>
</div>
{!! Form::close() !!}
<script type="text/javascript">
	$(document).ready(function() {
		init(IDFORMMANTENIMIENTO+'{!! $entidad !!}', 'M', '{!! $entidad !!}');
		configurarAnchoModal('350');
    });

    function retirar(id){
        
			console.log("datos serialixados 0001: "+$('#formMantCredito').serialize());
			$.ajax({
				url: 'ahorros/retiro',
				headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' },
				type: 'GET',
				data: 'id_ahorro='+id,
				beforeSend: function(){
					
				},
				success: function(res){
                    window.close();
					
				}
			}).fail(function(){
				
			});
		}
</script>