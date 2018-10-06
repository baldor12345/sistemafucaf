@include('auth.header')
<div class="wrapper-page">
    <div class="card-box">
        <div class="text-center">
            <a href="#" class="logo-lg"><i class="md md-equalizer"></i> <span>SISTEMA FUCAF</span> </a>
        </div>
        <form action="{{ url('/login') }}" method="post" class="form-horizontal m-t-20">
            {{ csrf_field() }}
            @if (count($errors) > 0)
            <div class="form-group has-error has-feedback">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li class="text-red">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="form-group">
                <div class="col-xs-12">
                    <input name="login" class="form-control" type="text" required="" placeholder="Usuario">
                    <i class="md md-account-circle form-control-feedback l-h-34"></i>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <input name="password" class="form-control" type="password" required="" placeholder="Password">
                    <i class="md md-vpn-key form-control-feedback l-h-34"></i>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="checkbox checkbox-primary">
                        <input name="remember" id="checkbox-signup" type="checkbox">
                        <label for="checkbox-signup">
                            Recordarme
                        </label>
                    </div>

                </div>
            </div>
            <div class="form-group text-right m-t-20">
                <div class="col-xs-12">
                    <button class="btn btn-primary btn-custom w-md waves-effect waves-light btn-block" type="submit">Ingresar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@include('auth.footer')