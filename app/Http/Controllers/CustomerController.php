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

class CustomerController extends Controller
{
    protected $folderview      = 'app.customer';
    protected $tituloAdmin     = 'Clientes';
    protected $tituloRegistrar = 'Registrar cliente';
    protected $tituloModificar = 'Modificar cliente';
    protected $tituloEliminar  = 'Eliminar cliente';
    protected $rutas           = array('create' => 'customer.create', 
            'edit'   => 'customer.edit', 
            'delete' => 'customer.eliminar',
            'search' => 'customer.search',
            'index'  => 'customer.index',
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
    public function search(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Customer';
        $name             = Libreria::getParam($request->input('name'));
        $resultado        = Personamaestro::listar($name, 'C');
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

    public function index()
    {
        $entidad          = 'Customer';
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
        $entidad         = 'Customer';
        $departamento    = Departamento::where('nombre', '=', 'LAMBAYEQUE')->first();
        $provincia       = Provincia::where('nombre', '=', 'CHICLAYO')->where('departamento_id', '=', $departamento->id)->first();
        $distrito        = Distrito::where('nombre', '=', 'CHICLAYO')->where('provincia_id', '=', $provincia->id)->first();
        $cboDepartamento = [''=>'Seleccione'] + Departamento::orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboProvincia    = [''=>'Seleccione'] + Provincia::where('departamento_id', '=', $departamento->id)->orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboDistrito     = [''=>'Seleccione'] + Distrito::where('provincia_id', '=', $provincia->id)->orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboType = array('P' => 'Persona','C'=>'Empresa');
        $birthdate       = null;
        $customer        = null;
        $formData        = array('customer.store');
        $formData        = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton           = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('customer', 'formData', 'entidad', 'boton', 'listar', 'departamento', 'provincia', 'distrito', 'cboDepartamento', 'cboProvincia', 'cboDistrito', 'birthdate', 'cboType'));
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

        $request->merge(array_map('trim', $request->all()));
		$mensajes = array(
			'departamento_id.required'   => 'Debe seleccionar un departamento',
			'provincia_id.required_with' => 'Debe seleccionar una provincia',
			'distrito_id.required_with'  => 'Debe seleccionar un distrito',
			);
		$reglas = array(
                'departamento_id'   => 'required|integer|exists:departamento,id',
				'provincia_id'      => 'required_with:departamento_id|integer|exists:provincia,id',
				'distrito_id'       => 'required_with:provincia_id|integer|exists:distrito,id'
                );

		$validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $type = $request->input('secondtype');
		if ($type == 'P') {
			$mensajes = array(
				'dni.required'               => 'Debe ingresar el DNI del cliente',
				'firstname.required'            => 'Debe ingresar nombre del cliente',
				'lastname.required'   		 => 'Debe ingresar apellidos del cliente',
				'dni.exists'                 => 'N° de DNI pertenece a cliente ya registrado'
				);
			$reglas = array(
                'lastname'   		=> 'required|max:100',
				'firstname'            => 'required|max:100',
				'dni'               => 'required|unique:person,dni,NULL,id,deleted_at,NULL|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/'
                );

			$validacion = Validator::make($request->all(), $reglas, $mensajes);
	        if ($validacion->fails()) {
	            return $validacion->messages()->toJson();
	        }
			
		}elseif ($type == 'C') {
			$request->merge(array_map('trim', $request->all()));
			$mensajes = array(
				'bussinesname.required'  => 'Debe ingresar la razon social del cliente',
				'ruc.exists'            => 'N° de RUC pertenece a cliente ya registrado',
				'ruc.required'          => 'Debe ingresar el RUC del cliente'
				);
			$reglas = array(
                'bussinesname' => 'required|max:100',
				'ruc'          => 'required|unique:person,ruc,NULL,id,deleted_at,NULL|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/'
                );

			$validacion = Validator::make($request->all(), $reglas, $mensajes);
	        if ($validacion->fails()) {
	            return $validacion->messages()->toJson();
	        }
			
		}

        $error = DB::transaction(function() use($request){
            Date::setLocale('es');
            $customer            = new Person();
			$customer->firstname   		  = Libreria::getParam($request->input('firstname'));
			$customer->lastname 		  = Libreria::getParam($request->input('lastname'));
			$customer->bussinesname       = Libreria::getParam($request->input('bussinesname'));
			$customer->distrito_id       = Libreria::getParam($request->input('distrito_id'));
			$customer->type 			    = 'C';
			$customer->secondtype			= Libreria::getParam($request->input('secondtype'));
			$customer->address         = Libreria::getParam($request->input('address'));
			$customer->dni               = Libreria::getParam($request->input('dni'));
			$customer->email             = Libreria::getParam($request->input('email'));
			$customer->ruc               = Libreria::getParam($request->input('ruc'));
			$customer->phonenumber          = Libreria::getParam($request->input('phonenumber'));
			$customer->cellnumber           = Libreria::getParam($request->input('cellnumber'));
			$customer->observation       = Libreria::getParam($request->input('observation'));
			$customer->save();
        });
        return is_null($error) ? "OK" : $error;
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
        $customer        = Person::find($id);
        $entidad         = 'Customer';
        $distrito        = Distrito::where('id', '=', $customer->distrito_id)->first();
        $provincia       = Provincia::where('id', '=', $distrito->provincia_id)->first();
        $departamento    = Departamento::where('id', '=', $provincia->departamento_id)->first();
        $cboDepartamento = array('' => 'Seleccione') + Departamento::orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboProvincia    = array('' => 'Seleccione') + Provincia::where('departamento_id', '=', $departamento->id)->orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboDistrito     = array('' => 'Seleccione') + Distrito::where('provincia_id', '=', $provincia->id)->orderBy('nombre', 'ASC')->pluck('nombre', 'id')->all();
        $cboType = array('P' => 'Persona','C'=>'Empresa');
        
        $formData        = array('customer.update', $id);
        $formData        = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton           = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('customer', 'formData', 'entidad', 'boton', 'departamento', 'provincia', 'distrito', 'cboDepartamento', 'cboProvincia', 'cboDistrito', 'listar', 'cboType'));
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
			'departamento_id.required'   => 'Debe seleccionar un departamento',
			'provincia_id.required_with' => 'Debe seleccionar una provincia',
			'distrito_id.required_with'  => 'Debe seleccionar un distrito',
			);
		$reglas = array(
                'departamento_id'   => 'required|integer|exists:departamento,id',
				'provincia_id'      => 'required_with:departamento_id|integer|exists:provincia,id',
				'distrito_id'       => 'required_with:provincia_id|integer|exists:distrito,id'
                );

