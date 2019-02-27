<?php

namespace App\Http\Controllers;

use Validator;
use App\Http\Requests;
use App\Persona;
use App\Caja;
use App\Acciones;
use App\Ahorros;
use App\Credito;
use App\Cuota;
use App\Concepto;
use App\Transaccion;
use App\ControlPersona;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ConceptoController extends Controller
{
    protected $folderview      = 'app.concepto';
    protected $tituloAdmin     = 'Conceptos';
    protected $tituloRegistrar = 'Registrar concepto';
    protected $tituloModificar = 'Modificar concepto';
    protected $tituloEliminar  = 'Eliminar concepto';
    protected $rutas           = array('create' => 'concepto.create', 
            'edit'   => 'concepto.edit', 
            'delete' => 'concepto.eliminar',
            'search' => 'concepto.buscar',
            'index'  => 'concepto.index',
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
        $entidad          = 'Concepto';
        $tipo             = Libreria::getParam($request->input('tipo'));
        $resultado        = Concepto::listar($tipo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Titulo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Tipo', 'numero' => '1');
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
        $entidad          = 'Concepto';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboTipo        = [''=>'Todo']+ array('I'=>'Ingresos','E'=>'Egresos');
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad','cboTipo' ,'title', 'titulo_registrar', 'ruta'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Concepto';
        $concepto        = null;
        $formData       = array('concepto.store');
        $cboTipo        = [''=>'Seleccione']+ array('I'=>'Ingresos','E'=>'Egresos');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('concepto', 'cboTipo','formData', 'entidad', 'boton', 'listar'));
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
            'titulo'         => 'required',
            'tipo'         => 'required'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $concepto               = new Concepto();
            $concepto->titulo        = $request->input('titulo');
            $concepto->tipo        = $request->input('tipo');
            $concepto->save();
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
        $existe = Libreria::verificarExistencia($id, 'concepto');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $concepto        = Concepto::find($id);
        $entidad        = 'Concepto';
        $formData       = array('concepto.update', $id);
        $cboTipo        = [''=>'Seleccione']+ array('I'=>'Ingresos','E'=>'Egresos');
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('concepto','cboTipo', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'concepto');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'titulo'       => 'required|max:20|unique:concepto,titulo,'.$id.',id,deleted_at,NULL',
            'tipo'         => 'required'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $concepto                 = Concepto::find($id);
            $concepto->titulo        = $request->input('titulo');
            $concepto->tipo        = $request->input('tipo');
            $concepto->save();
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
        $existe = Libreria::verificarExistencia($id, 'concepto');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $concepto = Concepto::find($id);
            $concepto->delete();
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
        $existe = Libreria::verificarExistencia($id, 'concepto');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $count_acciones = Acciones::where('concepto_id',$id)->count();
        $count_transaccion = Transaccion::where('concepto_id',$id)->count();

        $modelo   = Concepto::find($id);
        $entidad  = 'Concepto';
        $boton    = 'Eliminar';
        if(($count_acciones==0) && ($count_transaccion == 0)){
            $formData = array('route' => array('concepto.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
            return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
        }else{
            return view($this->folderview.'.messageconcepto')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
        }
        
    }
}
