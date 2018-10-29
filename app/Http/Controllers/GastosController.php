<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Gastos;
use App\Transaccion;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class GastosController extends Controller
{
    protected $folderview      = 'app.gastos';
    protected $tituloAdmin     = 'Gastos';
    protected $tituloRegistrar = 'Registrar gasto';
    protected $tituloModificar = 'Modificar gasto';
    protected $tituloEliminar  = 'Eliminar gasto';
    protected $rutas           = array('create' => 'gastos.create', 
            'edit'   => 'gastos.edit', 
            'delete' => 'gastos.eliminar',
            'search' => 'gastos.buscar',
            'index'  => 'gastos.index',
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
        $entidad          = 'Gastos';
        $fecha             = Libreria::getParam($request->input('fecha'));
        $resultado        = Gastos::listar($fecha);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Concepto', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
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
        $entidad          = 'Gastos';
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
        $entidad        = 'Gastos';
        $gastos        = null;
        $formData       = array('gastos.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('gastos', 'formData', 'entidad', 'boton', 'listar'));
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
       /* $reglas = array(
            'codigo'         => 'required|max:20|unique:persona,codigo,NULL,id,deleted_at,NULL',
            'nombres'        => 'required|max:100',
            'apellidos'      => 'required|max:100',
            'dni'            => 'required|unique:persona,dni,NULL,id,deleted_at,NULL|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
            'fecha_nacimiento'    => 'required|max:100',
            'sexo'    => 'required|max:100',
            'ocupacion'    => 'required|max:100',
            'tipo'    => 'required|max:5',
            'direccion'    => 'required|max:100',
            'ingreso_personal'    => 'required|max:20',
            'ingreso_familiar'    => 'required|max:20',
            'telefono_fijo'     => 'required|max:15|regex:/^[0-9]+?-?[0-9]+$/',
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }*/
        $error = DB::transaction(function() use($request){
            $gastos               = new Gastos();

            $gastos->monto = $request->input('monto');
            $gastos->concepto = $request->input('concepto');
            $gastos->fecha = $request->input('fechag');
            $gastos->save();

            //Guardar en tabla transacciones **********
            $fechahora_actual = date("Y-m-d H:i:s");
            $transaccion = new Transaccion();
            $transaccion->gastos_id = $gastos->id;
            $transaccion->fecha = $fechahora_actual;
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
        $existe = Libreria::verificarExistencia($id, 'gastos');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        
        $gastos        = Gastos::find($id);
        $entidad        = 'Gastos';
        $formData       = array('gastos.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('gastos', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'gastos');
        if ($existe !== true) {
            return $existe;
        }
      /*  $reglas = array(
            'codigo'       => 'required|max:20|unique:persona,codigo,'.$id.',id,deleted_at,NULL',
            'dni'       => 'required|max:20|unique:persona,dni,'.$id.',id,deleted_at,NULL',
            'nombres'    => 'required|max:100',
            'apellidos'    => 'required|max:100',
            'fecha_nacimiento'    => 'required|max:100',
            'sexo'    => 'required|max:100',
            'ocupacion'    => 'required|max:100',
            'tipo'    => 'required|max:5',
            'direccion'    => 'required|max:100',
            'ingreso_personal'    => 'required|max:20',
            'ingreso_familiar'    => 'required|max:20',
            'telefono_fijo'    => 'required|max:15',
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } */
        $error = DB::transaction(function() use($request, $id){
            $gastos = Gastos::find($id);

            $agstos->monto = $request->input('monto');
            $gastos->concepto = $request->input('concepto');
            $gastos->fecha = $request->input('fechag');
            $gastos->save();

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
        $existe = Libreria::verificarExistencia($id, 'gastos');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $resultado = Transaccion::obtenerid($id);
            $transacc = $resultado[0];
            $transaccion = Transaccion::find($transacc->id);
            $transaccion->delete();

            $gastos = Gastos::find($id);
            $gastos->delete();
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
        $existe = Libreria::verificarExistencia($id, 'gastos');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Gastos::find($id);
        $entidad  = 'Gastos';
        $formData = array('route' => array('gastos.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