		$validacion = Validator::make($request->all(), $reglas, $mensajes);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $type = $request->input('secondtype');
		if ($type == 'P') {
			$mensajes = array(
				'dni.required'               => 'Debe ingresar el DNI del cliente',
				'firstname.required'            => 'Debe ingresar nombre del cliente',
				'lastname.required'   		 => 'Debe ingresar apellidos del cliente',
				);
			$reglas = array(
                'lastname'   		=> 'required|max:100',
				'firstname'            => 'required|max:100',
				'dni'               => 'required|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/'
                );

			$validacion = Validator::make($request->all(), $reglas, $mensajes);
	        if ($validacion->fails()) {
	            return $validacion->messages()->toJson();
	        }

	        $person = Person::where('dni','=',$request->input('dni'))->where('id','<>',$id)->first();
	        if ($person !== null) {
	            $error = array(
	                'dni' => array(
	                    'El dni ya esta siendo usado por otra persona'
	                    ));
	            return json_encode($error);
	        }
			
		}elseif ($type == 'C') {
			$request->merge(array_map('trim', $request->all()));
			$mensajes = array(
				'bussinesname.required'  => 'Debe ingresar la razon social del cliente',
				'ruc.required'          => 'Debe ingresar el RUC del cliente'
				);
			$reglas = array(
                'bussinesname' => 'required|max:100',
				'ruc'          => 'required|regex:/^[0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]$/'
                );

			$validacion = Validator::make($request->all(), $reglas, $mensajes);
	        if ($validacion->fails()) {
	            return $validacion->messages()->toJson();
	        }

	        $person = Person::where('ruc','=',$request->input('ruc'))->where('id','<>',$id)->first();
	        if ($person !== null) {
	            $error = array(
	                'ruc' => array(
	                    'El ruc ya esta siendo usado por otra persona'
	                    ));
	            return json_encode($error);
	        }
			
		}
        $error = DB::transaction(function() use($request, $id){
            $customer                = Person::find($id);
            $customer->firstname   		  = Libreria::getParam($request->input('firstname'));
			$customer->lastname 		  = Libreria::getParam($request->input('lastname'));
			$customer->bussinesname       = Libreria::getParam($request->input('bussinesname'));
			$customer->distrito_id       = Libreria::getParam($request->input('distrito_id'));
			//$customer->type 			    = 'C';
			$customer->secondtype			= Libreria::getParam($request->input('secondtype'));
			$customer->address         = Libreria::getParam($request->input('address'));
			$customer->dni               = Libreria::getParam($request->input('dni'));
			$customer->email             = Libreria::getParam($request->input('email'));
			$customer->ruc               = Libreria::getParam($request->input('ruc'));
			$customer->phonenumber          = Libreria::getParam($request->input('phonenumber'));
			$customer->cellnumber           = Libreria::getParam($request->input('cellnumber'));
			$customer->observation       = Libreria::getParam($request->input('observation'));
			$customer->save();
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
            $customer = Person::find($id);
            $customer->delete();
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
        $entidad  = 'Customer';
        $formData = array('route' => array('customer.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
}
