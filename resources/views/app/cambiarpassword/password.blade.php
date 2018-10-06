<div class="row">
    <div class="col-sm-12">
        <div class="page-title-box">
            <h4 class="page-title">{{ $title }}</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="card-box table-responsive">
            <div id="divMensajeError{!! $entidad !!}"></div>
            {!! Form::model($user, $formData) !!}
            {{ csrf_field() }}	
            <div class="form-group">
                <label class="control-label col-md-4" for="mypassword">Contraseña Actual:</label>
                <div class="col-md-6">
                    <input type="password" name="mypassword" class="form-control" placeholder="Ingrese contraseña actual">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4" for="password">Nueva Contraseña:</label>
                <div class="col-md-6">
                    <input type="password" name="password" class="form-control" placeholder="Ingrese nueva contraseña">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-4" for="mypassword">Confirme Contraseña:</label>
                <div class="col-md-6">
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirme nueva contraseña">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6 col-md-offset-5">
                    {!! Form::button('<i class="fa fa-check fa-lg"></i> '.$boton, array('class' => 'btn btn-primary', 'id' => 'btnGuardar', 'onclick' => 'cambiarpassword(\''.$entidad.'\', this)')) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<script>
	function cambiarpassword (entidad, idboton) {
		var idformulario = IDFORMMANTENIMIENTO + entidad;
		var data         = submitForm(idformulario);
		var respuesta    = '';
		var listar       = 'NO';
		if ($(idformulario + ' :input[id = "listar"]').length) {
			var listar = $(idformulario + ' :input[id = "listar"]').val()
		};
		var btn = $(idboton);
		btn.button('loading');
		data.done(function(msg) {
			respuesta = msg;
		}).fail(function(xhr, textStatus, errorThrown) {
			respuesta = 'ERROR';
		}).always(function() {
			btn.button('reset');
			var divError ='#divMensajeError' + entidad;
			var divMensaje = $(divError);
			if(respuesta === 'ERROR'){
                var cadenaError = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>La contraseña actual ingresada no es la correcta!!</strong></div>';
				divMensaje.html(cadenaError);
				divMensaje.show('slow');
			}else{
				if (respuesta === 'OK') {
                    var cadenaExito = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Contraseña cambiada satisfactoriamente!!</strong></div>';
					divMensaje.html(cadenaExito);
					divMensaje.show('slow');
					var grilla ="#";
					$(idformulario).find(':input').each(function() {
						var elemento         = this;
						var cadena           = idformulario + " :input[id='" + elemento.id + "']";
						var elementoValidado = $(cadena);
						elementoValidado.parent().removeClass('has-error');
					});
                }else if(respuesta === "IGUAL"){
                    var cadenaError = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><strong>Tu nueva contraseña no puede ser igual a la anterior!!</strong></div>';
                    divMensaje.html(cadenaError);
                    divMensaje.show('slow');
				
				} else {
					mostrarErrores(respuesta, idformulario, entidad);
				}
			}
		});
	}
</script>