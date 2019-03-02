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
use Jenssegers\Date\Date;
use PDF;
use DateTime;

class ControlPersonaController extends Controller
{
    protected $folderview      = 'app.controlpersona';
    protected $tituloAdmin     = 'Control de Asistencia de Socios';
    protected $tituloRegistrar = 'Nuevo Control de Asistencia';
    protected $tituloModificar = 'Modificar concepto';
    protected $tituloEliminar  = 'Eliminar concepto';
    protected $titulo_reporte  = 'Generar Reportes!';
    protected $titulo_pagarmulta = 'Pago de Multa por Tardanza o Inasistencia';
    protected $titulo_justificar = 'Justificar falta o tardanza';
    protected $rutas           = array('create' => 'controlpersona.create', 
            'edit'   => 'controlpersona.edit', 
            'delete' => 'controlpersona.eliminar',
            'search' => 'controlpersona.buscar',
            'index'  => 'controlpersona.index',
            'cargarpagarmulta'   => 'controlpersona.cargarpagarmulta',
            'guardarpagarmulta'  => 'controlpersona.guardarpagarmulta',
            'cargarjustificar'  => 'controlpersona.cargarjustificar',
            'guardarjustificar' => 'controlpersona.guardarjustificar',

            'cargarreporte' => 'controlpersona.cargarreporte',
            'generarreporteasistenciaPDF'  => 'controlpersona.generarreporteasistenciaPDF',
            'generarreportejustificadaPDF'  => 'controlpersona.generarreportejustificadaPDF'
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
        $fechaini             = Libreria::getParam($request->input('fechaini'));
        $fechafin             = Libreria::getParam($request->input('fechafin'));
        $tipoi             = Libreria:: getParam($request->input('tipoi'));
        $resultado        = ControlPersona::listar($fechaini,$fechafin,$tipoi);
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

        $Month = array(1=>'Ene',
                        2=>'Feb',
                        3=>'Mar',
                        4=>'Abr',
                        5=>'May',
                        6=>'Jun',
                        7=>'Jul',
                        8=>'Ago',
                        9=>'Sep',
                        10=>'Oct',
                        11=>'Nov',
                        12=>'Dic');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_pagarmulta = "Pagar Multa";
        $titulo_justificar = $this->titulo_justificar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta','titulo_pagarmulta','cboAsistencia','idCaja','Month','titulo_justificar'));
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
        $titulo_reporte   = $this->titulo_reporte;
        $cboTipo        = [''=>'Todo']+ array('F'=>'Faltas','T'=>'Tardanzas');
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad','cboTipo' ,'title', 'titulo_registrar', 'ruta','titulo_reporte'));
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


        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Socio o Socio Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Asistencia', 'numero' => '1');

        $listar         = Libreria::getParam($request->input('listar'), 'NO');

        $caja_status = Caja::where('estado','A')->where('deleted_at',null)->get();
        $date_caja = (count($caja_status) !=0)? Date::parse($caja_status[0]->fecha_horaapert)->format('Y-m-d'): date('Y-m-d');

        $validList = DB::table('control_socio')->where('fecha', $date_caja)->count();

        $entidad        = 'ControlPersona';
        $controlpersona        = null;
        $formData       = array('controlpersona.store');
        $cboTipo        = [''=>'Seleccione']+ array('I'=>'Ingresos','E'=>'Egresos');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('controlpersona', 'cboTipo','formData', 'entidad', 'boton', 'listar','lista','cboAsistencia','cabecera','fecha','validList','date_caja'));
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

    //JUSTIFICAR FALTA O TARDANZA
    public function cargarjustificar(Request $request, $id, $listarLuego){
        $entidad  = 'ControlPersona';
        $ruta = $this->rutas;
        $titulo_justificar = "Justificar falta o tardanza";
        $indiceCbo = $request->get('indiceCbo');
        return view($this->folderview.'.justificarasist')->with(compact('entidad', 'id','ruta', 'titulo_justificar','indiceCbo'));
    }

