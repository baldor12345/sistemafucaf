<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Personamaestro;
use App\Departamento;
use App\Provincia;
use App\Distrito;
use App\Workertype;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

class EmployeeController extends Controller
{
    protected $folderview      = 'app.employee';
    protected $tituloAdmin     = 'Empleados';
    protected $tituloRegistrar = 'Registrar empleado';
    protected $tituloModificar = 'Modificar empleado';
    protected $tituloEliminar  = 'Eliminar empleado';
    protected $rutas           = array('create' => 'employee.create', 
            'edit'   => 'employee.edit', 
            'delete' => 'employee.eliminar',
            'search' => 'employee.buscar',
            'index'  => 'employee.index',
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
        $entidad          = 'Employee';
        $name             = Libreria::getParam($request->input('name'));
        $resultado        = Personamaestro::listar($name, 'E');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apellido y nombres / Razon social', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI / RUC', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Direccion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nro Celular', 'numero' => '1');
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
        $entidad          = 'Employee';
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
        $listar          = Libreria::getParam($request->input('listar'), 'NO');
        $entidad         = 'Employee';
        $departamento    = Departamento::where('nombre', '=', 'LAMBAYEQUE')->first();
        $provincia       = Provincia::where('nombre', '=', 'CHICLAYO')->where('departamento_id', '=', $departamento->id)->first();
        $distrito        = Distrito::where('nombre', '=', 'CHICLAYO')->where('provincia_id', '=', $provincia->id)->first();
        $cboDepartamento = [''=>'Seleccione'] + Departamento::orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboProvincia    = [''=>'Seleccione'] + Provincia::where('departamento_id', '=', $departamento->id)->orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboDistrito     = [''=>'Seleccione'] + Distrito::where('provincia_id', '=', $provincia->id)->orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboWorkertype   = [''=>'Seleccione'] + Workertype::pluck('name', 'id')->all();
        $birthdate       = null;
        $employee        = null;
        $formData        = array('employee.store');
        $formData        = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton           = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('employee', 'formData', 'entidad', 'boton', 'listar', 'departamento', 'provincia', 'distrito', 'cboDepartamento', 'cboProvincia', 'cboDistrito', 'birthdate', 'cboWorkertype'));
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
        $mensajes = array(
            'birthdate.required'         => 'Ingrese fecha de nacimiento',
            'departamento_id.required'   => 'Debe seleccionar un departamento',
            'provincia_id.required_with' => 'Debe seleccionar una provincia',
            'distrito_id.required_with'  => 'Debe seleccionar un distrito',
            'address.required'           => 'Debe ingresar la dirección del personal',
            'dni.required'               => 'Debe ingresar el DNI del personal',
            'firstname.required'         => 'Debe ingresar nombre del personal',
            'lastname.required'          => 'Debe ingresar apellidos del personal',
            'dni.exists'                 => 'N° de DNI pertenece a personal ya registrado',
            'phonenumber.required'       => 'Debe ingresar el número de telefono fijo',
            'cellnumber.required'        => 'Debe ingresar el número de telefono celular',
            'workertype_id.required'     => 'Debe seleccionar el tipo de trabajador'
            );
        $reglas = array(
                'birthdate'       => 'required|date_format:d/m/Y',
                'lastname'        => 'required|max:100',
                'firstname'       => 'required|max:100',
                'dni'             => 'required|unique:person,dni,NULL,id,deleted_at,NULL|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
                'ruc'             => 'unique:person,dni,NULL,id,deleted_at,NULL|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
                'address'         => 'required|max:120',
                'departamento_id' => 'required|integer|exists:departamento,id',
                'provincia_id'    => 'required_with:departamento_id|integer|exists:provincia,id',
                'distrito_id'     => 'required_with:provincia_id|integer|exists:distrito,id',
                'phonenumber'     => 'required|max:15|regex:/^[0-9]+?-?[0-9]+$/',
                'cellnumber'      => 'max:20|regex:/^[*]?[#]?[0-9]+?-?[0-9]+$/',
                'workertype_id'   => 'required|integer|exists:workertype,id,deleted_at,NULL'
                );

