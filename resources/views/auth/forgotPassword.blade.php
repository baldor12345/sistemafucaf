@include('auth.header')
<div class="wrapper-page">
    <div class="card-box">
        <div class="text-center">
            <a class="logo-lg"><i class="md md-equalizer"></i> <span>Restablecer Contraseña</span> </a>
        </div>
        <div class="panel-body">
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/password/email') }}">
                {{ csrf_field() }}

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <div class="col-md-12">
                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Correo Electrónico" required>
                        <i class="md md-mail form-control-feedback l-h-34"></i>

                        @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="form-group" style="text-align: center">
                    <div class="col-md-12">
                        <button class="btn btn-primary btn-custom w-md waves-effect waves-light" type="submit">
                            Restablecer Contraseña
                        </button>
                    </div>
                </div>
            </form>
            <div class="form-group" style="text-align: center">
                <div class="col-md-12">
                    <a href="{{ url('/login') }}" class="btn btn-success btn-custom w-md waves-effect waves-light">
                        Iniciar Sesión
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@include('auth.footer')