    public function guardarjustificar(Request $request) {

        $control_id         = $request->get('control_id');

        $existe = Libreria::verificarExistencia($control_id, 'control_socio');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($request, $control_id){
            $control_socio            = ControlPersona::find($control_id);
            $control_socio->asistencia = 'J';
            $control_socio->estado = 'P';
            $control_socio->descripcion = $request->get('descripcion');
            $control_socio->save();
        });
        return is_null($error) ? "OK" : $error;
    }




    public function cargarpagarmulta($id, $listarLuego){

        $caja_id = Caja::where("estado","=","A")->value('id');
        $cboMulta        = array('12'=>'Multa Por Tardanza o Insistencia');
        $entidad  = 'ControlPersona';

        $caja = Caja::where("estado","=","A")->get();
        $fecha_caja = count($caja) == 0? 0: Date::parse($caja[0]->fecha_horaApert)->format('Y-m-d');

        $ruta = $this->rutas;
        $titulo_pagarmulta = "Pagar Multa por Tardanza o Inasistencia";
        return view($this->folderview.'.pagarmulta')->with(compact('entidad', 'ruta', 'titulo_pagarmulta','cboMulta','caja_id','id','fecha_caja'));
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

            $resultado = (new Ahorros())->getahorropersona(6);

            $ahorro_id=null;
            if(count($resultado) >0){
                $ahorro_actual = $resultado[0];
                $ahorro_actual->capital = $ahorro_actual->capital + $monto;
                $ahorro_actual->estado = 'P';
                $ahorro_actual->save();
                $ahorro_id = $ahorro_actual->id;
            }else{
                $ahorro = new Ahorros();
                $ahorro->capital = $monto;
                $ahorro->interes = 0;
                $ahorro->estado = 'P';
                $ahorro->fechai = $fecha_pago;
                $ahorro->persona_id = 6;
                $ahorro->save();
                $ahorro_id = $ahorro->id;
            }
            /*
            $transaccion = new Transaccion();
            $transaccion->fecha = $fecha_pago;
            $transaccion->monto = $monto;
            $transaccion->monto_ahorro = 0;
            $transaccion->concepto_id = $concepto_id;///multa por tradanza
            $transaccion->persona_id = $persona->id;
            $transaccion->descripcion =  "pagó ".$persona->nombres." ";
            $transaccion->usuario_id = Caja::getIdPersona();
            $transaccion->caja_id = $caja_id;
            $transaccion->save();*/

            $idconcepto = $request->input('concepto');
            $transaccion = new Transaccion();
            $transaccion->fecha = $fecha_pago;
            $transaccion->monto =  $monto;
            $transaccion->monto_ahorro=  $monto;
            $transaccion->id_tabla = $ahorro_id;
            $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
            $transaccion->concepto_id = 5;// concepto deposito de ahorros
            $transaccion->persona_id = 6;
            $transaccion->descripcion =  "Se ahorró S/. 5 de multa de ".$persona->apellidos." ".$persona->nombres;
            $transaccion->usuario_id = Caja::getIdPersona();
            $transaccion->caja_id =  $caja_id;
            $transaccion->save();

        });
        
        return is_null($error) ? "OK" : $error;
    }


    public function cargarreporte(){
        $cboTipo        = [''=>'Seleccione'] + array('I'=>'Lista de faltas o tardanzas', 'E'=>'Lista de tardanzas o faltas justificadas');
        $entidad  = 'ControlPersona';
        $ruta = $this->rutas;
        $date_first = date('Y-01');
        $date_last = date('Y-m');

        $titulo_reporte = $this->titulo_reporte;
        return view($this->folderview.'.reporte')->with(compact('entidad', 'ruta', 'titulo_reporte','cboTipo','date_first','date_last'));
    }


    public function generarreporteasistenciaPDF($fechai, $fechaf, Request $request)
    {    
        $control_socioT = ControlPersona::listAsistenciaT($fechai, $fechaf);
        $listaT = $control_socioT->get();

        $control_socioF = ControlPersona::listAsistenciaF($fechai, $fechaf);
        $listaF = $control_socioF->get();
        $Month = array(1=>'Enero',
                        2=>'Febrero',
                        3=>'Marzo',
                        4=>'Abril',
                        5=>'Mayo',
                        6=>'Junio',
                        7=>'Julio',
                        8=>'Agosto',
                        9=>'Septiembre',
                        10=>'Octubre',
                        11=>'Noviembre',
                        12=>'Diciembre');

        $titulo = "reporte_control_asistencia_hasta".$fechaf;
        $view = \View::make('app.controlpersona.generarreporteasistenciaPDF')->with(compact('listaT','listaF', 'fechai','fechaf','Month'));
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

    public function generarreportejustificadaPDF($fechai, $fechaf, Request $request)
    {    
        $control_socioT = ControlPersona::listJustificadas($fechai, $fechaf);
        $listaT = $control_socioT->get();
        $Month = array(1=>'Enero',
                        2=>'Febrero',
                        3=>'Marzo',
                        4=>'Abril',
                        5=>'Mayo',
                        6=>'Junio',
                        7=>'Julio',
                        8=>'Agosto',
                        9=>'Septiembre',
                        10=>'Octubre',
                        11=>'Noviembre',
                        12=>'Diciembre');

        $titulo = "reporte_control_asistencia_justificadas".$fechaf;
        $view = \View::make('app.controlpersona.generarreportejustificada')->with(compact('listaT', 'fechai','fechaf','Month'));
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
