<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Credito;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CreditoController extends Controller
{

    protected $folderview      = 'app.credito';
    protected $tituloAdmin     = 'Credito';
    protected $tituloRegistrar = 'Registrar credito';
    protected $tituloModificar = 'Modificar credito';
    protected $tituloEliminar  = 'Eliminar credito';
    protected $rutas           = array('create' => 'creditos.create', 
            'edit'     => 'creditos.edit', 
            'delete'   => 'creditos.eliminar',
            'search'   => 'creditos.buscar',
            'index'    => 'creditos.index',
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Credito';
        $fecha             = Libreria::getParam($request->input('fecha'));
        $estado             = Libreria::getParam($request->input('estado'));
        $resultado        = Credito::listar($fecha, $estado);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto s/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cuotas', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
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
        $entidad          = 'Credito';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboEstado        = array(0=>'Pendientes', 1 => 'Cancelados');
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboEstado' ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $entidad      = 'Credito';
        $credito  = null;
        $formData     = array('creditos.store');
        $formData     = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton        = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('credito', 'formData', 'entidad', 'boton', 'listar'));
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
        //$reglas     = array('name' => 'required|max:100');
        //$mensajes   = array();
        //$validacion = Validator::make($request->all(), $reglas, $mensajes);
       /* if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        */

        $error = DB::transaction(function() use($request){
            $credito       = new Credito();
            $credito->valor_credito = $request->input('valor_credito');
            $credito->cantidad_cuotas = $request->input('cantidad_cuotas');
            $credito->comision = $request->input('comision');
            $credito->multa = 20;
            //$fecha =Libreria::getParam($request->input('fecha'));
            $credito->fecha = $request->input('fecha');
            $credito->estado = '0';
            $credito->persona_id = 1; //$request->input('idpersona');
            //$credito->socioaval_id = $request->input('socioaval_id');
            $credito->save();
            /*
            for( $i=0; $i< count($credito->cantidad_cuotas); $i++){

                $detalle_cuotas  = new Detalle_Cuotas();
                $detalle_cuotas ->credito_id = (int)$credito->id;
                $detalle_cuotas->capital = 
                $detalle_cuotas->interes =
                $detalle_cuotas->fecha_pago = Date::createFromFormat('d/m/Y', '')->format('Y-m-d');
                $detalle_cuotas->situacion = '0';
                $detalle_cuotas->save();
            $employee->birthdate     = Date::createFromFormat('d/m/Y', $request->input('birthdate'))->format('Y-m-d');
            
        }*/




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
    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $credito = Credito::find($id);
        $entidad  = 'Credito';
        $formData = array('credito.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('credito', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
       /* $reglas     = array('name' => 'required|max:100');
        $mensajes   = array();
        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } */
        $error = DB::transaction(function() use($request, $id){
            $credito       = Credito::find($id);
            
            $credito->save();
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
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $credito = Credito::find($id);
            $credito->delete();
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
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Credito::find($id);
        $entidad  = 'Credito';
        $formData = array('route' => array('credito.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
