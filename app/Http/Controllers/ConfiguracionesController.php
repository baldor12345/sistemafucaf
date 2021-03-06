<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Configuraciones;
use App\Persona;
use App\Caja;
use App\Acciones;
use App\Ahorros;
use App\Credito;
use App\Cuota;
use App\Concepto;
use App\ControlPersona;
use App\Transaccion;
use App\Http\Requests;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ConfiguracionesController extends Controller
{
    protected $folderview      = 'app.configuraciones';
    protected $tituloAdmin     = 'Configuraciones';
    protected $tituloRegistrar = 'Registrar configuraciones';
    protected $tituloModificar = 'Modificar configuraciones';
    protected $tituloEliminar  = 'Eliminar configuraciones';
    protected $rutas           = array('create' => 'configuraciones.create', 
            'edit'   => 'configuraciones.edit', 
            'delete' => 'configuraciones.eliminar',
            'search' => 'configuraciones.buscar',
            'index'  => 'configuraciones.index',
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
        $entidad          = 'Configuraciones';
        $codigo             = Libreria::getParam($request->input('codigo'));
        $resultado        = Configuraciones::listar($codigo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Precio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Limite', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Interes', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Mora', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Interes', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripcion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
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
        $entidad          = 'Configuraciones';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Configuraciones';
        $configuraciones        = null;
        $formData       = array('configuraciones.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('configuraciones', 'formData', 'entidad', 'boton', 'listar'));
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
            'precio_accion'         => 'required',
            'limite_acciones'      => 'required',
            'valor_recibo'      => 'required',
            'fecha'      => 'required',
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $configuraciones = new Configuraciones();
            $configuraciones->codigo = $request->input('codigo');
            $configuraciones->precio_accion = $request->input('precio_accion');
            $configuraciones->valor_recibo = $request->input('valor_recibo');
            $configuraciones->taza_mora = $request->input('taza_mora');
            $configuraciones->ganancia_accion = 0.0;
            $configuraciones->limite_acciones = $request->input('limite_acciones');

            $configuraciones->tasa_interes_credito = $request->input('tasa_interes_credito');
            $configuraciones->tasa_interes_multa = $request->input('tasa_interes_multa');
            $configuraciones->tasa_interes_ahorro = $request->input('tasa_interes_ahorro');

            $configuraciones->fecha = $request->input('fecha');
            $configuraciones->descripcion = $request->input('descripcion');
            $configuraciones->save();
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
        $existe = Libreria::verificarExistencia($id, 'configuraciones');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $configuraciones        = Configuraciones::find($id);
        $entidad        = 'Configuraciones';
        $formData       = array('configuraciones.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('configuraciones', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'configuraciones');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'precio_accion'         => 'required',
            'limite_acciones'      => 'required',
            'fecha'      => 'required',
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $configuraciones = Configuraciones::find($id);
            $configuraciones->codigo = $request->input('codigo');
            $configuraciones->precio_accion = $request->input('precio_accion');
            $configuraciones->valor_recibo = $request->input('valor_recibo');
            $configuraciones->taza_mora = $request->input('taza_mora');
            $configuraciones->ganancia_accion = 0.0;
            $configuraciones->limite_acciones = $request->input('limite_acciones');
            $configuraciones->tasa_interes_credito = $request->input('tasa_interes_credito');
            $configuraciones->tasa_interes_multa = $request->input('tasa_interes_multa');
            $configuraciones->tasa_interes_ahorro = $request->input('tasa_interes_ahorro');
            $configuraciones->fecha = $request->input('fecha');
            $configuraciones->descripcion = $request->input('descripcion');
            $configuraciones->save();
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
        $existe = Libreria::verificarExistencia($id, 'configuraciones');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $configuraciones = Configuraciones::find($id);
            $configuraciones->delete();
        });
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
        $existe = Libreria::verificarExistencia($id, 'configuraciones');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $count_acciones = Acciones::where('configuraciones_id',$id)->count();

        $modelo   = Configuraciones::find($id);
        $entidad  = 'Configuraciones';
        if($count_acciones == 0){
            $formData = array('route' => array('configuraciones.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
            $boton    = 'Eliminar';
            return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
        }else{
            return view($this->folderview.'.messageconfig')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
        }
    }
}
