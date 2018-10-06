<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Persona;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    protected $folderview      = 'app.persona';
    protected $tituloAdmin     = 'Persona';
    protected $tituloRegistrar = 'Registrar persona';
    protected $tituloModificar = 'Modificar persona';
    protected $tituloEliminar  = 'Eliminar persona';
    protected $rutas           = array('create' => 'persona.create', 
            'edit'   => 'persona.edit', 
            'delete' => 'persona.eliminar',
            'search' => 'persona.buscar',
            'index'  => 'persona.index',
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
        $entidad          = 'Persona';
        $codigo             = Libreria::getParam($request->input('codigo'));
        $nombres             = Libreria::getParam($request->input('nombres'));
        $dni             = Libreria::getParam($request->input('dni'));
        $tipo             = Libreria::getParam($request->input('tipo'));
        $resultado        = Persona::listar($codigo, $nombres, $dni, $tipo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de Ingreso', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Direccion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Telefono', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Email', 'numero' => '1');
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
        $entidad          = 'Persona';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboTipo        = [''=>'Todo']+ array('S'=>'Socio','C'=>'Cliente' ,'SC' => 'Socio/Cliente');
        $cboSexo        = [''=>'Seleccione']+ array('M'=>'Masculino','F' => 'Femenino');
        $cboEstadoCivil        = [''=>'Seleccione']+ array('S'=>'Soltero','C' => 'Casado', 'V' => 'Viudo');
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboTipo', 'cboSexo','cboEstadoCivil'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Persona';
        $persona        = null;
        $cboTipo        = [''=>'Seleccione']+ array('S'=>'Socio','C'=>'Cliente' ,'SC' => 'Socio/Cliente');
        $cboSexo        = [''=>'Seleccione']+ array('M'=>'Masculino','F' => 'Femenino');
        $cboEstadoCivil        = [''=>'Seleccione']+ array('S'=>'Soltero','C' => 'Casado', 'V' => 'Viudo');
        $formData       = array('persona.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('persona', 'formData', 'entidad', 'boton', 'listar', 'cboTipo','cboSexo','cboEstadoCivil'));
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
        }
        $error = DB::transaction(function() use($request){
            $perosna               = new Persona();
            $perosna->codigo        = $request->input('codigo');
            $perosna->dni        = $request->input('dni');
            $perosna->nombres        = $request->input('nombres');
            $perosna->apellidos        = $request->input('apellidos');
            $perosna->tipo        = $request->input('tipo');
            $perosna->fechai        = $request->input('fechai');
            $perosna->direccion        = $request->input('direccion');
            $perosna->fecha_nacimiento        = $request->input('fecha_nacimiento');

            $perosna->nombres_apoderado        = $request->input('nombres_apoderado');
            $perosna->dni_apoderado        = $request->input('dni_apoderado');
            $perosna->telefono_fijo_apoderado        = $request->input('telefono_fijo_apoderado');
            $perosna->direccion_apoderado        = $request->input('direccion_apoderado');

            $perosna->sexo        = $request->input('sexo');
            $perosna->estado_civil        = $request->input('estado_civil');
            $perosna->ocupacion        = $request->input('ocupacion');
            $perosna->personas_en_casa        = $request->input('personas_en_casa');
            $perosna->ingreso_personal        = $request->input('ingreso_personal');
            $perosna->ingreso_familiar        = $request->input('ingreso_familiar');
            $perosna->telefono_fijo        = $request->input('telefono_fijo');
            $perosna->telefono_movil1        = $request->input('telefono_movil1');
            $perosna->telefono_movil2        = $request->input('telefono_movil2');
            $perosna->email        = $request->input('email');
            $perosna->save();
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
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $cboSexo        = [''=>'Seleccione']+ array('M'=>'Masculino','F' => 'Femenino');
        $cboEstadoCivil        = [''=>'Seleccione']+ array('S'=>'Soltero','C' => 'Casado', 'V' => 'Viudo');
        $cboTipo        = [''=>'Seleccione']+ array('S'=>'Socio','C'=>'Cliente' ,'SC' => 'Socio/Cliente');
        $persona        = Persona::find($id);
        $entidad        = 'Persona';
        $formData       = array('persona.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('persona', 'formData', 'entidad', 'boton', 'listar','cboSexo','cboEstadoCivil', 'cboTipo'));
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
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
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
        } 
        $error = DB::transaction(function() use($request, $id){
            $perosna                 = Persona::find($id);
            $perosna->codigo        = $request->input('codigo');
            $perosna->dni        = $request->input('dni');
            $perosna->nombres        = $request->input('nombres');
            $perosna->apellidos        = $request->input('apellidos');
            $perosna->tipo        = $request->input('tipo');
            $perosna->fechai        = $request->input('fechai');
            $perosna->direccion        = $request->input('direccion');
            $perosna->fecha_nacimiento        = $request->input('fecha_nacimiento');

            $perosna->nombres_apoderado        = $request->input('nombres_apoderado');
            $perosna->dni_apoderado        = $request->input('dni_apoderado');
            $perosna->telefono_fijo_apoderado        = $request->input('telefono_fijo_apoderado');
            $perosna->direccion_apoderado        = $request->input('direccion_apoderado');

            $perosna->sexo        = $request->input('sexo');
            $perosna->estado_civil        = $request->input('estado_civil');
            $perosna->ocupacion        = $request->input('ocupacion');
            $perosna->personas_en_casa        = $request->input('personas_en_casa');
            $perosna->ingreso_personal        = $request->input('ingreso_personal');
            $perosna->ingreso_familiar        = $request->input('ingreso_familiar');
            $perosna->telefono_fijo        = $request->input('telefono_fijo');
            $perosna->telefono_movil1        = $request->input('telefono_movil1');
            $perosna->telefono_movil2        = $request->input('telefono_movil2');
            $perosna->email        = $request->input('email');
            $perosna->save();
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
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $persona = Persona::find($id);
            $persona->delete();
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
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Persona::find($id);
        $entidad  = 'Persona';
        $formData = array('route' => array('persona.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
