<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Persona;
use App\Caja;
use App\Ahorros;
use App\Transaccion;
use App\Concepto;
use App\Configuraciones;
use App\Gastos;
use App\Credito;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use PDF;
use DateTime;

class CajaController extends Controller
{
    protected $folderview      = 'app.caja';
    protected $tituloAdmin     = 'Caja';
    protected $tituloRegistrar = 'Apertura Caja';
    protected $tituloModificar = 'Modificar Caja';
    protected $titulo_nuevomovimiento = 'Registrar Nuevo Movimiento';
    protected $tituloCerrarCaja = 'Cerrar Caja';
    protected $titulo_reaperturar = 'Reaperturar Caja';
    protected $titulo_reporte = 'Reportes';
    protected $titulo_transaccion = 'Transacciones Realizadas';
    protected $tituloNuevaTransaccion = 'Registrar Nuevo Gasto';
    protected $tituloEliminar  = 'Eliminar persona';
    protected $rutas           = array('create' => 'caja.create', 
            'edit'   => 'caja.edit', 
            'delete' => 'caja.eliminar',
            'search' => 'caja.buscar',
            'index'  => 'caja.index',
            'nuevatransaccion'   => 'caja.nuevatransaccion',
            'detalle'   => 'caja.detalle',
            'search1' => 'caja.detalle',
            'nuevomovimiento'   => 'caja.nuevomovimiento',

            'cargarreapertura'   => 'caja.cargarreapertura',
            'guardarreapertura' => 'caja.guardarreapertura',
            'cargarreporte' => 'caja.cargarreporte',
            'generarreportes' => 'caja.generarreportes',
            'cargarselect' => 'encuesta.cargarselect',
            'cargarselecttransaccion' => 'caja.cargarselecttransaccion',
            'buscartransaccion'=> 'caja.buscartransaccion',

            'reporteingresosPDF' => 'caja.reporteingresosPDF',
            'reporteegresosPDF' => 'caja.reporteegresosPDF',
            'listpersonas' =>'caja.listpersonas'
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
        $entidad          = 'Caja';
        $titulo             = Libreria::getParam($request->input('titulo'));
        $resultado        = Caja::listar($titulo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Apertura', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cierre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto Ini.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto Cie.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto Dif.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Movimiento', 'numero' => '2');
        $cabecera[]       = array('valor' => 'Reporte', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
        $caja_last = Caja::all()->last();

        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_cerrarCaja = $this->tituloCerrarCaja;
        $titulo_transaccion = $this->titulo_transaccion;
        $titulo_nuevomovimiento = $this->titulo_nuevomovimiento;
        $titulo_reapertura = $this->titulo_reaperturar;
        $titulo_reporte = $this->titulo_reporte;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar','titulo_cerrarCaja','titulo_nuevomovimiento','titulo_transaccion' ,'ruta','titulo_reapertura','titulo_reporte','caja_last'));
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
        $entidad          = 'Caja';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $titulo_nuevomovimiento = $this->titulo_nuevomovimiento;
        $titulo_reapertura = $this->titulo_reaperturar;
        $titulo_reporte = $this->titulo_reporte;
        $ruta             = $this->rutas;
        $listCaja = Caja::listCaja();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar','titulo_nuevomovimiento', 'ruta','listCaja','titulo_reapertura','titulo_reporte'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $caja_last = Caja::All()->last();
        $ingresos = (count($caja_last) != 0)?$caja_last->monto_cierre:0;
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Caja';
        $count_caja = Caja::where('estado','C')->count();
        if(strlen($count_caja) == 1){
            $titulo = "Caja 000".$count_caja;
        }
        if(strlen($count_caja) == 2){
            $titulo = "Caja 00".$count_caja;
        }
        if(strlen($count_caja) == 3){
            $titulo = "Caja 0".$count_caja;
        }
        if(strlen($count_caja) == 4){
            $titulo = "Caja ".$count_caja;
        }
        $caja        = null;
        $formData       = array('caja.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('caja', 'formData', 'entidad', 'boton', 'listar','ingresos','titulo'));
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
            'fecha_horaApert'        => 'required|max:100',
            'hora_apertura'      => 'required|max:100',
            'monto_iniciado'    => 'required|max:100',
            'titulo'    => 'required|max:200'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            //apertura una nueva caja
            $caja               = new Caja();
            $caja->titulo        = $request->input('titulo');
            $caja->descripcion        = $request->input('descripcion');
            $caja->fecha_horaApert        = $request->input('fecha_horaApert').date(" H:i:s");
            $caja->monto_iniciado        = $request->input('monto_iniciado');
            $caja->estado        = 'A';//abierto
            $caja->persona_id        = Caja::getIdPersona();
            $caja->save();
        });

        $error =  $this->actualizardatosahorros($request);
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
        $result = DB::table('caja')->where('id', $id)->first();
        $ingresos = $result->monto_iniciado;
        $egresos = 0;
        $diferencia = 0;
        $saldo = Transaccion::getsaldo($id)->get();
        for($i = 0; $i<count( $saldo ); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }

        $diferencia= $ingresos-$egresos;
        $monto_cierre=0;
        $monto_cierre = round(($result->monto_iniciado-$diferencia),1);

        //fecha de apertura de caja
        $fecha_caja = Date::parse($result->fecha_horaApert)->format('Y-m-d');

        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true){
            return $existe;
        }

        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Caja';
        $caja = Caja::find($id);

