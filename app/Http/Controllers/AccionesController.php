<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Acciones;
use App\Persona;
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AccionesController extends Controller
{
    protected $folderview      = 'app.acciones';
    protected $tituloAdmin     = 'Acciones';
    protected $tituloRegistrar = 'Registrar acciones';
    protected $tituloModificar = 'Modificar acciones';
    protected $tituloEliminar  = 'Eliminar acciones';
    protected $rutas           = array('create' => 'acciones.create', 
            'edit'   => 'acciones.edit', 
            'delete' => 'acciones.eliminar',
            'search' => 'acciones.buscar',
            'index'  => 'acciones.index',
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
        $entidad          = 'Acciones';
        $codigo             = Libreria::getParam($request->input('codigo'));
        $nombres             = Libreria::getParam($request->input('nombres'));
        $dni             = Libreria::getParam($request->input('dni'));
        $resultado        = Acciones::listar($codigo, $nombres, $dni);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'NOMBRES', 'numero' => '1');
        $cabecera[]       = array('valor' => 'ACCIONES', 'numero' => '1');
        $cabecera[]       = array('valor' => 'PRECIO ACCION', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '1');
        
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
        $entidad          = 'Acciones';
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
        $cboEstado        = [''=>'Seleccione']+ array('C'=>'Compra','V'=>'Venta' );
        $cboPersona = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboConfiguraciones = array('' => 'Seleccione') + Configuraciones::pluck('precio_accion', 'id')->all();
        $entidad        = 'Acciones';
        $acciones        = null;
        $formData       = array('acciones.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('acciones', 'formData', 'entidad', 'boton', 'listar','cboEstado','cboConfiguraciones'));
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
            'cadenaAcciones'      => 'required|max:100',
            'persona_id'         => 'required|max:20|unique:acciones,persona_id,NULL,id,deleted_at,NULL',
            'configuraciones_id'        => 'required|max:100'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        //evaluando los datos que vienen del view
        $error = DB::transaction(function() use($request){
            if($request->input('cadenaAcciones') !==''){
                $valores = explode(",", $request->input('cadenaAcciones'));
                for( $i=0; $i< count($valores); $i++){
                    $accion=  explode(":", $valores[$i]);
                    for( $j=1; $j< $accion[0]+1; $j++){
                        $acciones               = new Acciones();    
                        $acciones->cantidad_acciones        =$j;
                        $acciones->estado        = $accion[1];
                        $acciones->fecha        = $accion[2];
                        $acciones->persona_id        = $request->input('persona_id');
                        $acciones->configuraciones_id        = $request->input('configuraciones_id');
                        $acciones->save();
                    }
                }
    
            }
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
        $existe = Libreria::verificarExistencia($id, 'acciones');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $cboEstado        = [''=>'Seleccione']+ array('C'=>'Compra','V'=>'Venta' );
        $cboPersona = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboConfiguraciones = array('' => 'Seleccione') + Configuraciones::pluck('precio_accion', 'id')->all();
        $acciones        = Acciones::find($id);
        $entidad        = 'Acciones';
        $formData       = array('acciones.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('acciones', 'formData', 'entidad', 'boton', 'listar', 'cboEstado','cboPersona','cboConfiguraciones'));
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
        $existe = Libreria::verificarExistencia($id, 'acciones');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'persona_id'       => 'required|max:20|unique:acciones,persona_id,'.$id.',id,deleted_at,NULL',
            'configuraciones_id'       => 'required|max:20|unique:acciones,configuraciones_id,'.$id.',id,deleted_at,NULL',
            'cadenaAcciones'      => 'required|max:100'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            
            if($request->input('cadenaAcciones') !==''){
                $valores = explode(",", $request->input('cadenaAcciones'));
                for( $i=0; $i< count($valores); $i++){
                    $accion=  explode(":", $valores[$i]);
                    for( $j=1; $j< $accion[0]+1; $j++){
                        $acciones                 = Acciones::find($id);  
                        $acciones->cantidad_acciones        =$j;
                        $acciones->estado        = $accion[1];
                        $acciones->fecha        = $accion[2];
                        $acciones->persona_id        = $request->input('persona_id');
                        $acciones->configuraciones_id        = $request->input('configuraciones_id');
                        
                        $acciones->save();
                    }
                }
    
            }
            
        });
        return is_null($error) ? "OK" : $error;
    }

    //listar el objeto persona por dni
    public function getPersona(Request $request, $dni){
        if($request->ajax()){
            $personas = Persona::personas($dni);
            return response()->json($personas);
        }
    }
    //listar la cantidad de acciones acumuladas 
    public function getListCantAcciones(Request $request, $persona_id){
        if($request->ajax()){
            $CantAcciones = Acciones::cant_acciones_acumuladas($persona_id);
            return response()->json($CantAcciones);
        }
    }
}