        $validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            Date::setLocale('es');
            $employee                = new Person();
            $employee->firstname     = $request->input('firstname');
            $employee->lastname      = $request->input('lastname');
            $employee->distrito_id   = $request->input('distrito_id');
            $employee->workertype_id = $request->input('workertype_id');
            $employee->address       = strtoupper($request->input('address'));
            $employee->dni           = $request->input('dni');
            $employee->birthdate     = Date::createFromFormat('d/m/Y', $request->input('birthdate'))->format('Y-m-d');
            $employee->email         = Libreria::getParam($request->input('email'));
            $employee->ruc           = Libreria::getParam($request->input('ruc'));
            $employee->phonenumber   = Libreria::getParam($request->input('phonenumber'));
            $employee->cellnumber    = Libreria::getParam($request->input('cellnumber'));
            $employee->observation   = Libreria::getParam($request->input('observation'));
            $employee->type          = 'E';
            $employee->save();
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
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $listar          = Libreria::getParam($request->input('listar'), 'NO');
        $employee        = Person::find($id);
        $entidad         = 'Employee';
        $distrito        = Distrito::where('id', '=', $employee->distrito_id)->first();
        $provincia       = Provincia::where('id', '=', $distrito->provincia_id)->first();
        $departamento    = Departamento::where('id', '=', $provincia->departamento_id)->first();
        $cboDepartamento = array('' => 'Seleccione') + Departamento::orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboProvincia    = array('' => 'Seleccione') + Provincia::where('departamento_id', '=', $departamento->id)->orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboDistrito     = array('' => 'Seleccione') + Distrito::where('provincia_id', '=', $provincia->id)->orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboWorkertype   = array('' => 'Seleccione') + Workertype::pluck('name', 'id')->all();
        
        $formData        = array('employee.update', $id);
        $formData        = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton           = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('employee', 'formData', 'entidad', 'boton', 'departamento', 'provincia', 'distrito', 'cboDepartamento', 'cboProvincia', 'cboDistrito', 'listar', 'cboWorkertype'));
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
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $request->merge(array_map('trim', $request->all()));
        $mensajes = array(
            'birthdate.required'         => 'Ingrese fecha de nacimiento',
            'departamento_id.required'   => 'Debe seleccionar un departamento',
            'provincia_id.required_with' => 'Debe seleccionar una provincia',
            'distrito_id.required_with'  => 'Debe seleccionar un distrito',
            'address.required'           => 'Debe ingresar la dirección del personal',
            'dni.required'               => 'Debe ingresar el DNI del personal',
            'firstname.required'         => 'Debe ingresar nombre del personal',
            'lastname.required'          => 'Debe ingresar apellidos del personal',
            'dni.exists'                 => 'N° de DNI pertenece a personal ya registrado',
            'phonenumber.required'       => 'Debe ingresar el número de telefono fijo',
            'cellnumber.required'        => 'Debe ingresar el número de telefono celular',
            'workertype_id.required'     => 'Debe seleccionar el tipo de trabajador'
            );
        $validacion = Validator::make($request->all(),
            array(
                'birthdate'       => 'required|date_format:d/m/Y',
                'lastname'        => 'required|max:100',
                'firstname'       => 'required|max:100',
                'dni'             => 'required|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
                'ruc'             => 'unique:person,dni,NULL,id,deleted_at,NULL|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/',
                'address'         => 'required|max:120',
                'departamento_id' => 'required|integer|exists:departamento,id',
                'provincia_id'    => 'required_with:departamento_id|integer|exists:provincia,id',
                'distrito_id'     => 'required_with:provincia_id|integer|exists:distrito,id',
                'phonenumber'     => 'required|max:15|regex:/^[0-9]+?-?[0-9]+$/',
                'cellnumber'      => 'max:20|regex:/^[*]?[#]?[0-9]+?-?[0-9]+$/',
                'workertype_id'   => 'required|integer|exists:workertype,id,deleted_at,NULL'
                ), $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $employee                = Person::find($id);
            $employee->firstname     = $request->input('firstname');
            $employee->lastname      = $request->input('lastname');
            $employee->distrito_id   = $request->input('distrito_id');
            $employee->workertype_id = $request->input('workertype_id');
            $employee->address       = strtoupper($request->input('address'));
            $employee->dni           = $request->input('dni');
            $employee->birthdate     = Date::createFromFormat('d/m/Y', $request->input('birthdate'))->format('Y-m-d');
            $employee->email         = Libreria::getParam($request->input('email'));
            $employee->ruc           = Libreria::getParam($request->input('ruc'));
            $employee->phonenumber   = Libreria::getParam($request->input('phonenumber'));
            $employee->cellnumber    = Libreria::getParam($request->input('cellnumber'));
            $employee->observation   = Libreria::getParam($request->input('observation'));
            $employee->save();
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
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $employee = Person::find($id);
            $employee->delete();
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
        $existe = Libreria::verificarExistencia($id, 'person');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Person::find($id);
        $entidad  = 'Employee';
        $formData = array('route' => array('employee.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
