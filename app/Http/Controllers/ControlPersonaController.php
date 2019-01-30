<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
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

class ControlPersonaController extends Controller
{
    protected $folderview      = 'app.controlpersona';
    protected $tituloAdmin     = 'Control de Asistencia de Socios';
    protected $tituloRegistrar = 'Nuevo Control de Asistencia';
    protected $tituloModificar = 'Modificar concepto';
    protected $tituloEliminar  = 'Eliminar concepto';
    protected $titulo_pagarmulta = 'Pago de Multa por Tardanza o Inasistencia';
    protected $rutas           = array('create' => 'controlpersona.create', 
            'edit'   => 'controlpersona.edit', 
            'delete' => 'controlpersona.eliminar',
            'search' => 'controlpersona.buscar',
            'index'  => 'controlpersona.index',
            'cargarpagarmulta'   => 'controlpersona.cargarpagarmulta',
            'guardarpagarmulta'  => 'controlpersona.guardarpagarmulta',
            'generarreporteasistenciaPDF'  => 'controlpersona.generarreporteasistenciaPDF'
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
        $entidad          = 'ControlPersona';
        $fechai             = Libreria::getParam($request->input('fechai'));
        $fechaf             = Libreria::getParam($request->input('fechaf'));
        $tipo              = Libreria:: getParam($request->input('tipo'));
        $resultado        = ControlPersona::listar($fechai,$fechaf,$tipo);
        $lista            = $resultado->get();
        
        $caja = Caja::where("estado","=","A")->get();
        $idCaja = count($caja) == 0 ?0: $caja[0]->id;


        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Socio o Socio Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Asistencia', 'numero' => '1');
        $cabecera[]       = array('valor' => 'estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '1');

        $cboAsistencia        = array('A'=>'Asistió','F'=>'Faltó','T'=>'Tardanza','J'=>'Falta Justificada','J'=>'Tardanza Justificada');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_pagarmulta = "Pagar Multa";
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta','titulo_pagarmulta','cboAsistencia','idCaja'));
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
        $fecha = date("Y-m-d");

        $entidad          = 'ControlPersona';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboTipo        = [''=>'Todo']+ array('F'=>'Faltas','T'=>'Tardanzas','J'=>'Faltas Justificadas','J'=>'Tardanzas Justificadas');
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

        $fecha             = Libreria::getParam($request->input('fecha'));
        $resultado        = ControlPersona::listSocios();
        $lista            = $resultado->get();
        $cboAsistencia        = array('A'=>'Asistió','F'=>'Faltó','T'=>'Tardanza');

        $validList = DB::table('control_socio')->where('fecha', $fecha)->count();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Socio o Socio Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Asistencia', 'numero' => '1');

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'ControlPersona';
        $controlpersona        = null;
        $formData       = array('controlpersona.store');
        $cboTipo        = [''=>'Seleccione']+ array('I'=>'Ingresos','E'=>'Egresos');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('controlpersona', 'cboTipo','formData', 'entidad', 'boton', 'listar','lista','cboAsistencia','cabecera','fecha','validList'));
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
        $cantidad = $request->input('cantidad');
        $error = null;
       if($cantidad >0){
            $error = DB::transaction(function() use($request,$cantidad){
                for($i=0;$i<$cantidad; $i++){
                    $control_socio = new ControlPersona();
                    $control_socio->persona_id = $request->input("persona_id".$i);
                    $control_socio->fecha= $request->input("fecha");
                    $control_socio->estado = 'N';
                    $control_socio->asistencia = $request->input("asist".$i);
                    $control_socio->save();
                }
            });
        }
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
        $existe = Libreria::verificarExistencia($id, 'controlpersona');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $concepto        = ControlPersona::find($id);
        $entidad        = 'ControlPersona';
        $formData       = array('controlpersona.update', $id);
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
        $existe = Libreria::verificarExistencia($id, 'controlpersona');
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
            $concepto                 = ControlPersona::find($id);
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
        $existe = Libreria::verificarExistencia($id, 'controlpersona');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $concepto = ControlPersona::find($id);
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
        $existe = Libreria::verificarExistencia($id, 'controlpersona');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = ControlPersona::find($id);
        $entidad  = 'ControlPersona';
        $formData = array('route' => array('concepto.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    public function cambiartardanza(Request $request) {
        $idpersona         = $request->get('idpersona');
        $asistencia_id  =$request->get('asistencia');

        $persona = DB::table('control_socio')->where('id',$idpersona)->first();
        $error = DB::transaction(function() use($request, $idpersona, $asistencia_id){
            $control_socio            = ControlPersona::find($idpersona);
            $control_socio->asistencia = $asistencia_id;
            $control_socio->estado = 'P';
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

        $control = ControlPersona::find($control_id);
        $persona = Persona::find($control->persona_id);

        $existe = Libreria::verificarExistencia($control_id, 'control_socio');
        if ($existe !== true) {
            return $existe;
        }
        
        $error = DB::transaction(function() use($request, $concepto_id, $monto, $fecha_pago, $caja_id, $control_id, $persona){

            $control_socio = ControlPersona::find($control_id);

            $control_socio->fecha_pago = $fecha_pago;
            $control_socio->monto = $monto;
            $control_socio->concepto_id = $concepto_id;
            $control_socio->estado = 'P';
            $control_socio->caja_id =  $caja_id;
            $control_socio->save();

            $resultado = Ahorros::getahorropersona($request->input(6));
            if(count($resultado) >0){
                $ahorro_actual = $resultado[0];
                $ahorro_actual->capital = $ahorro_actual->capital + $monto;
                $ahorro_actual->estado = 'P';
                $ahorro_actual->save();
            }else{
                $ahorro = new Ahorros();
                $ahorro->capital = $monto;
                $ahorro->interes = 0;
                $ahorro->estado = 'P';
                $ahorro->fechai = $fecha_pago;
                $ahorro->persona_id = 6;
                $ahorro->save();
            }

            $transaccion = new Transaccion();
            $transaccion->fecha = $fecha_pago;
            $transaccion->monto = $monto;
            $transaccion->monto_ahorro = $monto;
            $transaccion->concepto_id = $concepto_id;
            $transaccion->persona_id = 6;
            $transaccion->descripcion =  "pagó ".$persona->nombres." ";
            $transaccion->usuario_id =Caja::getIdPersona();
            $transaccion->caja_id = $caja_id;
            $transaccion->save();
        });
        
        return is_null($error) ? "OK" : $error;
    }

    public function generarreporteasistenciaPDF($fechai, $fechaf, Request $request)
    {    
        $control_socioT = ControlPersona::listAsistenciaT($fechai, $fechaf);
        $listaT = $control_socioT->get();

        $control_socioF = ControlPersona::listAsistenciaF($fechai, $fechaf);
        $listaF = $control_socioF->get();
        $fecha = $fechaf;
        $titulo = "reporte_control_asistencia_hasta".$fechaf;
        $view = \View::make('app.controlpersona.generarreporteasistenciaPDF')->with(compact('listaT','listaF', 'fecha'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('A','A4',0);
        PDF::SetTopMargin(5);
        //PDF::SetLeftMargin(40);
        //PDF::SetRightMargin(40);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
 
        PDF::Output($titulo.'.pdf', 'I');
    }
}