        $formData       = array('caja.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Cerrar Caja';
        return view($this->folderview.'.cierrecaja')->with(compact( 'formData', 'caja','listar','entidad', 'boton','diferencia','monto_cierre','fecha_caja'));
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
        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'diferencia_monto'    => 'required|max:200'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $caja                 = Caja::find($id);
            $caja->descripcion        = $request->get('descripcion');
            $caja->fecha_horaCierre        = $request->input('fecha_horaApert').date(" H:i:s");
            $caja->monto_cierre        = $request->get('monto_cierre');
            $caja->diferencia_monto        = $request->get('diferencia_monto');
            $caja->estado        = 'C';//cierre
            $caja->save();
        });
        
        return is_null($error) ? "OK" : $error;
    }


    /**REAPERTURAR CAJA LA ULTIMA CAJA CERRADA*/
    public function cargarreapertura($id, $listarLuego){
        $caja = DB::table('caja')->where('id', $id)->first();
        $monto_inicio = $caja->monto_iniciado;
        $monto_cierre = 0;
        $diferencia = 0;
        $cboEstado        = array('A'=>'Reaperturar');
        $entidad  = 'Caja';
        $ruta = $this->rutas;
        $titulo_reapertura = $this->titulo_reaperturar;
        return view($this->folderview.'.reaperturar')->with(compact('caja','entidad', 'ruta', 'titulo_reapertura','cboEstado','monto_inicio','monto_cierre','diferencia'));
    }

    /**ACTUALIZAR REAPERTURA DE LA ULTIMA CAJA */
    public function guardarreapertura(Request $request)
    {
        $caja_id = $request->get('caja_id');
        $monto_inicio = Libreria::getParam($request->input('monto_inicio'));
        $monto_cierre = Libreria::getParam($request->input('monto_cierre'));
        $estado = Libreria::getParam($request->input('estado'));

        $existe = Libreria::verificarExistencia($caja_id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        
        $error = DB::transaction(function() use($request, $caja_id, $monto_inicio, $monto_cierre, $estado){
            $caja                 = Caja::find($caja_id);
            $caja->fecha_horaCierre        = null;
            $caja->monto_cierre        = $monto_cierre;
            $caja->diferencia_monto        = 0.0;
            $caja->estado        = $estado;//cierre
            $caja->save();
        });
        
        return is_null($error) ? "OK" : $error;
    }

    /**CARGAR REPORTE */
    public function cargarreporte(){
        $cboTipo        = [''=>'Seleccione'] + array('I'=>'Ingresos', 'E'=>'Egresos');
        $entidad  = 'Caja';
        $ruta = $this->rutas;
        $titulo_reporte = $this->titulo_reporte;
        return view($this->folderview.'.reportes')->with(compact('entidad', 'ruta', 'titulo_reporte','cboTipo'));
    }

    /**GENERAR REPORTES DE CAJA Y EGRESOS E INGRESOS DEL MES */
    public function generarreportes(Request $request)
    {
        $res = null;
        $mes = $request->get('mes');
        $res = is_null($res) ? "OK" : $res;
        $respuesta = array($res, $mes);
        return $respuesta;
    }



    /*********************************** ACTUALIZA AHORROS ************************************** */
    public function numero_meses($fecha_inico, $fecha_final){
        $anio_menor= date("Y", strtotime($fecha_inico));
        $mes_menor= date("m", strtotime($fecha_inico));

        $anio_mayor= date("Y", strtotime($fecha_final));
        $mes_mayor= date("m", strtotime($fecha_final));
        $num_meses = 0;
        if($anio_mayor == $anio_menor){
            $num_meses = $mes_mayor - $mes_menor;
        }else if($anio_mayor > $anio_menor){
            $diferencia_anios = $anio_mayor - $anio_menor;
            $num_meses = 12 - $mes_menor + (12 * ($diferencia_anios - 1)) + $mes_mayor;
            
        }
        return $num_meses;
    }

    public function fecha_mes_siguiente($fecha){
        $fecha_siguiente = date("Y-m-d",strtotime($fecha."+ 1 month"));
        if($this->numero_meses($fecha, $fecha_siguiente) > 1){
            $fecha_siguiente = date("Y-m-d",strtotime($fecha_siguiente."- 4 day"));
            $fechap = new DateTime($fecha_siguiente);
            $fechap->modify('last day of this month');
            $fechap->format('Y-m-d');
            $fecha_siguiente = date("Y-m-d",strtotime($fechap->format('Y')."-".$fechap->format('m')."-".$fechap->format('d')));
        }
    
        return $fecha_siguiente;
    
    }

    /*********************************** ACTUALIZA AHORROS ************************************** */
    /*public function actualizardatosahorrosNuevo(Request $request){

        $configuracion = Configuraciones::all()->last();
        $tasa_interes_ahorro  = $configuracion->tasa_interes_ahorro;
        $lista_ahorros = Ahorros::where("estado","=",'P')->get();

        $error1 = null;
        if(count($lista_ahorros)>0){
            $fechinit = date('Y-m-d', strtotime($lista_ahorros[0]->fechai));
            $NumeroMeses = $this->numero_meses($fechinit, $request->input('fecha_horaApert'));
            $nuevafecha_actual = $request->input('fecha_horaApert').' '.date('H:i:s');
     
            $caja = Caja::all()->last();
            $error1 = DB::transaction(function() use($request, $fechinit, $lista_ahorros, $tasa_interes_ahorro, $NumeroMeses,$caja){
                $fecha_nueva =$fechinit;
                if($NumeroMeses >0){
                    for($j=0; $j < $NumeroMeses; $j++){

                        $fecha_nueva = date("Y-m-d",strtotime($fecha_nueva."+ 1 month"));
                        $lista_ahorros = Ahorros::where("estado","=",'P')->get();

                        foreach ($lista_ahorros as $key => $value) {
                            
                            if($value->id != null){
                                $ahorro_ant = Ahorros::find($value->id);
                                $ahorro_ant->fechaf = $fecha_nueva;
                                $ahorro_ant->estado = 'C';
                                $ahorro_ant->save();

                                $ahorro = new Ahorros();
                                $ahorro->fechai = $fecha_nueva;
                                $ahorro->capital = $value->capital + $tasa_interes_ahorro * $value->capital;
                                $ahorro->interes = $tasa_interes_ahorro * $value->capital;
                                $ahorro->persona_id = $value->persona_id;
                                $ahorro->estado = 'P';
                                $ahorro->save();

                                $transaccion = new Transaccion();
                                $transaccion->monto = 0;
                                $transaccion->concepto_id = 16;
                                $transaccion->fecha = $fecha_nueva;
                                $transaccion->interes_ahorro =$tasa_interes_ahorro * $value->capital;
                                $transaccion->persona_id = $value->persona_id;
                                $transaccion->caja_id = $caja->id;
                                $transaccion->usuario_id = Credito::idUser();
                                $transaccion->save();

                                $transaccion = new Transaccion();
                                $transaccion->monto = 0;
                                $transaccion->concepto_id = 17;
                                $transaccion->fecha = $fecha_nueva;
                                $transaccion->interes_ahorro =$tasa_interes_ahorro * $value->capital;
                                $transaccion->persona_id = $value->persona_id;
                                $transaccion->caja_id = $caja->id;
                                $transaccion->usuario_id = Credito::idUser();
                                $transaccion->save();
                            }
                        }
                        
                    }
                }
            });
        }
        return $error1;
    }*/

    public function actualizardatosahorros(Request $request){

        $configuracion = Configuraciones::all()->last();
        $tasa_interes_ahorro  = $configuracion->tasa_interes_ahorro;
        $lista_ahorros = Ahorros::where("estado","=",'P')->get();

        $error1 = null;
        if(count($lista_ahorros)>0){
            $fechinit = date('Y-m-d', strtotime($lista_ahorros[0]->fechai));
            $NumeroMeses = $this->numero_meses($fechinit, $request->input('fecha_horaApert'));
            $nuevafecha_actual = $request->input('fecha_horaApert').' '.date('H:i:s');
     
            $caja = Caja::all()->last();
            $error1 = DB::transaction(function() use($request, $fechinit, $lista_ahorros, $tasa_interes_ahorro, $NumeroMeses,$caja, $nuevafecha_actual){
                $fecha_nueva =$fechinit;
                if($NumeroMeses >0){
                    for($j=0; $j < $NumeroMeses; $j++){

                        if($this->numero_meses($fecha_nueva ,$nuevafecha_actual)> 1){
                            $fecha_nueva = $this->fecha_mes_siguiente($fecha_nueva);
                        }else{
                            $fecha_nueva = $nuevafecha_actual;
                        }
                        

                        $lista_ahorros = Ahorros::where("estado","=",'P')->get();
                        foreach ($lista_ahorros as $key => $value) {
                            
                            if($value->id != null){
                                $ahorro_ant = Ahorros::find($value->id);
                                $ahorro_ant->fechaf = $fecha_nueva;
                                $ahorro_ant->estado = 'C';
                                $ahorro_ant->save();

                                $ahorro = new Ahorros();
                                $ahorro->fechai = $fecha_nueva;
                                $ahorro->capital = $value->capital + $tasa_interes_ahorro * $value->capital;
                                $ahorro->interes = $tasa_interes_ahorro * $value->capital;
                                $ahorro->persona_id = $value->persona_id;
                                $ahorro->estado = 'P';
                                $ahorro->save();

                                $transaccion = new Transaccion();
                                $transaccion->monto = 0;
                                $transaccion->concepto_id = 16;
                                $transaccion->fecha = $fecha_nueva;
                                $transaccion->interes_ahorro =$tasa_interes_ahorro * $value->capital;
                                $transaccion->persona_id = $value->persona_id;
                                $transaccion->caja_id = $caja->id;
                                $transaccion->usuario_id = Credito::idUser();
                                $transaccion->save();

                                $transaccion = new Transaccion();
                                $transaccion->monto = 0;
                                $transaccion->concepto_id = 17;
                                $transaccion->fecha = $fecha_nueva;
                                $transaccion->interes_ahorro =$tasa_interes_ahorro * $value->capital;
                                $transaccion->persona_id = $value->persona_id;
                                $transaccion->caja_id = $caja->id;
                                $transaccion->usuario_id = Credito::idUser();
                                $transaccion->save();
                            }
                        }
                        
                    }
                }
            });
        }
        return $error1;
    }


    /************************************* Fin actualizar *************************************** */
    //CONTROL DETALLE DE LA CAJA

    public function detalle($id, Request $request)
    {
        $result = DB::table('caja')->where('id', $id)->first();
        //calculos
        $ingresos =$result->monto_iniciado;
        $egresos=0;
        $diferencia =0;
        $saldo = Transaccion::getsaldo($id)->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }
        $diferencia= $ingresos-$egresos;
        $cboTipo1        = [''=>'Todo']+ array('I'=>'Ingreso','E'=>'Egreso');
        $cboConceptos1        = [''=>'Todo'];

        $concepto_id             = Libreria::getParam(-1);
        $tituloNuevaTransaccion = $this->tituloNuevaTransaccion;
        $ruta             = $this->rutas;
        $inicio           = 0;
        $entidad ='Transaccion';
        return view($this->folderview.'.transaccion')->with(compact('concepto_id','entidad', 'ruta', 'inicio', 'id','saldo','ingresos','egresos','diferencia','cboConceptos1','cboTipo1','tituloNuevaTransaccion'));
    }
    ///************** NUevo metodo */

    public function buscartransaccion(Request $request){
    
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad ='Transaccion';
        $idcaja  = Libreria::getParam($request->input('idcaja'));
        $concepto_id      = Libreria::getParam($request->input('concepto_id1'));
        $resultado        = Transaccion::listar1( $idcaja, $concepto_id);
        $lista            = $resultado->get();
     

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'FECHA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'MONTO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'CONCEPTO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'TIPO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'USUARIO/CLIENTE', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DESCRIPCION', 'numero' => '1');
      
        $ruta             = $this->rutas;
        $inicio           = 0;

        $month = array(1=>'Enero',
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
        
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.listTransac')->with(compact('concepto_id','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio', 'idcaja','month'));
        }
        return view($this->folderview.'.listTransac')->with(compact('concepto_id','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio', 'idcaja','month'));
    
    }


    //PARA REGISTRAR NUEVO MOVIMIENTO PARA GASTOS, AHORROS DESDE LA CAJA
    public function nuevomovimiento($id, Request $request)
    {
        $result = DB::table('caja')->where('id', $id)->first();
        $ingresos = $result->monto_iniciado;
        $egresos = 0;
        $diferencia = 0;
        $saldo = Transaccion::getsaldo($id)->get();
        for($i = 0; $i<count( $saldo ); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }

        $diferencia= $ingresos-$egresos;

        //fecha caja 
        $fecha_caja = Date::parse($result->fecha_horaApert)->format('Y-m-d');

        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $caja = Caja::find($id);

        if (isset($listarParam)) {
            $listar = $listarParam;
        }
        $cboPers = array(0=>'Seleccione...');

        $entidad        = 'Transaccion';
        $ruta = $this->rutas;
        $cboTipo        = [''=>'Seleccione']+ array('I'=>'Ingreso','E'=>'Egreso');
        $cboConceptos        = [''=>'Seleccione'];
        $boton          = 'Registrar';
        return view($this->folderview.'.nuevomovimiento')->with(compact('caja', 'entidad', 'id','boton', 'listar','cboTipo','cboConceptos','cboPers','ruta','diferencia','result','fecha_caja'));
    }



    public function registrarmovimiento(Request $request, $id)
    {
        $idcaja = $id;
        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'fecha'        => 'required|max:100',
            'concepto_id'        => 'required|max:100',
            'total'        => 'required|max:100'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
       }

       $listar        = Libreria::getParam($request->input('listar'), 'NO');

       $tipo_id = $request->input('tipo_id');
       $persona_id = $request->get('selectnom');
       if($tipo_id == 'I'){
           if($persona_id != ''){
                $persona = Persona::find($persona_id);
                $error = DB::transaction(function() use($request, $id, $persona){
                    $gastos = new Gastos();
                    $gastos->monto = $request->get('total');
                    $gastos->fecha = $request->get('fecha');
                    $gastos->concepto = $request->get('concepto_id');
                    $gastos->descripcion =  $request->get('comentario');
                    $gastos->save();
        
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $request->get('fecha');
                    $transaccion->monto = $request->get('total');
                    $transaccion->concepto_id = $request->get('concepto_id');
                    $transaccion->descripcion =  $request->get('comentario')." ".$persona->nombres;
                    $transaccion->usuario_id =Caja::getIdPersona();
                    $transaccion->caja_id = $id;
                    $transaccion->save();
                });
           }else{
                $error = DB::transaction(function() use($request, $id){
                    $gastos = new Gastos();
                    $gastos->monto = $request->get('total');
                    $gastos->fecha = $request->get('fecha');
                    $gastos->concepto = $request->get('concepto_id');
                    $gastos->descripcion =  $request->get('comentario');
                    $gastos->save();
        
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $request->get('fecha');
                    $transaccion->monto = $request->get('total');
                    $transaccion->concepto_id = $request->get('concepto_id');
                    $transaccion->descripcion =  $request->get('comentario');
                    $transaccion->usuario_id =Caja::getIdPersona();
                    $transaccion->caja_id = $id;
                    $transaccion->save();
                });
           }
            
       }
       if($tipo_id == 'E'){
            $persona_id = $request->get('selectnom');
            if($persona_id != 0){
                $persona = Persona::find($persona_id);
                $error = DB::transaction(function() use($request, $id, $persona){
                    $gastos = new Gastos();
                    $gastos->monto = $request->get('total');
                    $gastos->fecha = $request->get('fecha');
                    $gastos->concepto = $request->get('concepto_id');
                    $gastos->descripcion =  $request->get('comentario');
                    $gastos->save();
    
    
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $request->get('fecha');
                    $transaccion->monto = $request->get('total');
                    $transaccion->concepto_id = $request->get('concepto_id');
                    $transaccion->tipo_egreso = $request->get('editable');
                    $transaccion->descripcion =  $request->get('comentario')." ".$persona->nombres;
                    $transaccion->usuario_id =Caja::getIdPersona();
                    $transaccion->caja_id = $id;
                    $transaccion->save();
                });
            }else{
                $error = DB::transaction(function() use($request, $id){
                    $gastos = new Gastos();
                    $gastos->monto = $request->get('total');
                    $gastos->fecha = $request->get('fecha');
                    $gastos->concepto = $request->get('concepto_id');
                    $gastos->descripcion =  $request->get('comentario');
                    $gastos->save();
    
    
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $request->get('fecha');
                    $transaccion->monto = $request->get('total');
                    $transaccion->concepto_id = $request->get('concepto_id');
                    $transaccion->tipo_egreso = $request->get('editable');
                    $transaccion->descripcion =  $request->get('comentario');
                    $transaccion->usuario_id =Caja::getIdPersona();
                    $transaccion->caja_id = $id;
                    $transaccion->save();
                });
            }
            
            
       }      
        return is_null($error) ? "OK" : $error;
    }

    //para seleccionar concepto
    public function cargarselect($idselect, Request $request)
    {
        echo $idselect;
        $entidad = $request->get('entidad');
        $t = '';
        $tt = '';

        if($request->get('t') == ''){
            $t = '_';
            $tt = '2';
        }

        $retorno = '<select class="form-control input-sm" id="' . $t . $entidad . '_id" name="';
        $cbo = Concepto::select('id', 'titulo')
            ->where('tipo', '=', $idselect)
            ->where('id', '!=', 10)
            ->where('id', '!=', 3)
            ->where('id', '!=', 6)
            ->where('id', '!=', 1)
            ->where('id', '!=', 2)
            ->where('id', '!=', 4)
            ->where('id', '!=', 11)
            ->where('id', '!=', 20)
            ->where('id', '!=', 5)
            ->where('id', '!=', 8)
            ->where('id', '!=', 16)
            ->where('id', '!=', 17)
            ->where('id', '!=', 19)
            ->where('id', '!=', 12)
            ->where('id', '!=', 18)
            ->get();
        $retorno .= '><option value="" selected="selected">Seleccione</option>';

        foreach ($cbo as $row) {
            $retorno .= '<option value="' . $row['id'] .  '">' . $row['titulo'] . '</option>';
        }
        $retorno .= '</select></div>';

        echo $retorno;
    }


    public function cargarselecttransaccion($idselect, Request $request ){
        echo $idselect;
        $entidad = $request->get('entidad');
        $t = '';
        $tt = '';

        if($request->get('t') == ''){
            $t = '_';
            $tt = '2';
        }
        
        $retorno = '<select class="form-control input-sm" id="'.$t.$entidad. '_id" name="';
        $cbo = Concepto::select('id','titulo')
                        ->where('tipo',$idselect)
                        ->where('id','!=',16)
                        ->where('id','!=',17)
                        ->where('id','!=',2)
                        ->where('id','!=',10)
                        ->where('deleted_at',null)
                        ->get();
        $retorno .='><option value="" selected="selected">Todo</option>';
        foreach($cbo as $row){
            $retorno .='<option value="' . $row['id'] . '">' .$row['titulo'] . '</option>';
        }
        $retorno .= '/selected></div>';
        echo $retorno;
    }

    //metodo para generar voucher en pdf
    public function reportecajaPDF($id, Request $request)
    {    
        $caja = DB::table('caja')->where('id', $id)->first();
        //calculos
        $ingresos =$caja->monto_iniciado;
        $egresos=0;
        $diferencia =0;
        $saldo = Transaccion::getsaldo($id)->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }
        $diferencia= $ingresos-$egresos;

        $concepto_id             = Libreria::getParam(-1);
        $resultado        = Transaccion::listar($id);
        $lista            = $resultado->get();
        $persona = DB::table('persona')->where('id', $caja->persona_id)->first();

        $titulo = "reporte ".$caja->titulo;
        $view = \View::make('app.caja.reportecajaPDF')->with(compact('lista', 'id', 'caja','diferencia', 'result','persona','ingresos','egresos'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('L','A4',0);
        PDF::SetTopMargin(5);
        //PDF::SetLeftMargin(40);
        //PDF::SetRightMargin(40);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
 
        PDF::Output($titulo.'.pdf', 'I');
    }

    //---------------------------------------------------------------------------METODO PARA GENERAR REPORTE DE INGRESOS
    public function reporteingresosPDF($fecha)
    {
        //fecha en mes y año separados
        $fechaf = explode("-", $fecha);
        $anio =  $fechaf[0];
        $month = $fechaf[1];

        $lista = Caja::listIngresos($anio,$month)->get();
        //evaluar mes 
        $mes="";
        $mesItm="";
        switch($month){
            case '01':
                $mes = "ENERO";
                $mesItm = "Ene";
                break;
            case '02':
                $mes = "FEBRERO";
                $mesItm = "Feb";
                break;
            case '03':
                $mes = "MARZO";
                $mesItm = "Mar";
                break;
            case '04':
                $mes = "ABRIL";
                $mesItm = "Abr";
                break;
            case '05':
                $mes = "MAYO";
                $mesItm = "May";
                break;
            case '06':
                $mes = "JUNIO";
                $mesItm = "Jun";
                break;
            case '07':
                $mes = "JULIO";
                $mesItm = "Jul";
                break;
            case '08':
                $mes = "AGOSTO";
                $mesItm = "Ago";
                break;
            case '09':
                $mes = "SETIEMBRE";
                $mesItm = "Set";
                break;
            case '10':
                $mes = "OCTUBRE";
                $mesItm = "Oct";
                break;
            case '11':
                $mes = "NOVIEMBRE";
                $mesItm = "Nov";
                break;
            default :
                $mes = "DICIEMBRE";
                $mesItm = "Dic";
                break;
        }

        //calculo del total de ingresos del mes
        $sum_deposito_ahorros_mes_actual=0;
        $sum_pagos_de_capital_mes_actual=0;
        $sum_interese_recibidos_mes_actual=0;
        $sum_acciones_mes_actual=0;
        $sum_otros_mes_actual=0;
        $sum_ingresos_totales_mes_actual=0;
        if(count($lista) >0 ){
            for($i=0; $i<count($lista); $i++){
                $sum_deposito_ahorros_mes_actual += $lista[$i]->deposito_ahorros + $lista[$i]->monto_ahorro;
                $sum_pagos_de_capital_mes_actual += $lista[$i]->pagos_de_capital;
                $sum_interese_recibidos_mes_actual += $lista[$i]->intereces_recibidos;
                $sum_acciones_mes_actual += $lista[$i]->acciones;
                $sum_otros_mes_actual += $lista[$i]->comision_voucher;
            }
            $sum_ingresos_totales_mes_actual=($sum_deposito_ahorros_mes_actual+$sum_pagos_de_capital_mes_actual+$sum_interese_recibidos_mes_actual+$sum_acciones_mes_actual+$sum_otros_mes_actual);
        }else{
            $sum_deposito_ahorros_mes_actual=0;
            $sum_pagos_de_capital_mes_actual=0;
            $sum_interese_recibidos_mes_actual=0;
            $sum_acciones_mes_actual=0;
            $sum_otros_mes_actual=0;
            $sum_ingresos_totales_mes_actual=0;
        }

        //lista de ingresos por concepto
        $lista_ingresos_por_concepto = Caja::listIngresos_por_concepto($month,$anio)->get();
        // calculo del total de ingresos del mes actual por concepto
        $sum_por_concepto_actual=0;
        if(count($lista_ingresos_por_concepto) >0 ){
            for($i=0; $i<count($lista_ingresos_por_concepto); $i++){
                $sum_por_concepto_actual += $lista_ingresos_por_concepto[$i]->transaccion_monto;
            }
            $sum_ingresos_totales_mes_actual += $sum_por_concepto_actual;
            $sum_otros_mes_actual += $sum_por_concepto_actual;
        }
        



        //calculo del total de ingresos acumulados al mes anterior
        //--primero identifico la fecha de la primera caja que fue aperturada
        $fecha_completa= $fecha."-01";

        //$fechai =  date("d-m-Y",strtotime($fecha_completa."- 1 days"));

        $lista_mes_anterior = Caja::listIngresosastamesanterior($fecha_completa)->get();


        $sum_deposito_ahorros_asta_mes_anterior=0;
        $sum_pagos_de_capital_asta_mes_anterior=0;
        $sum_interese_recibidos_asta_mes_anterior=0;
        $sum_acciones_asta_mes_anterior=0;
        $sum_otros_asta_mes_anterior=0;
        $sum_ingresos_totales_asta_mes_anterior=0;
        if(count($lista_mes_anterior) >0 ){
            for($i=0; $i<count($lista_mes_anterior); $i++){
                $sum_deposito_ahorros_asta_mes_anterior += $lista_mes_anterior[$i]->deposito_ahorros + $lista_mes_anterior[$i]->monto_ahorro;
                $sum_pagos_de_capital_asta_mes_anterior += $lista_mes_anterior[$i]->pagos_de_capital;
                $sum_interese_recibidos_asta_mes_anterior += $lista_mes_anterior[$i]->intereces_recibidos;
                $sum_acciones_asta_mes_anterior += $lista_mes_anterior[$i]->acciones;
                $sum_otros_asta_mes_anterior += $lista_mes_anterior[$i]->comision_voucher;
            }
            $sum_ingresos_totales_asta_mes_anterior=($sum_deposito_ahorros_asta_mes_anterior+$sum_pagos_de_capital_asta_mes_anterior+$sum_interese_recibidos_asta_mes_anterior+$sum_acciones_asta_mes_anterior+$sum_otros_asta_mes_anterior);
        }else{
            $sum_deposito_ahorros_asta_mes_anterior=0;
            $sum_pagos_de_capital_asta_mes_anterior=0;
            $sum_interese_recibidos_asta_mes_anterior=0;
            $sum_acciones_asta_mes_anterior=0;
            $sum_otros_asta_mes_anterior=0;
            $sum_ingresos_totales_asta_mes_anterior=0;
        }

        //lista de ingresos por concepto
        $lista_ingresos_por_concepto_mes_anterior = Caja::listIngresos_por_concepto_asta_mes_anterior($fecha_completa)->get();
        // calculo del total de ingresos del mes actual por concepto
        $sum_por_concepto_mes_anterior=0;
        if(count($lista_ingresos_por_concepto_mes_anterior) >0 ){
            for($i=0; $i<count($lista_ingresos_por_concepto_mes_anterior); $i++){
                $sum_por_concepto_mes_anterior += $lista_ingresos_por_concepto_mes_anterior[$i]->transaccion_monto;
            }
            $sum_ingresos_totales_asta_mes_anterior += $sum_por_concepto_mes_anterior;
            $sum_otros_asta_mes_anterior += $sum_por_concepto_mes_anterior;
        }



        //calculo de ingresos acumulados asta la fecha
        $sum_deposito_ahorros_acumulados=0;
        $sum_pagos_de_capital_acumulados=0;
        $sum_interese_recibidos_acumulados=0;
        $sum_acciones_acumulados=0;
        $sum_otros_acumulados=0;
        $sum_ingresos_totales_acumulados=0;

        //-------suma
        $sum_deposito_ahorros_acumulados=($sum_deposito_ahorros_mes_actual+$sum_deposito_ahorros_asta_mes_anterior);
        $sum_pagos_de_capital_acumulados=($sum_pagos_de_capital_mes_actual+$sum_pagos_de_capital_asta_mes_anterior);
        $sum_interese_recibidos_acumulados=($sum_interese_recibidos_mes_actual+$sum_interese_recibidos_asta_mes_anterior);
        $sum_acciones_acumulados=($sum_acciones_mes_actual+$sum_acciones_asta_mes_anterior);
        $sum_otros_acumulados=($sum_otros_mes_actual+$sum_otros_asta_mes_anterior);

        $sum_ingresos_totales_acumulados=($sum_ingresos_totales_mes_actual+$sum_ingresos_totales_asta_mes_anterior);


        //$persona = DB::table('persona')->where('id', $caja->persona_id)->first();

        $titulo = "reporte ".$mes."-".$anio."_Ingresos";
        $view = \View::make('app.reportes.reporteIngresoPDF')->with(compact('lista','lista_ingresos_por_concepto' ,'id', 'caja', 'mes','anio','mesItm','sum_deposito_ahorros_mes_actual',
                                                                            'sum_pagos_de_capital_mes_actual','sum_interese_recibidos_mes_actual','sum_acciones_mes_actual','sum_otros_mes_actual','sum_ingresos_totales_mes_actual','sum_por_concepto_actual',
                                                                        'sum_deposito_ahorros_asta_mes_anterior','sum_pagos_de_capital_asta_mes_anterior','sum_interese_recibidos_asta_mes_anterior','sum_acciones_asta_mes_anterior','sum_otros_asta_mes_anterior','sum_ingresos_totales_asta_mes_anterior',
                                                                    'sum_deposito_ahorros_acumulados','sum_pagos_de_capital_acumulados','sum_interese_recibidos_acumulados','sum_acciones_acumulados','sum_otros_acumulados','sum_ingresos_totales_acumulados'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('L','A4',0);
        PDF::SetTopMargin(5);
        //PDF::SetLeftMargin(40);
        //PDF::SetRightMargin(40);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

    //---------------------------------------------------------------------------METODO PARA GENERAR REPORTE DE EGRESOS
    public function reporteegresosPDF($fecha, Request $request)
    {    
        //fecha en mes y año separados
        $fechaf = explode("-", $fecha);
        $anio =  $fechaf[0];
        $month = $fechaf[1];

        $lista = Caja::listEgresos($month,$anio)->get();

        //evaluar mes 
        $mes="";
        $mesItm="";
        switch($month){
            case '1':
                $mes = "ENERO";
                $mesItm = "Ene";
                break;
            case '2':
                $mes = "FEBRERO";
                $mesItm = "Feb";
                break;
            case '3':
                $mes = "MARZO";
                $mesItm = "Mar";
                break;
            case '4':
                $mes = "ABRIL";
                $mesItm = "Abr";
                break;
            case '5':
                $mes = "MAYO";
                $mesItm = "May";
                break;
            case '6':
                $mes = "JUNIO";
                $mesItm = "Jun";
                break;
            case '7':
                $mes = "JULIO";
                $mesItm = "Jul";
                break;
            case '8':
                $mes = "AGOSTO";
                $mesItm = "Ago";
                break;
            case '9':
                $mes = "SETIEMBRE";
                $mesItm = "Set";
                break;
            case '10':
                $mes = "OCTUBRE";
                $mesItm = "Oct";
                break;
            case '11':
                $mes = "NOVIEMBRE";
                $mesItm = "Nov";
                break;
            default :
                $mes = "DICIEMBRE";
                $mesItm = "Dic";
                break;
        }

        //lista de egresos por personas


        //calculo del total de egresos del mes actual por persona
        $sum_retiro_ahorros_mes_actual=0;
        $sum_prestamo_de_capital_mes_actual=0;
        $sum_interes_pagado_mes_actual=0;
        $sum_otros_egresos_mes_actual =0;
        $sum_utilidad_distribuida =0;
        $sum_egresos_totales_mes_actual=0;
        if(count($lista) >0 ){
            for($i=0; $i<count($lista); $i++){
                $sum_retiro_ahorros_mes_actual += $lista[$i]->monto_ahorro;
                $sum_prestamo_de_capital_mes_actual += $lista[$i]->monto_credito;
                $sum_interes_pagado_mes_actual += $lista[$i]->interes_ahorro;
                $sum_utilidad_distribuida += $lista[$i]->utilidad_distribuida;
                $sum_otros_egresos_mes_actual += $lista[$i]->otros_egresos;
            }
            $sum_egresos_totales_mes_actual=($sum_retiro_ahorros_mes_actual+$sum_prestamo_de_capital_mes_actual+$sum_interes_pagado_mes_actual+$sum_otros_egresos_mes_actual+$sum_utilidad_distribuida);
        }else{
            $sum_retiro_ahorros_mes_actual=0;
            $sum_prestamo_de_capital_mes_actual=0;
            $sum_interes_pagado_mes_actual=0;
            $sum_egresos_totales_mes_actual=0;
            $sum_otros_egresos_mes_actual =0;
            $sum_utilidad_distribuida =0;
        }


        //lista de egresos por concepto(gastos administrativos)
        $lista_por_conceptoAdmin = Caja::listEgresos_por_conceptoAdmin($anio,$month)->get();

        // calculo del total de egresos del mes actual por concepto
        $sum_gasto_administrativo_mes_actual=0;
        if(count($lista_por_conceptoAdmin) >0 ){
            for($i=0; $i<count($lista_por_conceptoAdmin); $i++){
                $sum_gasto_administrativo_mes_actual += $lista_por_conceptoAdmin[$i]->transaccion_monto;
            }
            $sum_egresos_totales_mes_actual += $sum_gasto_administrativo_mes_actual;
        }

        //lista de egresos por concepto(otros gastos)
        $lista_por_conceptoOthers = Caja::listEgresos_por_conceptoOthers($anio,$month)->get();

        // calculo del total de egresos del mes actual por concepto

        if(count($lista_por_conceptoOthers) >0 ){
            for($i=0; $i<count($lista_por_conceptoOthers); $i++){
                $sum_otros_egresos_mes_actual += $lista_por_conceptoOthers[$i]->transaccion_monto;
            }
            $sum_egresos_totales_mes_actual += $sum_otros_egresos_mes_actual;
        }

        //calculo del total de egresos acumulados al mes anterior por persona

        //calculo del total de ingresos acumulados al mes anterior

        //identifico la fecha del mes anterior 
        $fecha_completa= $fecha."-01";
        //$fechainicial =  date("d-m-Y",strtotime($fecha_completa."- 1 days"));

        $lista_mes_anterior = Caja::listEgresos_asta_mes_anterior($fecha_completa)->get();

        $sum_retiro_ahorros_mes_anterior=0;
        $sum_prestamo_de_capital_mes_anterior=0;
        $sum_interes_pagado_mes_anterior=0;
        $sum_utilidad_distribuida_mes_anterior=0;
        $sum_egresos_totales_mes_anterior=0;

        if(count($lista_mes_anterior) >0 ){
            for($i=0; $i<count($lista_mes_anterior); $i++){
                $sum_retiro_ahorros_mes_anterior += $lista_mes_anterior[$i]->monto_ahorro;
                $sum_prestamo_de_capital_mes_anterior += $lista_mes_anterior[$i]->monto_credito;
                $sum_utilidad_distribuida_mes_anterior += $lista_mes_anterior[$i]->utilidad_distribuida;
                $sum_interes_pagado_mes_anterior += $lista_mes_anterior[$i]->interes_ahorro;
            }
            $sum_egresos_totales_mes_anterior=($sum_retiro_ahorros_mes_anterior+$sum_prestamo_de_capital_mes_anterior+$sum_interes_pagado_mes_anterior+$sum_utilidad_distribuida_mes_anterior);
        }else{
            $sum_retiro_ahorros_mes_anterior=0;
            $sum_prestamo_de_capital_mes_anterior=0;
            $sum_interes_pagado_mes_anterior=0;
            $sum_utilidad_distribuida_mes_anterior =0;
            $sum_egresos_totales_mes_anterior=0;
        }

        $lista_por_concepto_asta_mes_anteriorAdmin = Caja::listEgresos_por_concepto_asta_mes_anteriorAdmin($fecha_completa)->get();

        // calculo del total de egresos del mes actual por concepto
        $sum_gasto_administrativo_asta_mes_anterior=0;
        if(count($lista_por_concepto_asta_mes_anteriorAdmin) >0 ){
            for($i=0; $i<count($lista_por_concepto_asta_mes_anteriorAdmin); $i++){
                $sum_gasto_administrativo_asta_mes_anterior += $lista_por_concepto_asta_mes_anteriorAdmin[$i]->transaccion_monto;
            }
            $sum_egresos_totales_mes_anterior += $sum_gasto_administrativo_asta_mes_anterior;
        }

        $lista_por_concepto_asta_mes_anteriorOthers = Caja::listEgresos_por_concepto_asta_mes_anteriorOthers($fecha_completa)->get();
        //echo "lista ".$lista_por_concepto_asta_mes_anteriorOthers;
        // calculo del total de egresos del mes actual por concepto
        $sum_otros_egresos_asta_mes_anterior =0;
        if(count($lista_por_concepto_asta_mes_anteriorOthers) >0 ){
            for($i=0; $i<count($lista_por_concepto_asta_mes_anteriorOthers); $i++){
                $sum_otros_egresos_asta_mes_anterior += $lista_por_concepto_asta_mes_anteriorOthers[$i]->transaccion_monto;
            }
            $sum_egresos_totales_mes_anterior += $sum_otros_egresos_asta_mes_anterior;
        }


        //calculo de ingresos acumulados asta la fecha
        $sum_retiro_ahorros_acumulados=0;
        $sum_prestamo_de_capital_acumulados=0;
        $sum_interes_pagado_acumulados=0;
        $sum_egresos_totales_acumulados=0;

        $sum_gasto_administrativo_acumulado =0;

        //-------suma
        $sum_retiro_ahorros_acumulados=($sum_retiro_ahorros_mes_actual+$sum_retiro_ahorros_mes_anterior);
        $sum_prestamo_de_capital_acumulados=($sum_prestamo_de_capital_mes_actual+$sum_prestamo_de_capital_mes_anterior);
        $sum_interes_pagado_acumulados=($sum_interes_pagado_mes_actual+$sum_interes_pagado_mes_anterior);

        $sum_egresos_totales_acumulados=($sum_egresos_totales_mes_actual+$sum_egresos_totales_mes_anterior);

        $sum_gasto_administrativo_acumulado =($sum_gasto_administrativo_mes_actual + $sum_gasto_administrativo_asta_mes_anterior);



        //calculo de los ingresos y egresos y saldo-----------------------------------------------------------

        $listaingreso = Caja::listIngresos($anio,$month)->get();

        //calculo del total de ingresos del mes
        $sum_deposito_ahorros_mes_actual=0;
        $sum_pagos_de_capital_mes_actual=0;
        $sum_interese_recibidos_mes_actual=0;
        $sum_acciones_mes_actual=0;
        $sum_otros_mes_actual=0;
        $sum_ingresos_totales_mes_actual=0;
        if(count($listaingreso) >0 ){
            for($i=0; $i<count($listaingreso); $i++){
                $sum_deposito_ahorros_mes_actual += $listaingreso[$i]->deposito_ahorros + $listaingreso[$i]->monto_ahorro;
                $sum_pagos_de_capital_mes_actual += $listaingreso[$i]->pagos_de_capital;
                $sum_interese_recibidos_mes_actual += $listaingreso[$i]->intereces_recibidos;
                $sum_acciones_mes_actual += $listaingreso[$i]->acciones;
                $sum_otros_mes_actual += $listaingreso[$i]->comision_voucher;
            }
            $sum_ingresos_totales_mes_actual=($sum_deposito_ahorros_mes_actual+$sum_pagos_de_capital_mes_actual+$sum_interese_recibidos_mes_actual+$sum_acciones_mes_actual+$sum_otros_mes_actual);
        }else{
            $sum_deposito_ahorros_mes_actual=0;
            $sum_pagos_de_capital_mes_actual=0;
            $sum_interese_recibidos_mes_actual=0;
            $sum_acciones_mes_actual=0;
            $sum_otros_mes_actual=0;
            $sum_ingresos_totales_mes_actual=0;
        }

        //lista de ingresos por concepto
        $lista_ingresos_por_concepto = Caja::listIngresos_por_concepto($month,$anio)->get();
        // calculo del total de ingresos del mes actual por concepto
        $sum_por_concepto_actual=0;
        if(count($lista_ingresos_por_concepto) >0 ){
            for($i=0; $i<count($lista_ingresos_por_concepto); $i++){
                $sum_por_concepto_actual += $lista_ingresos_por_concepto[$i]->transaccion_monto;
            }
            $sum_ingresos_totales_mes_actual += $sum_por_concepto_actual;
            $sum_otros_mes_actual += $sum_por_concepto_actual;
        }
        
        //calculo del total de ingresos acumulados al mes anterior
        //--primero identifico la fecha de la primera caja que fue aperturada
        
        $fecha_completa= $fecha."-01";

        $lista_mes_anterioringreso = Caja::listIngresosastamesanterior($fecha_completa)->get();


        $sum_deposito_ahorros_asta_mes_anterior=0;
        $sum_pagos_de_capital_asta_mes_anterior=0;
        $sum_interese_recibidos_asta_mes_anterior=0;
        $sum_acciones_asta_mes_anterior=0;
        $sum_otros_asta_mes_anterior=0;
        $sum_ingresos_totales_asta_mes_anterior=0;
        if(count($lista_mes_anterioringreso) >0 ){
            for($i=0; $i<count($lista_mes_anterioringreso); $i++){
                $sum_deposito_ahorros_asta_mes_anterior += $lista_mes_anterioringreso[$i]->deposito_ahorros + $lista_mes_anterioringreso[$i]->monto_ahorro;
                $sum_pagos_de_capital_asta_mes_anterior += $lista_mes_anterioringreso[$i]->pagos_de_capital;
                $sum_interese_recibidos_asta_mes_anterior += $lista_mes_anterioringreso[$i]->intereces_recibidos;
                $sum_acciones_asta_mes_anterior += $lista_mes_anterioringreso[$i]->acciones;
                $sum_otros_asta_mes_anterior += $lista_mes_anterioringreso[$i]->comision_voucher;
            }
            $sum_ingresos_totales_asta_mes_anterior=($sum_deposito_ahorros_asta_mes_anterior+$sum_pagos_de_capital_asta_mes_anterior+$sum_interese_recibidos_asta_mes_anterior+$sum_acciones_asta_mes_anterior+$sum_otros_asta_mes_anterior);
        }else{
            $sum_deposito_ahorros_asta_mes_anterior=0;
            $sum_pagos_de_capital_asta_mes_anterior=0;
            $sum_interese_recibidos_asta_mes_anterior=0;
            $sum_acciones_asta_mes_anterior=0;
            $sum_otros_asta_mes_anterior=0;
            $sum_ingresos_totales_asta_mes_anterior=0;
        }

        //lista de ingresos por concepto
        $lista_ingresos_por_concepto_mes_anterior = Caja::listIngresos_por_concepto_asta_mes_anterior($fecha_completa)->get();
        // calculo del total de ingresos del mes actual por concepto
        $sum_por_concepto_mes_anterior=0;
        if(count($lista_ingresos_por_concepto_mes_anterior) >0 ){
            for($i=0; $i<count($lista_ingresos_por_concepto_mes_anterior); $i++){
                $sum_por_concepto_mes_anterior += $lista_ingresos_por_concepto_mes_anterior[$i]->transaccion_monto;
            }
            $sum_ingresos_totales_asta_mes_anterior += $sum_por_concepto_mes_anterior;
            $sum_otros_asta_mes_anterior += $sum_por_concepto_mes_anterior;
        }



        //calculo de ingresos acumulados asta la fecha
        $sum_deposito_ahorros_acumulados=0;
        $sum_pagos_de_capital_acumulados=0;
        $sum_interese_recibidos_acumulados=0;
        $sum_acciones_acumulados=0;
        $sum_otros_acumulados=0;
        $sum_ingresos_totales_acumulados=0;

        //-------suma
        $sum_deposito_ahorros_acumulados=($sum_deposito_ahorros_mes_actual+$sum_deposito_ahorros_asta_mes_anterior);
        $sum_pagos_de_capital_acumulados=($sum_pagos_de_capital_mes_actual+$sum_pagos_de_capital_asta_mes_anterior);
        $sum_interese_recibidos_acumulados=($sum_interese_recibidos_mes_actual+$sum_interese_recibidos_asta_mes_anterior);
        $sum_acciones_acumulados=($sum_acciones_mes_actual+$sum_acciones_asta_mes_anterior);
        $sum_otros_acumulados=($sum_otros_mes_actual+$sum_otros_asta_mes_anterior);

        $sum_ingresos_totales_acumulados = ($sum_ingresos_totales_mes_actual+$sum_ingresos_totales_asta_mes_anterior);


        //------------------------
        //inicializar variables
        $saldo_del_mes_anterior =0;
        $ingresos_del_mes=0;
        $total_ingresos_del_mes=0;
        $egresos_del_mes=0;
        $saldo=0;

        $saldo_del_mes_anterior =($sum_ingresos_totales_asta_mes_anterior- $sum_egresos_totales_mes_anterior);
        $ingresos_del_mes =  $sum_ingresos_totales_mes_actual;
        $total_ingresos_del_mes= ($saldo_del_mes_anterior + $ingresos_del_mes);  
        $egresos_del_mes=   $sum_egresos_totales_mes_actual;
        $saldo =    ($total_ingresos_del_mes-$egresos_del_mes);


        //-------------------------------------------
        //$persona = DB::table('persona')->where('id', $caja->persona_id)->first();

        $titulo = "reporte ".$mes."_Egresos";
        $view = \View::make('app.reportes.reporteEgresoPDF')->with(compact('lista','lista_por_conceptoAdmin','lista_por_conceptoOthers' ,'id', 'caja','day','mes','anio','mesItm','sum_retiro_ahorros_mes_actual',
                                                                            'sum_prestamo_de_capital_mes_actual','sum_interes_pagado_mes_actual','sum_egresos_totales_mes_actual','sum_gasto_administrativo_mes_actual','sum_otros_egresos_mes_actual','sum_utilidad_distribuida',
                                                                        'sum_retiro_ahorros_mes_anterior','sum_prestamo_de_capital_mes_anterior','sum_interes_pagado_mes_anterior','sum_egresos_totales_mes_anterior','sum_gasto_administrativo_asta_mes_anterior','sum_otros_egresos_asta_mes_anterior',
                                                                    'sum_retiro_ahorros_acumulados','sum_prestamo_de_capital_acumulados','sum_interes_pagado_acumulados','sum_egresos_totales_acumulados','sum_gasto_administrativo_acumulado', 
                                                                    'saldo_del_mes_anterior','ingresos_del_mes','total_ingresos_del_mes','egresos_del_mes','saldo','sum_ingresos_totales_acumulados'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('L','A4',0);
        PDF::SetTopMargin(5);
        //PDF::SetLeftMargin(40);
        //PDF::SetRightMargin(40);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

    //Metodo para redondear numero decimal
    public function rouNumber($numero, $decimales) { 
        $factor = pow(10, $decimales); 
        return (round($numero*$factor)/$factor);
    }

    public function listpersonas(Request $request){
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $tags = Persona::where("dni",'ILIKE', '%'.$term.'%')->orwhere("nombres",'ILIKE', '%'.$term.'%')->orwhere("apellidos",'ILIKE', '%'.$term.'%')->limit(5)->get();
        $formatted_tags = [];
        foreach ($tags as $tag) {
            $formatted_tags[] = ['id' => $tag->id, 'text' => $tag->nombres." ".$tag->apellidos];
        }

        return \Response::json($formatted_tags);
    }

}
