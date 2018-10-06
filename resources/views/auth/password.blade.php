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