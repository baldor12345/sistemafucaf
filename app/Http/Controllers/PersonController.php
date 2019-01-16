<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Persona;
use App\Caja;
use App\Acciones;
use App\Ahorros;
use App\Credito;
use App\Cuota;
use App\Concepto;
use App\ControlPersona;
use App\Transaccion;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class PersonController extends Controller
{
    protected $folderview      = 'app.persona';
    protected $tituloAdmin     = 'Persona';
    protected $tituloRegistrar = 'Registrar persona';
    protected $tituloModificar = 'Modificar persona';
    protected $titulo_control = 'Control de Asistencia Reunion Socios';
    protected $tituloEliminar  = 'Eliminar persona';
    protected $rutas           = array('create' => 'persona.create', 
            'edit'   => 'persona.edit', 
            'delete' => 'persona.eliminar',
            'search' => 'persona.buscar',
            'index'  => 'persona.index',
            'cargarcontrolpersona'  => 'persona.cargarcontrolpersona',
            'buscarpersona'=> 'persona.buscarpersona',
            'cargarpagarmulta'   => 'persona.cargarpagarmulta',
            'guardarpagarmulta'  => 'persona.guardarpagarmulta',
            'cargarbinnacle'  => 'persona.cargarbinnacle',
            'generarreporte'  => 'persona.generarreporte',
            
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
        $tipo             = Libreria::getParam($request->input('tipoi'));
        $resultado        = Persona::listar($codigo, $nombres, $dni, $tipo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Telefono', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Email', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'E. Cuenta', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_control  = $this->titulo_control;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta','titulo_control'));
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
        $titulo_control = $this->titulo_control;
        $cboTipo        = [''=>'Todo']+ array('S'=>'Socio','C'=>'Cliente' ,'SC' => 'Socio/Cliente');
        $cboSexo        = [''=>'Seleccione']+ array('M'=>'Masculino','F' => 'Femenino');
        $cboEstadoCivil        = [''=>'Seleccione']+ array('SO'=>'Soltero','CA' => 'Casado', 'VI' => 'Viudo','CO'=>'Conviviente');
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboTipo', 'cboSexo','cboEstadoCivil','titulo_control'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //generar codigo para agregar una nueva persona

        $findPersonalast = DB::table('persona')->orderBy('id','DESC')->first();
        $codigo_generado = "";
        $codigo_generado = "FUCAF0".($findPersonalast->id+1);

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Persona';
        $persona        = null;
        $cboTipo        = [''=>'Seleccione']+ array('S'=>'Socio','C'=>'Cliente' ,'SC' => 'Socio/Cliente');
        $cboEstado        = array('A'=>'Activo','I'=>'Inactivo');
        $cboSexo        = [''=>'Seleccione']+ array('M'=>'Masculino','F' => 'Femenino');
        $cboEstadoCivil        = [''=>'Seleccione']+ array('SO'=>'Soltero','CA' => 'Casado', 'VI' => 'Viudo','CO'=>'Conviviente');
        $formData       = array('persona.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('persona', 'formData', 'entidad','codigo_generado', 'boton', 'listar', 'cboTipo','cboSexo','cboEstadoCivil','cboEstado'));
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
            'ingreso_familiar'    => 'required|max:20'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){

            $codigo_generado = $request->input('codigo');
            if($request->input('tipo') == "S"){
                $codigo_generado = "S".$codigo_generado;
            }else if($request->input('tipo') == "C"){
                $codigo_generado = "C".$codigo_generado;
            }else{
                $codigo_generado = "SC".$codigo_generado;
            }

            $perosna               = new Persona();
            $perosna->codigo        = $codigo_generado;
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
            $perosna->estado        = $request->input('estado');
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
        $cboEstadoCivil        = [''=>'Seleccione']+ array('SO'=>'Soltero','CA' => 'Casado', 'VI' => 'Viudo','CO'=>'Conviviente');
        $cboEstado        = array('A'=>'Activo','I'=>'Inactivo');
        $cboTipo        = [''=>'Seleccione']+ array('S '=>'Socio','C '=>'Cliente' ,'SC' => 'Socio/Cliente');
        $persona        = Persona::find($id);
        $entidad        = 'Persona';
        $formData       = array('persona.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('persona', 'formData', 'entidad', 'boton', 'listar','cboSexo','cboEstadoCivil', 'cboTipo','cboEstado'));
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
            'ingreso_familiar'    => 'required|max:20'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $valor = $request->input('codigo');
            $tipo_selected = $request->input('tipo');
            $codigo_generado =  substr_replace($valor , $tipo_selected, 0, 1);

            $perosna                 = Persona::find($id);
            $perosna->codigo        = $codigo_generado;
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
            $perosna->estado        = $request->input('estado');
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
        $entidad  = 'Persona';//este  
        $formData = array('route' => array('persona.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }


    public function cargarcontrolpersona(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idCaja = count($caja) == 0 ?0: $caja[0]->id;

        $fecha = date("Y-m-d");

        $listaControl = DB::table('control_socio')->where('fecha','=',$fecha)->count();

        if($listaControl == 0){
            $resultado        = ControlPersona::listSocioCliente();
            $lista =  $resultado->get();
            $error = DB::transaction(function() use($request,$lista){
                for($i=0; $i<count($lista); $i++){
                    $control_socio               = new ControlPersona();
                    $control_socio->persona_id        = $lista[$i]->id;
                    $control_socio->asistencia = 'A';
                    $control_socio->estado = 'A';
                    $control_socio->fecha        = date ("Y-m-d");
                    $control_socio->save();
                }
            });
        }

        $titulo_control = $this->titulo_control;
        $ruta             = $this->rutas;
        $entidad ='ControlPersona';
        return view($this->folderview.'.controlpersona')->with(compact('entidad', 'ruta','titulo_control','idCaja'));
    }

    public function buscarpersona(Request $request){
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $fecha            = Libreria::getParam($request->input('fecha'));
        $entidad ='ControlPersona';
        $resultado        = ControlPersona::listar($fecha);
        $lista            = $resultado->get();

        $caja = Caja::where("estado","=","A")->get();
        $idCaja = count($caja) == 0 ?0: $caja[0]->id;


        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Socio o Socio Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Asistencia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '1');
      
        $cboAsistencia        = array('A'=>'Asistió','T'=>'Tardanza','F' => 'Faltó');
        $ruta             = $this->rutas;
        $inicio           = 0;
        $titulo_pagarmulta = "Pagar Multa";
        
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.buscarpersona')->with(compact('lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio','titulo_pagarmulta','cboAsistencia','idCaja'));
        }
        return view($this->folderview.'.buscarpersona')->with(compact('concepto_id','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio','titulo_pagarmulta','idCaja'));
    
    }

    public function cambiartardanza(Request $request) {
        $idpersona         = $request->get('idpersona');
        $asistencia_id  =$request->get('asistencia');

        $persona = DB::table('control_socio')->where('id',$idpersona)->first();
        $error = DB::transaction(function() use($request, $idpersona, $asistencia_id){
            $control_socio            = ControlPersona::find($idpersona);
            $control_socio->asistencia = $asistencia_id;
            $control_socio->estado = 'N';
            $control_socio->save();
        });
        return is_null($error) ? "OK" : $error;
    }


    public function cargarpagarmulta($id, $listarLuego){

        $caja_id = Caja::where("estado","=","A")->value('id');

        $cboMulta        = array('12'=>'Multa Por Tardanza o Insistencia');
        $entidad  = 'ControlPersona';
        $ruta = $this->rutas;
        $titulo_pagarmulta = "Pagar Multa por Tardanza o Inasistencia";
        return view($this->folderview.'.pagarmulta')->with(compact('entidad', 'ruta', 'titulo_pagarmulta','cboMulta','caja_id','id'));
    }

    public function guardarpagarmulta(Request $request)
    {
        $concepto_id = Libreria::getParam($request->input('concepto_id'));
        $monto = Libreria::getParam($request->input('monto'));
        $fecha_pago = Libreria::getParam($request->input('fecha_pago'));
        $caja_id = Libreria::getParam($request->input('caja_id'));
        $control_id = Libreria::getParam($request->input('control_id'));

        $existe = Libreria::verificarExistencia($control_id, 'control_socio');
        if ($existe !== true) {
            return $existe;
        }
        
        $error = DB::transaction(function() use($request, $concepto_id, $monto, $fecha_pago, $caja_id, $control_id){

            $control_socio            = ControlPersona::find($control_id);

            $control_socio->fecha_pago = $fecha_pago;
            $control_socio->monto = $monto;
            $control_socio->concepto_id = $concepto_id;
            $control_socio->estado = 'P';
            $control_socio->caja_id =  $caja_id;
            $control_socio->save();

            $transaccion = new Transaccion();
            $transaccion->fecha = $fecha_pago;
            $transaccion->monto = $monto;
            $transaccion->concepto_id = $concepto_id;
            $transaccion->descripcion =  "multa por tardanza o falta";
            $transaccion->usuario_id =Caja::getIdPersona();
            $transaccion->caja_id = $caja_id;
            $transaccion->save();
        });
        
        return is_null($error) ? "OK" : $error;
    }

    public function generarestadocuentaPDF($id, Request $request)
    {    
        $persona_ahorrista = DB::table('persona')->where('id', $id)->first();

        //monto ahorrado y cantidad total de acciones asta la fecha
        $monto_ahorro = Ahorros::where('persona_id', $id)->where('estado','P')->first();
        $ahorro = (count($monto_ahorro) == 0)?0: $monto_ahorro;

        if($ahorro !== 0){
           $capital_ahorrado = $ahorro->capital;
           $fecha_ahorro =  $ahorro->fechai;
           $interes_ahorro = $ahorro->interes;
        }else{
            $capital_ahorrado = 0;
            $fecha_ahorro =0;
            $interes_ahorro = 0;
        }

        $CantAccionesCompradas = DB::table('acciones')->where('estado', 'C')->where('persona_id',$id)->count();
        $CantAccionesVendidas = DB::table('acciones')->where('estado', 'V')->where('persona_id',$id)->count();

        //creditos pendientes de la persona
        $credito        = Persona::creditos_por_persona($id);
        $credito_pendiente           = $credito->get();

        $moras_acumuladas = Persona::moras_acumuladas_persona($id)->get();


        $titulo = 'estado_de_cuenta_'.$persona_ahorrista->nombres;
        $view = \View::make('app.persona.generarestadocuentaPDF')->with(compact('persona_ahorrista','fecha_ahorro','interes_ahorro','capital_ahorrado', 'CantAccionesCompradas','CantAccionesVendidas','credito_pendiente', 'socio_aval','moras_acumuladas'));
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

}
