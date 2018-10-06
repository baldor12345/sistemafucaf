<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Librerias\Libreria;
use App\User;

class UpdatePasswordController extends Controller
{
    protected $folderview      = 'app.cambiarpassword';
    protected $tituloAdmin     = 'Cambiar Contraseña';
    protected $rutas           = array(
        'update'   => 'updatepassword.update',
        'index'  => 'updatepassword.index',
    );

    public function index(Request $request){
        $title            = $this->tituloAdmin;
        $ruta             = $this->rutas;
        $entidad          = 'usuario';
        $user           = Auth::user();
        $existe = Libreria::verificarExistencia($user->id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $formData       = array('updatepassword.update', $user->id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal' , 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Cambiar Contraseña';
        return view($this->folderview.'.password')->with(compact('user','formData','listar','title','entidad','ruta','boton'));
    }   

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id){
        $error = null;
        $success = "";
        $rules = [
            'mypassword' => 'required',
            'password' => 'required|confirmed|min:6|max:18',
        ];
        
        $messages = [
            'mypassword.required' => 'El campo contraseña actual es requerido!!',
            'password.required' => 'El campo nueva contraseña es requerido!!',
            'password.confirmed' => 'Las contraseñas no coinciden!!',
            'password.min' => 'El mínimo permitido son 6 caracteres!!',
            'password.max' => 'El máximo permitido son 18 caracteres!!',
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()){
            return $validator->messages()->toJson();
        }
        else{
            if (Hash::check($request->mypassword, Auth::user()->password) && !Hash::check($request->password, Auth::user()->password) ){
                $error = DB::transaction(function() use($request, $id){
                    $usuario           = User::find($id);
                    $usuario->password = bcrypt($request->password);
                   // $usuario->password = bcrypt($request->get('new-password'));
                    $usuario->save();
                });
                return is_null($error) ? "OK" : $error;
            }
            else if(Hash::check($request->password, Auth::user()->password))
            {
                $error =  'IGUAL';
                return $error;
            }
            else
            {
                $error =  'ERROR';
                return $error;
            }
        }
    }

}
