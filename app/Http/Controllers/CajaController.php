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
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use PDF;

class CajaController extends Controller
{
    protected $folderview      = 'app.caja';
    protected $tituloAdmin     = 'Caja';
    protected $tituloRegistrar = 'Apertura Caja';
    protected $tituloModificar = 'Modificar Caja';
    protected $titulo_nuevomovimiento = 'Registrar Nuevo Gasto';
    protected $tituloCerrarCaja = 'Cerrar Caja';
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
            'cargarselect' => 'encuesta.cargarselect',
            'buscartransaccion'=> 'caja.buscartransaccion'
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
        $cabecera[]       = array('valor' => 'Fecha-hora Apertura', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha-hora Cierre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto I.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto C.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto D.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '6');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_cerrarCaja = $this->tituloCerrarCaja;
        $titulo_transaccion = $this->titulo_transaccion;
        $titulo_nuevomovimiento = $this->titulo_nuevomovimiento;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar','titulo_cerrarCaja','titulo_nuevomovimiento','titulo_transaccion' ,'ruta'));
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
        $ruta             = $this->rutas;
        $listCaja = Caja::listCaja();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar','titulo_nuevomovimiento', 'ruta','listCaja'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $datosCaja = Caja::orderby('created_at','DESC')->take(1)->get();
        $ingresos = $datosCaja[0]->monto_cierre;

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Caja';
        $caja        = null;
        $formData       = array('caja.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('caja', 'formData', 'entidad', 'boton', 'listar','ingresos'));
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
            $caja               = new Caja();
            $caja->titulo        = $request->input('titulo');
            $caja->descripcion        = $request->input('descripcion');
            $caja->fecha_horaApert        = $request->input('fecha_horaApert').date(" H:i:s");
            $caja->monto_iniciado        = $request->input('monto_iniciado');
            $caja->estado        = 'A';//abierto
            $caja->persona_id        = Caja::getIdPersona();
            $caja->save();
        });
        $this->actualizardatosahorros();
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


        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }

        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Caja';
        $caja = Caja::find($id);

        $formData       = array('caja.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Cerrar Caja';
        return view($this->folderview.'.cierrecaja')->with(compact( 'formData', 'caja','listar','entidad', 'boton','diferencia'));
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
    //------------------------------
/*********************************** ACTUALIZA AHORROS ************************************** */
public function actualizardatosahorros(){
    $configuracion = configuraciones::all()->last();
    $tasa_interes_ahorro  = $configuracion->tasa_interes_ahorro;
    $lista_ahorros1 = DB::table('ahorros')->where('fechaf','!=', null);
    $lista_ahorros = Ahorros::where("fechaf","=",null)->get();
    $fecha_actual = date('Y-m-d');
    $fecha_hora_actual = date("Y-m-d H:i:s");
    $sfechA = explode('-',$fecha_actual);
    $mesA = $sfechA[1];

    $fecha_ah = date("Y-m-d", strtotime($lista_ahorros[0]->fechai));
    $sfechH = explode('-',$fecha_ah);
    $mesH = $sfechH[1];
    $dif_mes = $mesA - $mesH;
    if($dif_mes >0 && count($lista_ahorros)>0){
        foreach ($lista_ahorros as $key => $value) {
            if($value->id != null){
                $error1 = DB::transaction(function() use($value, $fecha_hora_actual, $tasa_interes_ahorro ){
                    $ahorro_ant = Ahorros::find($value->id);
                    $ahorro_ant->fechaf = $fecha_hora_actual;
                    $ahorro_ant->estado = 'C';
                    $ahorro_ant->save();

                    $ahorro = new Ahorros();
                    $ahorro->fechai = $fecha_hora_actual;
                    $ahorro->capital = $value->capital + $tasa_interes_ahorro * $value->capital;
                    $ahorro->interes = $tasa_interes_ahorro * $value->capital;
                    $ahorro->persona_id = $value->persona_id;
                    $ahorro->estado = 'C';
                    $ahorro->save();
                });
            }
        }
    }
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
        $cboConcepto = array('' => 'Todo') + Concepto::pluck('titulo', 'id')->all();
        $concepto_id             = Libreria::getParam(-1);
        $tituloNuevaTransaccion = $this->tituloNuevaTransaccion;
        $ruta             = $this->rutas;
        $inicio           = 0;
        $entidad ='Transaccion';
        return view($this->folderview.'.transaccion')->with(compact('concepto_id','entidad', 'ruta', 'inicio', 'id','saldo','ingresos','egresos','diferencia','cboConcepto','tituloNuevaTransaccion'));
    }
    ///************** NUevo metodo */

    public function buscartransaccion(Request $request){
    
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad ='Transaccion';
        $idcaja  = Libreria::getParam($request->input('idcaja'));
        $concepto_id      = Libreria::getParam(-1);
        $resultado        = Transaccion::listar( $idcaja);
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
        
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.listTransac')->with(compact('concepto_id','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio', 'idcaja'));
        }
        return view($this->folderview.'.listTransac')->with(compact('concepto_id','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio', 'idcaja'));
    
    }


    //PARA REGISTRAR NUEVO MOVIMIENTO PARA GASTOS, AHORROS DESDE LA CAJA
    public function nuevomovimiento($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $caja = Caja::find($id);

        if (isset($listarParam)) {
            $listar = $listarParam;
        }

        $entidad        = 'Transaccion';
        $cboTipo        = [''=>'Seleccione']+ array('I'=>'Ingreso','E'=>'Egreso');
        $cboConceptos        = [''=>'Seleccione'];
        $boton          = 'Registrar Gasto';
        return view($this->folderview.'.nuevomovimiento')->with(compact('caja', 'entidad', 'id','boton', 'listar','cboTipo','cboConceptos'));
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
            'total'        => 'required|max:100',
            'comentario'        => 'required|max:100'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
       }

        $listar        = Libreria::getParam($request->input('listar'), 'NO');

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
            ->get();
        $retorno .= '><option value="" selected="selected">Seleccione</option>';

        foreach ($cbo as $row) {
            $retorno .= '<option value="' . $row['id'] .  '">' . $row['titulo'] . '</option>';
        }
        $retorno .= '</select></div>';

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
    public function reporteingresosPDF($id, Request $request)
    {    
        $caja = DB::table('caja')->where('id', $id)->first();
        $fechai =  date("d-m-Y",strtotime($caja->fecha_horaApert."- 1 days"));
        $fechaf =  date("d-m-Y",strtotime($caja->fecha_horaCierre."+ 1 days"));

        $day = Date::parse($caja->fecha_horaApert)->format('d');
        $month = Date::parse($caja->fecha_horaApert)->format('m');
        $year = Date::parse($caja->fecha_horaApert)->format('y');
        $anio = "20".$year;

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

        $lista = Caja::listIngresos($fechai,$fechaf)->get();

        //calculo del total de ingresos del mes
        $sum_deposito_ahorros_mes_actual=0;
        $sum_pagos_de_capital_mes_actual=0;
        $sum_interese_recibidos_mes_actual=0;
        $sum_acciones_mes_actual=0;
        $sum_otros_mes_actual=0;
        $sum_ingresos_totales_mes_actual=0;
        if(count($lista) >0 ){
            for($i=0; $i<count($lista); $i++){
                $sum_deposito_ahorros_mes_actual += $lista[$i]->deposito_ahorros;
                $sum_pagos_de_capital_mes_actual += $lista[$i]->pagos_de_capital;
                $sum_interese_recibidos_mes_actual += $lista[$i]->intereces_recibidos;
                $sum_acciones_mes_actual += $lista[$i]->acciones;
                $sum_otros_mes_actual += $lista[$i]->cuota_mora;
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

        //calculo del total de ingresos acumulados al mes anterior
        $caja_asta_mes_anterior = DB::table('caja')->orderBy('id', 'asc')->get();
        $fechai =  date("d-m-Y",strtotime($caja->fecha_horaApert."- 1 days"));
        $fechai_caja_first =  date("d-m-Y",strtotime($caja_asta_mes_anterior[0]->fecha_horaApert."- 1 days"));
        $lista_mes_anterior = Caja::listIngresos($fechai_caja_first,$fechai)->get();

        $sum_deposito_ahorros_asta_mes_anterior=0;
        $sum_pagos_de_capital_asta_mes_anterior=0;
        $sum_interese_recibidos_asta_mes_anterior=0;
        $sum_acciones_asta_mes_anterior=0;
        $sum_otros_asta_mes_anterior=0;
        $sum_ingresos_totales_asta_mes_anterior=0;
        if(count($lista_mes_anterior) >0 ){
            for($i=0; $i<count($lista_mes_anterior); $i++){
                $sum_deposito_ahorros_asta_mes_anterior += $lista_mes_anterior[$i]->deposito_ahorros;
                $sum_pagos_de_capital_asta_mes_anterior += $lista_mes_anterior[$i]->pagos_de_capital;
                $sum_interese_recibidos_asta_mes_anterior += $lista_mes_anterior[$i]->intereces_recibidos;
                $sum_acciones_asta_mes_anterior += $lista_mes_anterior[$i]->acciones;
                $sum_otros_asta_mes_anterior += $lista_mes_anterior[$i]->cuota_mora;
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


        $persona = DB::table('persona')->where('id', $caja->persona_id)->first();

        $titulo = "reporte ".$caja->titulo."_Ingresos";
        $view = \View::make('app.reportes.reporteIngresoPDF')->with(compact('lista', 'id', 'caja', 'persona','day','mes','anio','mesItm','sum_deposito_ahorros_mes_actual',
                                                                            'sum_pagos_de_capital_mes_actual','sum_interese_recibidos_mes_actual','sum_acciones_mes_actual','sum_otros_mes_actual','sum_ingresos_totales_mes_actual',
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
    public function reporteegresosPDF($id, Request $request)
    {    
        $caja = DB::table('caja')->where('id', $id)->first();
        $fechai =  date("d-m-Y",strtotime($caja->fecha_horaApert."- 1 days"));
        $fechaf =  date("d-m-Y",strtotime($caja->fecha_horaCierre."+ 1 days"));

        $day = Date::parse($caja->fecha_horaApert)->format('d');
        $month = Date::parse($caja->fecha_horaApert)->format('m');
        $year = Date::parse($caja->fecha_horaApert)->format('y');
        $anio = "20".$year;

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

        $lista = Caja::listEgresos($fechai,$fechaf)->get();

        //calculo del total de egresos del mes actual
        $sum_retiro_ahorros_mes_actual=0;
        $sum_prestamo_de_capital_mes_actual=0;
        $sum_interes_pagado_mes_actual=0;
        $sum_egresos_totales_mes_actual=0;
        if(count($lista) >0 ){
            for($i=0; $i<count($lista); $i++){
                $sum_retiro_ahorros_mes_actual += $lista[$i]->monto_ahorro;
                $sum_prestamo_de_capital_mes_actual += $lista[$i]->monto_credito;
                $sum_interes_pagado_mes_actual += $lista[$i]->interes_ahorro;
            }
            $sum_egresos_totales_mes_actual=($sum_retiro_ahorros_mes_actual+$sum_prestamo_de_capital_mes_actual+$sum_interes_pagado_mes_actual);
        }else{
            $sum_retiro_ahorros_mes_actual=0;
            $sum_prestamo_de_capital_mes_actual=0;
            $sum_interes_pagado_mes_actual=0;
            $sum_egresos_totales_mes_actual=0;
        }

        //calculo del total de egresos acumulados al mes anterior
        $caja_asta_mes_anterior = DB::table('caja')->orderBy('id', 'asc')->get();
        $fechai =  date("d-m-Y",strtotime($caja->fecha_horaApert."- 1 days"));
        $fechai_caja_first =  date("d-m-Y",strtotime($caja_asta_mes_anterior[0]->fecha_horaApert."- 1 days"));
        $lista_mes_anterior = Caja::listIngresos($fechai_caja_first,$fechai)->get();

        $sum_retiro_ahorros_mes_anterior=0;
        $sum_prestamo_de_capital_mes_anterior=0;
        $sum_interes_pagado_mes_anterior=0;
        $sum_egresos_totales_mes_anterior=0;
        if(count($lista) >0 ){
            for($i=0; $i<count($lista); $i++){
                $sum_retiro_ahorros_mes_anterior += $lista[$i]->monto_ahorro;
                $sum_prestamo_de_capital_mes_anterior += $lista[$i]->monto_credito;
                $sum_interes_pagado_mes_anterior += $lista[$i]->interes_ahorro;
            }
            $sum_egresos_totales_mes_anterior=($sum_retiro_ahorros_mes_anterior+$sum_prestamo_de_capital_mes_anterior+$sum_interes_pagado_mes_anterior);
        }else{
            $sum_retiro_ahorros_mes_anterior=0;
            $sum_prestamo_de_capital_mes_anterior=0;
            $sum_interes_pagado_mes_anterior=0;
            $sum_egresos_totales_mes_anterior=0;
        }

        //calculo de ingresos acumulados asta la fecha
        $sum_retiro_ahorros_acumulados=0;
        $sum_prestamo_de_capital_acumulados=0;
        $sum_interes_pagado_acumulados=0;
        $sum_egresos_totales_acumulados=0;

        //-------suma
        $sum_retiro_ahorros_acumulados=($sum_retiro_ahorros_mes_actual+$sum_retiro_ahorros_mes_anterior);
        $sum_prestamo_de_capital_acumulados=($sum_prestamo_de_capital_mes_actual+$sum_prestamo_de_capital_mes_anterior);
        $sum_interes_pagado_acumulados=($sum_interes_pagado_mes_actual+$sum_interes_pagado_mes_anterior);
        $sum_egresos_totales_acumulados=($sum_egresos_totales_mes_actual+$sum_egresos_totales_mes_anterior);


        $persona = DB::table('persona')->where('id', $caja->persona_id)->first();

        $titulo = "reporte ".$caja->titulo."_Ingresos";
        $view = \View::make('app.reportes.reporteEgresoPDF')->with(compact('lista', 'id', 'caja', 'persona','day','mes','anio','mesItm','sum_retiro_ahorros_mes_actual',
                                                                            'sum_prestamo_de_capital_mes_actual','sum_interes_pagado_mes_actual','sum_egresos_totales_mes_actual',
                                                                        'sum_retiro_ahorros_mes_anterior','sum_prestamo_de_capital_mes_anterior','sum_interes_pagado_mes_anterior','sum_egresos_totales_mes_anterior',
                                                                    'sum_retiro_ahorros_acumulados','sum_prestamo_de_capital_acumulados','sum_interes_pagado_acumulados','sum_egresos_totales_acumulados'));
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

}
