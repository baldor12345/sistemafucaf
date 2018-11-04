<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Ahorros;
use App\Concepto;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AhorrosController extends Controller
{
    protected $folderview      = 'app.ahorros';
    protected $tituloAdmin     = 'Ahorros';
    protected $tituloRegistrar = 'Registrar ahorro';
    protected $tituloModificar = 'Modificar ahorro';
    protected $tituloEliminar  = 'Eliminar ahorro';
    protected $rutas           = array('create' => 'ahorros.create', 
            'edit'   => 'ahorros.edit', 
            'delete' => 'ahorros.eliminar',
            'search' => 'ahorros.buscar',
            'index'  => 'ahorros.index',
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
        $entidad          = 'Ahorros';
        $fecha             = Libreria::getParam($request->input('fecha'));
        $nombres             = Libreria::getParam($request->input('nombres'));
        $resultado        = Ahorros::listar($nombres, $fecha);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Periodo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
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
        $entidad          = 'Ahorros';
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
        $entidad        = 'Ahorros';
        $ahorros        = null;
        $resultado      = Concepto::listar('I');
        $cboConcepto    = $resultado->get();
        $formData       = array('ahorros.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('ahorros', 'formData', 'entidad', 'boton', 'listar','cboConcepto'));
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
            'importe'         => 'required|max:20',
            'periodo'        => 'required|max:10',
            'fecha_inicio'      => 'required|max:100',
            'interes'    => 'required|max:100',
            'persona_id'    => 'required|max:100',
            );

        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $error = DB::transaction(function() use($request){
            $ahorros               = new Ahorros();

            $ahorros->importe = $request->input('importe');
            $ahorros->periodo = $request->input('periodo');
            $ahorros->fecha_inicio = $request->input('fecha_inicio');
            $ahorros->fecha_fin = $request->input('fecha_fin');
            $ahorros->interes = $request->input('interes');
            $ahorros->persona_id = $request->input('persona_id');
            $ahorros->descripcion = $request->input('descripcion');
            $ahorros->save();
            //Guardar en tabla transacciones **********
            $caja = Caja::where("estado","=","A")->get();
            $fechahora_actual = date("Y-m-d H:i:s");
            $idconcepto = $request->input('concepto');

            $transaccion = new Transaccion();
            $transaccion->fecha = $ahorros->fecha_inicio;
            $transaccion->monto = $ahorros->importe;
            $transaccion->id_tabla = $ahorros->id;
            $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
            $transaccion->concepto_id = $idconcepto;
            $transaccion->descripcion = $ahorros->descripcion;
            $transaccion->persona_id = $ahorros->persona_id;
            $transaccion->usuario_id = Credito::idUser();
            $transaccion->caja_id =  $caja[0]->id;
            $transaccion->save();

            

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
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $ahorros        = Ahorros::find($id);
        $entidad        = 'Ahorros';
        $formData       = array('ahorros.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('ahorros', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $listar     = Libreria::getParam($request->input('listar'), 'NO');
        $reglas = array(
            'importe'         => 'required|max:20',
            'periodo'        => 'required|max:10',
            'fecha_inicio'      => 'required|max:100',
            'fecha_fin'            => 'required',
            'interes'    => 'required|max:100',
            'persona_id'    => 'required|max:100',
            );

        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request, $id){
            $ahorros                 = Ahorros::find($id);
            $ahorros->importe = $request->input('importe');
            $ahorros->periodo = $request->input('periodo');
            $ahorros->fecha_inicio = $request->input('fecha_inicio');
            $ahorros->fecha_fin = $request->input('fecha_fin');
            $ahorros->interes = $request->input('interes');
            $ahorros->persona_id = $request->input('persona_id');
            $ahorros->descripcion = $request->input('descripcion');
            $ahorros->save();
            /// REGISTRO EN CAJA
            $idconcepto = $request->input('concepto');

            $transaccion = Transaccion::getTransaccion($ahorros->id,'AH');
            $transaccion->monto = $ahorros->importe;
            $transaccion->id_tabla = $ahorros->id;
            $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
            $transaccion->concepto_id = $idconcepto;
            $transaccion->descripcion = $ahorros->descripcion;
            $transaccion->persona_id = $ahorros->persona_id;
            $transaccion->save();
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
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $ahorros = Ahorros::find($id);
            $ahorros->delete();
            $transaccion = Transaccion::getTransaccion($ahorros->id,'AH');
            $transaccion->delete();
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
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Ahorros::find($id);
        $entidad  = 'Ahorros';
        $formData = array('route' => array('ahorros.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
