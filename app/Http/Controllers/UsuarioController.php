<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Hash;
use Validator;
use App\Http\Requests;
use App\User;
use App\Caja;
use App\Transaccion;
use App\Persona;
use App\Directivos;
use App\Usertype;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class UsuarioController extends Controller
{
    protected $folderview      = 'app.usuario';
    protected $tituloAdmin     = 'Usuario';
    protected $tituloRegistrar = 'Registrar usuario';
    protected $tituloModificar = 'Modificar usuario';
    protected $titulo_bitacora = 'Generar Reporte Bitacora';
    protected $tituloEliminar  = 'Eliminar usuario';
    protected $rutas           = array('create' => 'usuario.create', 
            'edit'   => 'usuario.edit', 
            'delete' => 'usuario.eliminar',
            'search' => 'usuario.buscar',
            'index'  => 'usuario.index',

            'cargarbinnacle'  => 'usuario.cargarbinnacle',
            'generarreporte'  => 'usuario.generarreporte',
            'binnaclePDF' => 'usuario.binnaclePDF',
            'listpersonas'  =>'usuario.listpersonas'
        );

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Usuario';
        $name             = Libreria::getParam($request->input('nombre'));
        $resultado        = User::listar($name);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Login', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Telefono', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Email', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo de usuario', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_bitacora = $this->titulo_bitacora;
        $ruta             = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta','titulo_bitacora'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entidad          = 'Usuario';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $titulo_bitacora =  $this->titulo_bitacora;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','titulo_bitacora'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Usuario';
        $usuario        = null;
        $cboPersona = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboPer = array(0=>'Seleccione ...');
        $cboTipousuario = array('' => 'Seleccione') + Usertype::pluck('name', 'id')->all();
        $cboEstado        = array('A'=>'Activo','I'=>'Inactivo');
        $formData       = array('usuario.store');
        $ruta = $this->rutas;
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('usuario', 'formData', 'entidad', 'boton', 'listar', 'cboTipousuario','cboPersona','cboEstado','cboPer','ruta'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas = array(
            'login'       => 'required|max:20|unique:user,login,NULL,login,deleted_at,NULL',
            'password'    => 'required|max:20',
            'fechai'    => 'required',
            'persona_id' => 'required|integer',
            'usertype_id' => 'required|integer|exists:user,usertype_id,deleted_at,NULL'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $usuario               = new User();
            $usuario->persona_id       = $request->input('persona_id');
            $usuario->login        = $request->input('login');
            $usuario->password     = Hash::make($request->input('password'));
            $usuario->fechai       = $request->input('fechai');
            $usuario->fechaf       = $request->input('fechaf');
            $usuario->estado       = $request->input('estado');
            $usuario->usertype_id  = $request->input('usertype_id');
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $cboPer = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboTipousuario = array('' => 'Seleccione') + Usertype::pluck('name', 'id')->all();
        $cboEstado        = array('A'=>'Activo','I'=>'Inactivo');
        $usuario        = User::find($id);
        $entidad        = 'Usuario';
        $ruta = $this->rutas;
        $formData       = array('usuario.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('usuario', 'formData', 'entidad', 'boton', 'listar', 'cboTipousuario','cboPer','cboEstado','ruta'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'login'       => 'required|max:20',
            'password'    => 'required|max:20',
            'fechai'    => 'required',
            'persona_id' => 'required|integer',
            'usertype_id' => 'required|integer|exists:user,usertype_id,deleted_at,NULL'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $usuario                 = User::find($id);
            $usuario->persona_id       = $request->input('persona_id');
            $usuario->login        = $request->input('login');
            if ($request->input('password') != null && $request->input('password') != '') {
                $usuario->password = Hash::make($request->input('password'));
            }
            $usuario->password     = Hash::make($request->input('password'));
            $usuario->fechai       = $request->input('fechai');
            $usuario->fechaf       = $request->input('fechaf');
            $usuario->estado       = $request->input('estado');
            $usuario->usertype_id = $request->input('usertype_id');
            $usuario->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $error = null;
        $user_consult = User::find($id);
        $count_result = Caja::where('persona_id',$user_consult->persona_id)->count();
        if($count_result == 0){
            $error = DB::transaction(function() use($id){
                $usuario = User::find($id);
                $usuario->delete();
            });
        }else{
            $error = "No se puede completar la accion, ya existe una relacion con otros registros este usuario!";
        }
        
        return is_null($error) ? "OK" : $error;
    }

    /**
     * Función para confirmar la eliminación de un registrlo
     * @param  integer $id          id del registro a intentar eliminar
     * @param  string $listarLuego consultar si luego de eliminar se listará
     * @return html              se retorna html, con la ventana de confirmar eliminar
     */
    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'user');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $error = null;
        $user_consult = User::find($id);
        $count_result = Caja::where('persona_id',$user_consult->persona_id)->count();
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = User::find($id);
        $entidad  = 'Usuario';
        $boton    = 'Eliminar';
        if($count_result == 0){
            $formData = array('route' => array('usuario.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
            return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
        }else{
            return view($this->folderview.'.message')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
        }

    }

    /**CARGAR REPORTE */
    public function cargarbinnacle(){
        $entidad  = 'Usuario';
        $ruta = $this->rutas;
        $titulo_bitacora = $this->titulo_bitacora;
        return view($this->folderview.'.cargarbinnacle')->with(compact('entidad', 'ruta', 'titulo_bitacora'));
    }

    /**GENERAR REPORTES DE CAJA Y EGRESOS E INGRESOS DEL MES */
    public function generarreporte(Request $request)
    {
        $res = null;
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');
        $res = is_null($res) ? "OK" : $res;
        $respuesta = array($res, $desde, $hasta);
        return $respuesta;
    }

    public function binnaclePDF($desde, $hasta, Request $request)
    {    

        $binnacle        = User::listBinnacle($desde, $hasta);
        $lista           = $binnacle->get();
        $titulo = 'bitacora_'.$desde;
        $view = \View::make('app.usuario.binnaclePDF')->with(compact('lista','desde','hasta'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        //PDF::SetLeftMargin(40);
        //PDF::SetRightMargin(110);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

    public function listpersonas(Request $request){
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $tags = Persona::where("dni",'ILIKE', '%'.$term.'%')->orwhere("nombres",'ILIKE', '%'.$term.'%')->orwhere("apellidos",'ILIKE', '%'.$term.'%')->limit(5)->get();
        $list_directivos = Directivos::where('estado','A')->get();
        $formatted_tags = [];
        foreach ($tags as $tag) {
            foreach($list_directivos as $value){
                if(trim($tag->tipo) == 'S'){
                    if(($tag->id == $value->presidente_id) || ($tag->id == $value->secretario_id) || ($tag->id == $value->tesorero_id) || ($tag->id == $value->vocal_id)){
                        $formatted_tags[] = ['id' => $tag->id, 'text' => $tag->nombres." ".$tag->apellidos];
                    }
                }else{
                    //$formatted_tags[] = ['id'=> '', 'text'=>"seleccione socio"];
                }
            }
            
        }

        return \Response::json($formatted_tags);
    }

}
