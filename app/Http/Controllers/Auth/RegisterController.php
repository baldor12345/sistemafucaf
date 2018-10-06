<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Empresa;
use App\Personamaestro;
use App\Persona;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = 'dashboard';
    protected $redirectPath = 'dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(){
        return view('auth.registrar');
    }

    public function register(Request $request)
    {

       //Validates data
        $this->validator($request->all())->validate();

       //Create user
        $user = $this->create($request->all());

        //Authenticates user
        $this->guard()->login($user);

       //Redirects sellers
        return redirect($this->redirectPath);
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'ruc'       => 'required|numeric|digits:11|unique:empresa',
            'razonsocial'    => 'required|string|max:200|unique:empresa',
            'emailempresa' => 'required|email|max:255|unique:empresa',
            'dni'       =>   'required|numeric|digits:8|unique:personamaestro',
            'nombres'  => 'required',
            'apellidos'  => 'required',
            'departamento'  => 'required',
            'provincia'  => 'required',
            'distrito'  => 'required',
            'fechanacimiento'  => 'required',
            'telefono'   => 'required|numeric|digits:9',
            'email' => 'required|email|max:255|unique:personamaestro',
            'password' => 'required|min:6|max:18|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        DB::transaction(function() use($data){
            $personamaestro                   = new Personamaestro();
            $personamaestro->dni              = $data['dni'];
            $personamaestro->nombres          = $data['nombres'];
            $personamaestro->apellidos        = $data['apellidos'];
            $personamaestro->distrito_id      = $data['distrito'];
            $personamaestro->fechanacimiento  = $data['fechanacimiento'];
            $personamaestro->celular          = $data['telefono'];
            $personamaestro->email            = $data['email'];
            $personamaestro->type             = 'E';
            $personamaestro->secondtype       = 'C';
            $personamaestro->save();

            $contacto = DB::table('personamaestro')->where('dni', '=', $data['dni'])->first();
            
            $empresa               = new Empresa();
            $empresa->ruc          = $data['ruc'];
            $empresa->razonsocial  = $data['razonsocial'];
            $empresa->emailempresa = $data['emailempresa'];
            $empresa->contacto_id  = $contacto->id;
            $empresa->save();

            $empresa1 = DB::table('empresa')->where('ruc', '=', $data['ruc'])->first();

            $persona                     = new Persona();
            $persona->empresa_id         = $empresa1->id;
            $persona->personamaestro_id  = $contacto->id;
            $persona->save();
        });

        $contactoo = DB::table('personamaestro')->where('dni', '=', $data['dni'])->first();
        $empresaa = DB::table('empresa')->where('ruc', '=', $data['ruc'])->first();
        $personaa = DB::table('persona')->where([
            ['personamaestro_id', '=', $contactoo->id],
            ['empresa_id', '=', $empresaa->id],
            ])->first();

        $usertype_id = 2;

        return User::create([
            'login' => $data['dni'],
            'password' => bcrypt($data['password']),
            'email' => $data['email'],
            'usertype_id' => $usertype_id,
            'persona_id' => $personaa->id ,
            'empresa_id' => $empresaa->id ,
            'state' => 'H' ,
        ]);
    }

    protected function guard()
   {
       return Auth::guard('web');
   }
}
