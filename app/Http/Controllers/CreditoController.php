<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Credito;
use App\Persona;
use App\Cuota;
use App\Caja;
use App\Pagos;
use App\Ahorros;
use App\Transaccion;
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use PDF;
use DateTime;
class CreditoController extends Controller{
// 0.20
    protected $folderview = 'app.credito';
    protected $tituloAdmin = 'Credito';
    protected $tituloRegistrar = 'Registro de Crédito';
    protected $tituloModificar = 'Modificar Crédito';
    protected $tituloEliminar = 'Eliminar Crédito';
    protected $tituloDetallecredito = 'Detalle de Crédito';
    protected $titulopagocuota = 'Pago de Cuota';
    protected $rutas = array(
        'create' => 'creditos.create',
        'delete' => 'creditos.eliminar',
        'search' => 'creditos.buscar',
        'index' => 'creditos.index',
        'detallecredito' => 'creditos.detallecredito',
        'vistaaccion' => 'creditos.vistaaccion',
        'listardetallecuotas' => 'creditos.listardetallecuotas',
        'vistapagocuota' => 'creditos.vistapagocuota',
        'pagarcuota' => 'creditos.pagarcuota',
        'generareportecuotasPDF' => 'creditos.generareportecuotasPDF',
        'generarecibopagocuotaPDF' => 'creditos.generarecibopagocuotaPDF',
        'generarecibopagocuotaPDF2' => 'creditos.generarecibopagocuotaPDF2',
        'generarecibocreditoPDF' => 'creditos.generarecibocreditoPDF',
        'abrirpdf' => 'creditos.abrirpdf',
        'listpersonas' => 'creditos.listpersonas',
        'cuotasalafecha' => 'creditos.cuotasalafecha',
        'pagarcuotainteres'=>'creditos.pagarcuotainteres',
        // 'amortizarcuotas' => 'creditos.amortizarcuotas',
        'obtenermontototal' => 'creditos.obtenermontototal',
        'pagarcreditototal' => 'creditos.pagarcreditototal',
        // 'ampliar_reducir_cuotas' => 'creditos.ampliar_reducir_cuotas',
        // 'datos_ampliar_reducir_cuotas' => 'creditos.datos_ampliar_reducir_cuotas',
        'vista_refinanciar' => 'creditos.vista_refinanciar',
        'guardar_refinanciacion' => 'creditos.guardar_refinanciacion',
        'reportecreditos' => 'creditos.reportecreditos',
        'vistareporte' => 'creditos.vistareporte'
    );

    public function __construct(){
        $this->middleware('auth');
    }

/*************--INICIO--************************* */
    public function index(){
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();
        $entidad = 'Credito';
        $ruta = $this->rutas;
        $title = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboEstado = array(0=>'Pendientes', 1 => 'Cancelados');
        $fecha_pordefecto =count($caja) == 0?  (date('Y')-3)."-01-01": (date('Y',strtotime($caja[0]->fecha_horaapert))-3)."-01-01";
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboEstado','caja_id','configuraciones','fecha_pordefecto' ));
    }

/*************--LISTAR CREDITOS--**************** */
    public function buscar(Request $request){
        $entidad = 'Credito';
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $fechabusqueda = Libreria::getParam($request->input('fechabusqueda'));
        $estadobusqueda = Libreria::getParam($request->input('estadobusqueda'));
        $nombreclientebusqueda = $request->input('txtbusquedanombre');

        $resultado = (new Credito())->listar($nombreclientebusqueda,$fechabusqueda,$estadobusqueda);
        $lista = $resultado->get();
        $cabecera = array();
        $cabecera[]  = array('valor' => '#', 'numero' => '1');
        $cabecera[]  = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]  = array('valor' => 'NOMBRE', 'numero' => '1');
        $cabecera[]  = array('valor' => 'MONTO CRÉDITO S/.', 'numero' => '1');
        $cabecera[]  = array('valor' => 'PERIODO', 'numero' => '1');
        $cabecera[]  = array('valor' => 'ESTADO', 'numero' => '1');
        if($estadobusqueda == 0){
            $cabecera[]  = array('valor' => 'Operaciones', 'numero' => '4');

        }else{
            $cabecera[]  = array('valor' => 'Operaciones', 'numero' => '1');

        }
        
        $ruta = $this->rutas;
        $titulo_detalle = $this->tituloDetallecredito;
        $titulo_eliminar = $this->tituloEliminar;
        if (count($lista) > 0) {
            $clsLibreria = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion = $paramPaginacion['cadenapaginacion'];
            $inicio = $paramPaginacion['inicio'];
            $fin = $paramPaginacion['fin'];
            $paginaactual = $paramPaginacion['nuevapagina'];
            $lista = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera','titulo_detalle','titulo_eliminar', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad','titulo_detalle'));
    }

/*************--MODAL NUEVO CREDITO--************ */
    public function create(Request $request){
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = Configuraciones::all()->last();
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'Credito';
        $credito = null;
        $ruta = $this->rutas;
        $formData = array('creditos.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Registrar'; 

        //calculos
        $monto_inicio = round($caja[0]->monto_iniciado, 1);
        $egresos=0;
        $ingresos=0;
        $saldo_en_caja =0;
        $saldo = Transaccion::getsaldo($caja_id)->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += round($saldo[$i]->monto, 1); 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += round($saldo[$i]->monto, 1);
            }
        }
        $saldo_en_caja= $ingresos+ $monto_inicio - $egresos;

        $cboPers = array(0=>'Seleccione...');
        $fecha_pordefecto =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaapert));
        return view($this->folderview.'.mant')->with(compact('credito', 'formData', 'entidad', 'boton', 'listar', 'configuraciones','caja_id','ruta','saldo_en_caja', 'cboPers','fecha_pordefecto'));
    }
    
/*************--GUARDAR NUEVO CREDITO--************ */
    public function store(Request $request){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $res = null;
        $credito_id = null;
        if($caja_id != 0){
            $numCreditos = Credito::where('estado','=','0')->where('persona_id','=', $request->get('selectnom'))->get();
            $valid = true;
            if(count($numCreditos) >= 2){
                $valid=false;
                $res = "El socio o cliente ya cuenta con 2 créditos por lo cual ya no puede obtener otro.";
            }else if(count($numCreditos) == 1){
                if($request->input('periodo') == '1'){
                    $valid=true;
                }else{
                    $valid=false;
                    $res = "El Socio o Cliente ya cuenta con un crédito, solo se le permite un crédito mas a una sola cuota.";
                }
            }
                $reglas =array(
                    'valor_credito' => 'required|max:20',
                    'periodo' => 'required|max:50|integer',
                    'tasa_interes' => 'required|max:20'
                );
                $validacion = Validator::make($request->all(),$reglas);
                if ($validacion->fails()) {
                    return $validacion->messages()->toJson();
                }
                $configuraciones = configuraciones::all()->last();
                $credito = new Credito();
                $error = DB::transaction(function() use($request, $caja_id, $credito_id, $credito){
                    $configuraciones = Configuraciones::all()->last();
                    $periodo = $request->input('periodo');
                    $fechainicio = $request->input('fechacredito')." ".date(" H:i:s");//**** */
                    $fechafinal = strtotime ( '+'.$periodo.' month' , strtotime ( $fechainicio));
                    $fechafinal = date( 'Y-m-d' , $fechafinal)." ".date(" H:i:s");
                    $valorcredito = $request->get('valor_credito');
                    $descripcion = $request->get('descripcion');
                    $persona_id = $request->input('selectnom');
                    $pers_aval_id = $request->input('selectaval');
                    $tasa_interes = $request->input('tasa_interes');
                    $tasa_multa = $configuraciones->tasa_interes_multa;
    
                    $credito->valor_credito = $valorcredito;
                    $credito->periodo = $periodo;
                    $credito->tasa_interes = $tasa_interes;
                    $credito->tasa_multa = $tasa_multa;
                    $credito->fechai =$fechainicio;
                    $credito->fechaf = $fechafinal;
                    $credito->estado = '0';//estado : 0 = pendiente de pago
                    $credito->descripcion = $descripcion;
                    $credito->persona_id = $persona_id;
                   
                    if($pers_aval_id != '0'){
                        $credito->pers_aval_id = $pers_aval_id;
                    }

                    $credito->save();
                        
                    $montorestante =  $valorcredito;
                    $montorestante2 =  $valorcredito;
                    $valor_cuota =  (($tasa_interes/100) * $valorcredito) / (1 - (pow(1/(1+($tasa_interes/100)), $periodo)));
                    $fecha_actual = $fechainicio;
                    $explod = explode('-',date("Y-m-d",strtotime($fecha_actual)));
                    $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                    $interesAcumulado = 0.00;
                    for($i=0;$i<(int)$periodo; $i++){
                        $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month")); 
                        $montInteres = ($tasa_interes/100) * $montorestante; 
                        $interesAcumulado +=  $montInteres; 
                       
                        $montCapital = round($valor_cuota, 1) - round($montInteres, 1); 
                        if($i < (int)($periodo - 1)){
                            $montorestante = $montorestante - $montCapital;
                        }
                        
                        $fecha_p = new DateTime($fechacuota);
                        $fecha_p->modify('last day of this month');
                        $fecha_p->format('Y-m-d');
                        
                        if($i == (int)($periodo - 1)){
                            $montCapital = round($montorestante, 1);
                            $montInteres =  round($valor_cuota, 1) - $montCapital;
                            $montorestante = 0;
                        }
                        $cuota = new Cuota();
                        $cuota->parte_capital = round($montCapital, 1); 
                        $cuota->interes = round($montInteres, 1);
                        $cuota->interes_mora = 0.00;
                        $cuota->saldo_restante = round($montorestante, 1);
                        $cuota->numero_cuota = $i + 1;
                        $cuota->fecha_programada_pago = $fecha_p;
                        $cuota->estado = '0';//0=PENDIENTE; 1 = PAGADO; m = MOROSO
                        $cuota->credito_id = $credito->id;
                        $cuota->save();
                    }
                  
                    //registro credito en transaccion
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $fechainicio;
                    $transaccion->monto = $credito->valor_credito;
                    $transaccion->concepto_id = 3;
                    $transaccion->descripcion = $descripcion;
                    $transaccion->persona_id = $persona_id;
                    $transaccion->usuario_id = Credito::idUser();
                    $transaccion->caja_id = $caja_id;
                    $transaccion->monto_credito = $valorcredito;
                    $transaccion->inicial_tabla = 'CR';
                    $transaccion->id_tabla = $credito->id;
                    $transaccion->save();
                    
                });
                $credito_id = $credito->id;
                //$ultimo_credito = Credito::all()->last();
                $res = $error;
        }else{
            $res = 'Caja no aperturada, asegurece de aperturar primero para registrar alguna transacción.!';
        }
        //$ultimo_credito = Credito::all()->last();
        $res = is_null($res) ? "OK" : $res;
        $respuesta = array($res, $credito_id);
        return $respuesta;
    }

/*************-- MODAL ELIMINAR CREDITO--*************** */
    public function eliminar($id, $listarLuego){
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $num_cuotas_pendientes = count(Cuota::where('credito_id','=', $id)->where('fecha_pago','=',null)->get());
        $credito = Credito::find($id);
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $list_cuotas = Cuota::where('credito_id','=', $id)->where('fecha_pago','!=', null)->get();
        $transaccion_credito = Transaccion::where("id_tabla",'=',$id)->where('inicial_tabla','=','CR')->get();
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $modelo = Credito::find($id);
        $entidad = 'Credito';
        $formData = array('route' => array('creditos.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
       $boton = "Eliminar";
        if($caja_id == (count($transaccion_credito)>0?$transaccion_credito[0]->caja_id:0) & count($list_cuotas)<= 0){
            return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
        }else if($num_cuotas_pendientes != $credito->periodo){
            $mensaje = "¡Error! El credito no se puede eliminar, contiene cuotas pagadas o vigentes de pago a la fecha!";
            return view('app.ahorros.mensajealerta')->with(compact('modelo', 'formData', 'entidad', 'listar','mensaje'));
        }else{
            $mensaje = "¡Error! El registro no se puede eliminar, esta registrado en una caja actualmente cerrada.!";
            return view('app.ahorros.mensajealerta')->with(compact('modelo', 'formData', 'entidad', 'listar','mensaje'));
        }
        
    }

/*************--BORRAR CREDITO--***************** */
    public function destroy($id){
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $list_cuotas = Cuota::where('credito_id','=', $id)->where('fecha_pago','!=', null)->get();
        $cuotas = Cuota::where('credito_id','=',$id)->get();
        $error = null;
        if(count($list_cuotas)<=0){
            $error = DB::transaction(function() use($id, $cuotas){
                foreach($cuotas as $cuota){
                    $cuota->delete();
                }
                $transaccion_credito = Transaccion::where("id_tabla",'=',$id)->where('inicial_tabla','=','CR')->get()[0];

                $transaccion_credito->delete();
                $credito = Credito::find($id);
                $credito->delete();
            });
        }else{
            $error = "El registro no se puede eliminar, contiene cuotas pagadas.";
        }
        
        return is_null($error) ? "OK" : $error;
    }

/*************--MODIFICAR CREDITO--************** */
    public function update(Request $request, $id){
        $reglas =array(
            'valor_credito' => 'required|max:20',
            'periodo' => 'required|max:5',
            'tasa_interes' => 'required|max:20|integer'
        );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($request, $id){
            $configuraciones = configuraciones::all()->last();
            $periodo = $request->input('periodo');
            $fechainicio = $request->input('fechacredito').date(" H:i:s");//**** */
            $fechafinal = strtotime ( '+'.$periodo.' month' , strtotime ( $credito->fechai ) ) ;
            $fechafinal = date( 'Y-m-d' , $fechafinal);
            $valorcredito = $request->get('valor_credito');
            $descripcion = $request->get('descripcion');
            $persona_id = $request->get('persona_id');
            $pers_aval_id= $request->get('pers_aval_id');
            $tasa_interes = $request->input('tasa_interes');
            $tasa_multa = $configuraciones->tasa_interes_multa;
            
            $credito = Credito::find($id);
            $credito = new Credito();
            $credito->valor_credito = $valorcredito;
            $credito->periodo = $periodo;
            $credito->tasa_interes = $tasa_interes;
            $credito->tasa_multa = $tasa_multa;
            $credito->fechai =$fechainicio;
            $credito->fechaf = $fechafinal;
            $credito->estado = '0';
            $credito->descripcion = $descripcion;
            $credito->persona_id = $persona_id;
            $credito->pers_aval_id = $pers_aval_id;
            $credito->save();
        });
        return is_null($error) ? "OK" : $error;
    }

/*************--MODAL PAGO CUOTA--*************** */
    public function vistapagocuota(Request $request, $cuota_id, $listarluego, $entidadr="nan"){
        $numero= $entidadr;
        $entidad_recibo = $entidadr;
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        $existe = Libreria::verificarExistencia($cuota_id, 'cuota');
        if ($existe !== true) {
            return $existe;
        }
        $listar = Libreria::getParam($request->input('listar'), 'SI');

        $entidad_credito = 'Credito';
        $entidad_cuota = 'Cuota';
        $credito = null;
        $interes_moratorio = $request->get('valor_moratorio');
        //$fechapago = $request->get('fechaselect');
        $fechapago =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaapert));
        $boton = 'Registrar'; 
        $ruta = $this->rutas;
        $cuota = Cuota::find($cuota_id);
        $cuota->interes_mora = round($interes_moratorio, 1);
        
        // if($cuota->fecha_iniciomora != null){
        //     $numero_meses = $this->numero_meses($cuota->fecha_iniciomora,$fechapago);
        //     $cuota->interes += $cuota->interes * $numero_meses;
        // }
        $configuraciones = configuraciones::all()->last();
        $credito2 = Credito::find($cuota->credito_id);
        $persona = Persona::find($credito2->persona_id);
        if($numero == 2){
            $formData = array('creditos.pagarcuotainteres');
            $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad_cuota, 'autocomplete' => 'off');
            return view($this->folderview.'.pagarcuota')->with(compact('cuota','persona', 'entidad_cuota', 'entidad_credito','entidad_recibo', 'credito','credito2', 'formData','listar','ruta','fechapago','configuraciones'));
        }else{
            $formData = array('creditos.pagarcuota');
            $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad_cuota, 'autocomplete' => 'off');
            return view($this->folderview.'.pagarcuota')->with(compact('cuota','persona', 'entidad_cuota', 'entidad_credito','entidad_recibo', 'credito','credito2', 'formData','listar','ruta','fechapago','configuraciones'));
        }
    }

/*************--REGISTRO PAGO CUOTA--************ */
    public function pagarcuota(Request $request){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $res = null;
        if($caja_id != 0){
            $error = DB::transaction(function() use($request, $caja_id){
                $id_cuota = $request->get('id_cuota');
                $id_credito = $request->get('id_credito');
                $fecha_pago = $request->get('fecha_pagoc')." ".date(" H:i:s");
                $id_cliente = $request->get('id_cliente');
                $valor_mora = $request->get('valor_mora');
              
                $partecapital = $request->get('partecapital');
                $cuotainteres = $request->get('cuotainteres');
                $cuotamora = $request->get('cuotamora');
                $configuraciones = configuraciones::all()->last();
                $comision_voucher = $configuraciones->valor_recibo;//0.2;
                //Actualiza cuota a estado cancelado
                $cuota = Cuota::find($id_cuota);
                $cuota->estado = 1;
               // $cuota->interes_mora = round(($valor_mora == null?0:$valor_mora), 7);
                $cuota->interes_mora = $cuotamora;
                $cuota->parte_capital = $partecapital;
                $cuota->interes = $cuotainteres;
                $cuota->fecha_pago = $fecha_pago;
                $cuota->save();

                //registra la comision por voucher en caja si desea imprimirlo
                $concepto_id = 8;
                $transaccion2 = new Transaccion();
                $transaccion2->fecha = $fecha_pago;
                $transaccion2->monto = $comision_voucher;
                $transaccion2->concepto_id = $concepto_id;
                $transaccion2->descripcion ='Comision por Recibo Pago Cuota';
                $transaccion2->persona_id = $id_cliente;
                $transaccion2->usuario_id = (new Credito())->idUser();
                $transaccion2->caja_id = $caja_id;
                $transaccion2->comision_voucher = $comision_voucher;
                $transaccion2->save();
            
                //registramos en caja el pago cuota
                $monto = round($cuota->parte_capital, 1);
                $parte_capital =  $cuota->parte_capital;
                $cuota_interes = 0;
                $cuota_interesMora = 0;
                //if(date('Y-m',strtotime($fecha_pago)) >= date('Y-m', strtotime($cuota->fecha_programada_pago))){
                    $monto += round($cuota->interes+ $cuota->interes_mora, 1);
                    $parte_capital = $cuota->parte_capital;
                    $cuota_interes = $cuota->interes;
                    $cuota_interesMora = $cuota->interes_mora;
                //}
     /*********************Mora de cuota */
                    $ahorro_id=null;
                    if($cuotamora > 0){
                        $resultado = (new Ahorros())->getahorropersona(6);

                        
                        if(count($resultado) >0){
                            $ahorro_actual = $resultado[0];
                            $ahorro_actual->capital = $ahorro_actual->capital + $cuotamora;
                            $ahorro_actual->estado = 'P';
                            $ahorro_actual->save();
                            $ahorro_id = $ahorro_actual->id;
                        }else{
                            $ahorro = new Ahorros();
                            $ahorro->capital = $cuotamora;
                            $ahorro->interes = 0;
                            $ahorro->estado = 'P';
                            $ahorro->fechai = $fecha_pago;
                            $ahorro->persona_id = 6;
                            $ahorro->save();
                            $ahorro_id = $ahorro->id;
                        }
                    }
                 $persona = Persona::find($id_cliente);
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $fecha_pago;
                    $transaccion->monto =  $cuotamora;
                    $transaccion->monto_ahorro=  $cuotamora;
                    $transaccion->id_tabla = $ahorro_id;
                    $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                    $transaccion->concepto_id = 5;// concepto deposito de ahorros
                    $transaccion->persona_id = 6;
                    $transaccion->descripcion =  "Se ahorró S/. ".$cuotamora." por morosidad de cuota de ".$persona->apellidos." ".$persona->nombres;
                    $transaccion->usuario_id = (new Credito())->idUser();
                    $transaccion->caja_id =  $caja_id;
                    $transaccion->save();
           
    /******************** ***********/
                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $fecha_pago;
                $transaccion->monto = round($cuotainteres + $partecapital,1);
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = "Pago de Cuota N°:".$cuota->numero_cuota;
                $transaccion->persona_id = $id_cliente;
                $transaccion->usuario_id = (new Credito())->idUser();
                $transaccion->caja_id = $caja_id;
                $transaccion->cuota_parte_capital = round($partecapital, 1);
                $transaccion->cuota_interes = round($cuotainteres, 1);
                $transaccion->cuota_mora = round($cuotamora,1);
                $transaccion->id_tabla = $id_credito;
                $transaccion->inicial_tabla = 'CU';
                $transaccion->save();

                //Modificamos el estado de credito si ya se cancelo todas las cuotas
                $credito = Credito::find($id_credito);
                $num_cuotas_vigentes = count(Cuota::where('credito_id','=', $credito->id)->where('fecha_pago','=', null)->where('estado','!=','1')->where('deleted_at','=',null)->get());
                if($num_cuotas_vigentes <=0){
                    $credito->estado = 1;
                    $credito->save();
                }

                $monto_pago = $request->get('monto_pago_p');
                $monto_recibido = $request->get('monto_recibido_p');
                $monto_recibido = $request->get('monto_recibido_p');

                $pago = new Pagos();
                $pago->monto_pago = round($monto_pago, 1);
                $pago->monto_recibido = round($monto_recibido,1);
                $pago->parte_entregado = 0;
                $pago->estado = 'F';
                $pago->ini_tabla = 'CU';
                $pago->fecha = $fecha_pago;
                $pago->persona_id = $credito->persona_id;
                $pago->save();

            });
            $res = $error;
        }else{
            $res = 'Caja no aperturada, asegurece de aperturar primero para registrar alguna transacción.!';
        }
        return is_null($res) ? "OK" : $res;
    }

/*************--REGISTRO PAGO INTERES CUOTA--************ */
    public function pagarcuotainteres(Request $request){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $res = null;
        if($caja_id != 0){
            $error = DB::transaction(function() use($request, $caja_id){
                $id_cuota = $request->get('id_cuota');
                $id_credito = $request->get('id_credito');
                $fecha_pago = $request->get('fecha_pagoc').date(" H:i:s");
                $id_cliente = $request->get('id_cliente');
                $configuraciones = configuraciones::all()->last();
                $comision_voucher = $configuraciones->valor_recibo;// 0.2;

                //Actualiza cuota a estado cancelado
                $cuota = Cuota::find($id_cuota);
                $cuota->estado = 'I';// pago de interes de cuota
                $cuota->fecha_pago = $fecha_pago;
                $cuota->save();

                //registra la comision por voucher en caja si desea imprimirlo
                $concepto_id = 8;
                $transaccion2 = new Transaccion();
                $transaccion2->fecha = $fecha_pago;
                $transaccion2->monto = $comision_voucher;
                $transaccion2->concepto_id = $concepto_id;
                $transaccion2->descripcion ='Comision por Recibo Pago Cuota';
                $transaccion2->persona_id = $id_cliente;
                $transaccion2->usuario_id = (new Credito())->idUser();
                $transaccion2->caja_id = $caja_id;
                $transaccion2->comision_voucher = $comision_voucher;
                $transaccion2->save();
                //registramos en caja el pago cuota
                $monto = $cuota->parte_capital;
                $parte_capital =  $cuota->parte_capital;
               
                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $fecha_pago;
                $transaccion->monto = round($cuota->interes, 1);
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = "Pago de interes Cuota N°:".$cuota->numero_cuota;
                $transaccion->persona_id = $id_cliente;
                $transaccion->usuario_id = (new Credito())->idUser();
                $transaccion->caja_id = $caja_id;
                $transaccion->cuota_parte_capital = 0;
                $transaccion->cuota_interes = round($cuota->interes, 1);
                $transaccion->cuota_mora = 0;
                $transaccion->save();
            });
            $res = $error;
        }else{
            $res = 'Caja no aperturada, asegurece de aperturar primero para registrar alguna transacción.!';
        }
        return is_null($res) ? "OK" : $res;
    }

/*************--MODAL DETALLE CREDITO--********** */
    public function detallecredito(Request $request, $credito_id){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;

        $configuraciones = configuraciones::all()->last();
        $credito = Credito::find($credito_id);
        $persona = Persona::find($credito->persona_id);
        $entidad_cuota = 'Cuota';
        $entidad_credito = 'Credito';
       
        $fechacaducidad = Date::parse($credito->fechai)->format('Y/m/d');
        $fechacaducidad = date("Y-m-d",strtotime($fechacaducidad."+ ".$credito->periodo." month"));
        $ruta = $this->rutas;
        return view($this->folderview.'.detallecredito')->with(compact('credito','credito_id', 'entidad_cuota','entidad_credito','fechacaducidad','caja_id','configuraciones', 'ruta', 'persona'));
    }

/*************--MODAL Realizar accion--********** */
    public function vistaaccion(Request $request, $credito_id){
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();
        $credito = Credito::find($credito_id);
        $persona = Persona::find($credito->persona_id);
        $entidad_cuota = 'Cuota';
        $entidad_credito = 'Credito';
        $cboacciones = array(//'1'=>'Pago de cuotas pendientes',//pago de cuota pendiente a la fecha
            '2'=>'Pago de interes (Cuota/Pendiente)',//pagar solo el interes de la cuota pendiente
            //'3'=>'Amortizar cuotas',//cancelacion de cuotas para reducir interes y acortar el plazo
            '4'=>'Cancelar todo',
            //'5'=>'Ampliar o disminnuir cuotas'
        );
    
           
            $anioInicio = 2007;
            $anioactual = explode('-',date('Y-m-d'))[0];
            $mesactual = explode('-',date('Y-m-d'))[1];
            
            $fecha_actual = date('Y-m-d');
        $fechacaducidad = Date::parse($credito->fechai)->format('Y/m/d');
        $fechacaducidad = date("Y-m-d",strtotime($fechacaducidad."+ ".$credito->periodo." month"));
        $ruta = $this->rutas;
        $fecha_pordefecto =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaapert));
        return view($this->folderview.'.vistaoperacion')->with(compact('credito','anios','meses','anioactual','mesactual','credito_id','cboacciones', 'entidad_cuota','entidad_credito','fechacaducidad','caja_id','configuraciones', 'ruta', 'persona','fecha_actual','fecha_pordefecto'));
    }

/*************--LISTAR DETALLE CUOTAS--********** */
    public function listardetallecuotas(Request $request){
        $credito_id =  $request->input('credito_id');
        $opcion = $request->input('select_opcion');
        $credito = Credito::find($credito_id);
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $entidad_cuota = 'Cuota';
        $entidad_credito = 'Credito';
        $cabecera = array();
        /*********************** */
        $caja = Caja::where('estado','=','A')->where('deleted_at','=',null)->get();
        $anio = count($caja)>0?date('Y', strtotime($caja[0]->fecha_horaapert)): date('Y');
        $mes = count($caja)>0?date('m', strtotime($caja[0]->fecha_horaapert)): date('m');
        $cuotasPendientes = Cuota::where('estado','!=','1')->where('credito_id','=',$credito_id)->where('deleted_at','=',null)->orderby('numero_cuota','ASC')->get();

        // $numero_cuotas_pendientes = count($cuotasPendientes);
        // $cuota_siguiente = null;
        // if($numero_cuotas_pendientes > 0){
        //     $cuota_siguiente = Cuota::where('credito_id','=', $credito_id)->where(DB::raw('extract( month from fecha_programada_pago)'),'=',$mes)->where(DB::raw('extract( year from fecha_programada_pago)'),'=',$anio)->where('deleted_at','=',null)->get()[0];
        // }
        // $saldo_restante= count($cuotasPendientes) >0?round($cuotasPendientes[0]->parte_capital + $cuotasPendientes[0]->saldo_restante, 1): 0;
        $saldo_pagado= Cuota::saldo_pagado($credito_id);
        $saldo_restante = $credito->valor_credito - (count($saldo_pagado)>0?$saldo_pagado[0]->saldo_pagado:0);
        // $saldo_restante = $cuota_siguiente != null? round($cuota_siguiente->parte_capital + $cuota_siguiente->saldo_restante,1): 0;
        
        /*************************** */
        if($opcion == "vigentes"){
           
            $resultado = Cuota::listar($credito_id);
            $cabecera[] = array('valor' => 'Fech. Pag', 'numero' => '1');
            $cabecera[] = array('valor' => 'Num. Cuota', 'numero' => '1');
            $cabecera[] = array('valor' => 'Monto Cuota S/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Capital S/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Interes', 'numero' => '1');
            $cabecera[] = array('valor' => 'Fech. Real Pag', 'numero' => '1');
            $cabecera[] = array('valor' => 'Interes Mora', 'numero' => '1');
            $cabecera[] = array('valor' => 'Mont. Real Cuota s/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Saldo s/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Estado', 'numero' => '1');
            // $cabecera[] = array('valor' => 'Operaciones', 'numero' => '1');
    
        }else if($opcion == 'cancelados'){
           
            $resultado = Transaccion::where('id_tabla','=',$credito_id)->where('inicial_tabla','=', 'CU')->where('deleted_at','=',null); //CU = inical de tabla Cuota
            $cabecera[] = array('valor' => 'N°', 'numero' => '1');
            $cabecera[] = array('valor' => 'Num. Cuota', 'numero' => '1');
            $cabecera[] = array('valor' => 'Monto Cuota S/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Capital S/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Interes', 'numero' => '1');
            $cabecera[] = array('valor' => 'Fecha de Pago', 'numero' => '1');
            $cabecera[] = array('valor' => 'Interes Mora', 'numero' => '1');
            $cabecera[] = array('valor' => 'Mont. Real Cuota s/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Saldo s/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Descripción', 'numero' => '1');
            $cabecera[] = array('valor' => 'Operaciones', 'numero' => '1');
    
        }else{
            $resultado = Cuota::listartodo($credito_id);
            $cabecera[] = array('valor' => 'N°', 'numero' => '1');
            $cabecera[] = array('valor' => 'Num. Cuota', 'numero' => '1');
            $cabecera[] = array('valor' => 'Monto Cuota S/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Capital S/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Interes', 'numero' => '1');
            $cabecera[] = array('valor' => 'Fecha de Pago', 'numero' => '1');
            $cabecera[] = array('valor' => 'Interes Mora', 'numero' => '1');
            $cabecera[] = array('valor' => 'Mont. Real Cuota s/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Saldo s/.', 'numero' => '1');
            $cabecera[] = array('valor' => 'Estado', 'numero' => '1');
            $cabecera[] = array('valor' => 'Descripción', 'numero' => '1');
        }
        $lista = $resultado->get();
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        $fecha_actual = count($caja)>0?$caja[0]->fecha_horaapert: date('Y-m-d');
        $configuraciones = configuraciones::all()->last();
       
        $fechacaducidad = Date::parse($credito->fechai)->format('Y/m/d');
        $fechacaducidad = date("Y-m-d",strtotime($fechacaducidad."+ ".$credito->periodo." month"));
        $ruta = $this->rutas;
        $titulo_pagocuota = $this->titulopagocuota;
        if (count($lista) > 0) {
            $clsLibreria = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad_cuota);
            $paginacion = $paramPaginacion['cadenapaginacion'];
            $inicio = $paramPaginacion['inicio'];
            $fin = $paramPaginacion['fin'];
            $paginaactual = $paramPaginacion['nuevapagina'];
            $lista = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.listdetallecredito')->with(compact('lista','credito', 'paginacion', 'inicio', 'fin', 'entidad_credito','entidad_cuota', 'cabecera','titulo_pagocuota','credito_id','ruta','caja_id', 'opcion','fecha_actual', 'saldo_restante'));
        }
        return view($this->folderview.'.listdetallecredito')->with(compact('lista','entidad_credito','entidad_cuota','fecha_actual'));
    }

/*************--REPORTE DE CUOTAS PDF--********** */
    public function generareportecuotasPDF($credito_id){   
        $resultado = Cuota::listartodo($credito_id);
        $lista = $resultado->get();
        $credito = Credito::find($credito_id);
        $cliente = Persona::find($credito->persona_id);
        $nombres_cliente = $cliente->nombres.' '.$cliente->apellidos;

        $titulo ='Detalle de cuotas - '.$cliente->codigo;
        $view = \View::make('app.credito.reportecuotas')->with(compact('lista','credito', 'nombres_cliente'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(5);
        PDF::SetLeftMargin(5);
        PDF::SetRightMargin(5);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

/*************--RECIBO PAGO CUOTA PDF--********** */
    


    public function generarecibopagocuotaPDF($cuota_id){
        $caja = Caja::where("estado","=","A")->get();
        $fecha_actual =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaapert));
        
        $cuota = Cuota::find($cuota_id);
        $mora_cuota = $this->calcular_mora($cuota,$fecha_actual);
        $cuota->interes_mora = $mora_cuota;
        $credito = Credito::find($cuota->credito_id);
        $persona = Persona::find($credito->persona_id);
        $periodocredito = $credito->periodo;
        $numoperacion = 00;
        
        $cuota_s = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 1))->first();
        $cuota_s1 = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 2))->first();
        $cuota_s2 = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 3))->first();
        $cuota_s  = ($cuota_s  == "")? null: $cuota_s;
        $cuota_s1 = ($cuota_s1 == "")? null: $cuota_s1;
        $cuota_s2 = ($cuota_s2 == "")? null: $cuota_s2;

        $configuraciones = configuraciones::all()->last();
        $ahorroactual = DB::table('ahorros')->where('persona_id', $persona->id)->where('fechaf','=',null)->value('capital');
        $titulo ='Voucher-Pago cuota-'.$persona->codigo;
        $view = \View::make('app.credito.recibopagocuota')->with(compact('cuota','credito', 'persona', 'periodocredito','numoperacion', 'cuota_s','cuota_s1', 'cuota_s2','configuraciones'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(5);
        PDF::SetLeftMargin(5);
        PDF::SetRightMargin(5);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }
    public function generarecibopagocuotaPDF2($transaccion_id){
        $transaccion = Transaccion::find($transaccion_id);
        $numero_cuot = intval(trim(explode(':',$transaccion->descripcion)[1]));
        $cuota = Cuota::where('credito_id', '=', $transaccion->id_tabla)->where('numero_cuota', '=', $numero_cuot)->get()[0];

        //$cuota = Cuota::find($cuota_id);
        $credito = Credito::find($cuota->credito_id);
        $persona = Persona::find($credito->persona_id);
        $periodocredito = $credito->periodo;
        $numoperacion = 00;
        
        $cuota_s = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 1))->first();
        $cuota_s1 = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 2))->first();
        $cuota_s2 = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 3))->first();
        $cuota_s  = ($cuota_s  == "")? null: $cuota_s;
        $cuota_s1 = ($cuota_s1 == "")? null: $cuota_s1;
        $cuota_s2 = ($cuota_s2 == "")? null: $cuota_s2;
        $configuraciones = configuraciones::all()->last();
        
        //$ahorroactual = DB::table('ahorros')->where('persona_id', $persona->id)->where('fechaf','=',null)->value('capital');
        $titulo ='Voucher-Pago cuota-'.$persona->codigo;
        $view = \View::make('app.credito.recibopagocuotapdf')->with(compact('cuota','credito', 'persona', 'periodocredito','numoperacion', 'cuota_s','cuota_s1', 'cuota_s2','transaccion','configuraciones'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(5);
        PDF::SetLeftMargin(5);
        PDF::SetRightMargin(5);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

/*************--RECIBO AMORTIZACION PDF--********** */
    public function generareciboamortizacionPDF($transaccion_id, $credito_id){   
        $transaccion = Transaccion::find($transaccion_id);
        $credito = Credito::find($credito_id);
        $persona = Persona::find($credito->persona_id);
        $periodocredito = $credito->periodo;
        $cuotas =array();
        $cadenaC = explode(':',$transaccion->descripcion)[1];
        $numerosCuotas = explode(',',$cadenaC);
        for($i=0; $i<count($numerosCuotas) -1 ; $i++){
            $num =trim($numerosCuotas[$i]);
            $cuota = Cuota::where('credito_id', '=', $credito->id)->where('numero_cuota','=', $num)->get()[0];
            $cuotas[$i]=$cuota;
        }

        $ahorroactual = DB::table('ahorros')->where('persona_id', $persona->id)->where('fechaf','=',null)->value('capital');
        $titulo ='Voucher-amortización cuotas-'.$persona->codigo;
        $view = \View::make('app.credito.reciboamortizacionpdf')->with(compact('transaccion','credito', 'persona', 'periodocredito','numoperacion', 'cuotas'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(5);
        PDF::SetLeftMargin(5);
        PDF::SetRightMargin(5);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

/*************--RECIBO CREDITO PDF--************* */
    public function generarecibocreditoPDF($credito_id){   
        
        $credito = Credito::find($credito_id);
        $persona = Persona::find($credito->persona_id);
        $numoperacion = '--';
        $titulo ='Voucher-Credito -'.$persona->codigo;
        $view = \View::make('app.credito.recibocreditopdf')->with(compact('credito', 'persona', 'numoperacion'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(5);
        PDF::SetLeftMargin(5);
        PDF::SetRightMargin(5);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

/*************--OTRAS FUNCIONES--**************** */
    public function rouNumber($numero, $decimales) { 
        $factor = pow(10, $decimales); 
        return (round($numero*$factor)/$factor);
    }

//listar el objeto persona por dni
    public function getPersona(Request $request, $persona_id){
        if($request->ajax()){
            $res = (new Credito())->getpersonacredito($persona_id);
            return response()->json($res);
        }
    }

/*************--LISTA PERSONAS--************ */
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

/*************--LISTA DE CUOTAS A LA FECHA--************ */
    public function cuotasalafecha(Request $request){
        $persona_id = $request->input('persona_id');
        $credito_id = $request->input('credito_id');
        $opcion = $request->input('opcion');
        $anio = $request->input('anio');
        $mes = $request->input('mes');
        $cuotas  = Cuota::listarCuotasAlafechaPersona($anio,$mes, $persona_id, $credito_id, $opcion)->get();

        return response()->json($cuotas);
    }


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

public function calcular_mora($cuota, $fechaFinal){
    $interes_mora =0;

    if($cuota->fecha_iniciomora != null){
        $num_meses = $this->numero_meses($cuota->fecha_iniciomora,$fechaFinal);
        if($num_meses > 0){
            $interes_mora = $num_meses*($cuota->tasa_interes_mora/100) * ($cuota->saldo_restante + $cuota->parte_capital);
        }
    }
    return $interes_mora;
}

/*************--PAGAR TODO EL CREDITO--************ */
    public function pagarcreditototal(Request $request){

        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $res = null;
        $transaccion_id = 0;
        if($caja_id != 0){
            $error = DB::transaction(function() use($request, $caja_id){
                $credito = Credito::find($request->get('credito_id'));
                $persona = Persona::find($credito->persona_id);
                //$monto_total = $request->get('monto_suma');
                $capital_total = $request->get('capital_total');
                $interes_total = $request->get('interes_total');
                $interes_mora_total = $request->get('interes_mora_total');
                $fecha_pago = $request->get('fechaop');
                $num_cuotas = $request->get('num_cuotas_p');
                $monto_total = $capital_total + $interes_total + $interes_mora_total;
                $monto_t_2 = $monto_total;
                $capital_restante = $capital_total;
                $num_cuota_pfinal=0;

                $cuotas = Cuota::where('credito_id','=', $credito->id)->where('estado','!=', '1')->where('deleted_at','=', null)->orderby('numero_cuota', 'ASC')->get();
                if($num_cuotas>0){
                    $num_cuota_pfinal = $cuotas[$num_cuotas - 1]->numero_cuota;
                }else{
                    $num_cuota_pfinal = $cuotas[0]->numero_cuota;
                }
                for($i=0; $i<count($cuotas); $i++){
                    if($i< $num_cuotas){
                        $interes_ganado =0;
		
                        if($cuotas[$i]->fecha_iniciomora != null){
                            $fecha_init = date("Y-m-d", strtotime($cuotas[$i]->fecha_iniciomora));
                            $fecha_inicial = new DateTime($fecha_init);
                            $fecha_fin =null;
                            if($cuotas[$i]->fecha_pago == null){
                                $fecha_fin= date("Y-m-d", strtotime($fecha_pago));
                            }else{
                                $fecha_fin= date("Y-m-d", strtotime($cuotas[$i]->fecha_pago));
                            }
                            $fecha_final = new DateTime($fecha_fin);
                            $diferencia = $fecha_inicial->diff( $fecha_final);
                            $numeroDias = $diferencia->format('%R%a días');
                           
                            if($numeroDias>0){
                                $interes_ganado += $numeroDias*($cuotas[$i]->tasa_interes_mora/100) * ($cuotas[$i]->parte_capital + $cuotas[$i]->interes);
                            }
                            $num_meses = $this->numero_meses($cuotas[$i]->fecha_iniciomora,$fecha_pago);
                            $cuotas[$i]->interes += $cuotas[$i]->interes*$num_meses;
                        }
                        if($i == $num_cuotas-1 ){
                            //$monto_t_2 =  $monto_t_2 -  $cuotas[$i]->interes- round($interes_ganado, 1);
                            //$capital_restante = $capital_restante - $cuotas[$i]->parte_capital;
                            $cuotas[$i]->estado = '1';
                            $cuotas[$i]->parte_capital = $capital_restante;
                            $cuotas[$i]->fecha_pago =  $fecha_pago;
                            $cuotas[$i]->saldo_restante = 0;
                            $cuotas[$i]->interes_mora = round($interes_ganado, 1);
                            $cuotas[$i]->save();
                        }else{
                            $capital_restante = $capital_restante - $cuotas[$i]->parte_capital;
                            $cuotas[$i]->estado = '1';
                            $cuotas[$i]->fecha_pago =  $fecha_pago;
                            
                            $cuotas[$i]->interes_mora = round($interes_ganado, 1);
                            $cuotas[$i]->saldo_restante = round($capital_restante,1);
                            $cuotas[$i]->save();
                            $monto_t_2 =  $monto_t_2 - $cuotas[$i]->interes_mora - $cuotas[$i]->parte_capital - $cuotas[$i]->interes;
                        }
                        
                    }else{
                        if($num_cuotas>0){
                            $cuotas[$i]->estado = '1';
                            $cuotas[$i]->fecha_pago =  $fecha_pago;
                            $cuotas[$i]->saldo_restante = 0;
                            $cuotas[$i]->interes = 0;
                            $cuotas[$i]->interes_mora = 0;
                            $cuotas[$i]->parte_capital = 0;
                            $cuotas[$i]->save();
                        }else{
                            if($i == 0){
                                $cuotas[$i]->estado = '1';
                                $cuotas[$i]->fecha_pago =  $fecha_pago;
                                $cuotas[$i]->saldo_restante = 0;
                                $cuotas[$i]->interes = 0;
                                $cuotas[$i]->interes_mora = 0;
                                $cuotas[$i]->parte_capital = $monto_t_2;
                                $cuotas[$i]->save();
                            }else{
                                $cuotas[$i]->estado = '1';
                                $cuotas[$i]->fecha_pago =  $fecha_pago;
                                $cuotas[$i]->saldo_restante = 0;
                                $cuotas[$i]->interes = 0;
                                $cuotas[$i]->interes_mora = 0;
                                $cuotas[$i]->parte_capital = 0;
                                $cuotas[$i]->save();
                            }
                            
                        }
                        
                    }
                    
                }
                $credito->estado = 1;
                $credito->save();
                $configuraciones = configuraciones::all()->last();
                if($num_cuotas>0){
                    if($configuraciones->valor_recibo > 0){

                        $concepto_id = 8;
                        $transaccion2 = new Transaccion();
                        $transaccion2->fecha = $fecha_pago;
                        $transaccion2->monto = $configuraciones->valor_recibo;//0.2;
                        $transaccion2->concepto_id = $concepto_id;
                        $transaccion2->descripcion ='Comision por Recibo Pago Cuota';
                        $transaccion2->persona_id =  $persona->id;
                        $transaccion2->usuario_id = (new Credito())->idUser();
                        $transaccion2->caja_id = $caja_id;
                        $transaccion2->comision_voucher = $configuraciones->valor_recibo;//0.2;
                        $transaccion2->save();
                    }
                }
                

                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $fecha_pago;
                $transaccion->monto = round($monto_total,1);
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = "Cancelado total del credito, cuota N°: ".$num_cuota_pfinal;
                $transaccion->persona_id = $persona->id;
                $transaccion->usuario_id = (new Credito())->idUser();
                $transaccion->caja_id = $caja_id; 
                $transaccion->cuota_parte_capital = round($capital_total, 1);
                $transaccion->cuota_interes = round($interes_total,1);
                $transaccion->cuota_mora = $interes_mora_total;
                $transaccion->id_tabla = $credito->id;
                $transaccion->inicial_tabla = 'CU';
                $transaccion->save();
                $transaccion_id = $transaccion->id;
            });
            $res = $error;
        }else{
            $res = 'Caja no aperturada, asegurece de aperturar primero para registrar alguna transacción.!';
        }
        return is_null($res) ? "OK" : $res;
    }

/*************--OBTENER MONTO TOTAL--************ */
    public function obtenermontototal(Request $request){
        $cuotas = Cuota::where('credito_id','=', $request->get('credito_id'))->where('estado','!=', '1')->where('deleted_at','=', null)->get();
        $valor_total = 0;
        $fechaOp = $request->get('fechaoperacion');
        $anio_mes = date('Y-m', strtotime($fechaOp));
        $interes_total = 0;
        $parte_capital_total = 0;
        $num_cuotas_pendientes =0;
        $num_cuotas_morosas =0;
        $interes_moratorio = 0;
        for($i =0; $i<count($cuotas); $i++){
            if(date('Y-m', strtotime($cuotas[$i]->fecha_programada_pago)) <= $anio_mes){

                $interes_ganado =0;
		
                if($cuotas[$i]->fecha_iniciomora != null){
                    $fecha_init = date("Y-m-d", strtotime($cuotas[$i]->fecha_iniciomora));
                    $fecha_inicial = new DateTime($fecha_init);
                    $fecha_fin =null;
                    if($cuotas[$i]->fecha_pago == null){
                        $fecha_fin= date("Y-m-d", strtotime($fechaOp));
                    }else{
                        $fecha_fin= date("Y-m-d", strtotime($cuotas[$i]->fecha_pago));
                    }
                    $fecha_final = new DateTime($fecha_fin);
                    $diferencia = $fecha_inicial->diff( $fecha_final);
                    $numeroDias = $diferencia->format('%R%a días');
                    
                    if($numeroDias>0){
                        $interes_ganado = $numeroDias*($cuotas[$i]->tasa_interes_mora/100) * ($cuotas[$i]->parte_capital + $cuotas[$i]->interes);
                    }
                     $num_meses = $this->numero_meses($cuotas[$i]->fecha_iniciomora,$fechaOp);
                     

                    $interes_moratorio += round($interes_ganado, 1);
                    $interes_total += round($cuotas[$i]->interes * $num_meses, 1);
                    $num_cuotas_morosas++;

                }

                
                $interes_total += round($cuotas[$i]->interes, 1);
                //$valor_total += round($cuotas[$i]->parte_capital, 1) + round($cuotas[$i]->interes,1) + $interes_ganado;
                $parte_capital_total += $cuotas[$i]->parte_capital ;
                $num_cuotas_pendientes ++;
            }else{
                //$valor_total += round($cuotas[$i]->parte_capital, 1);
                $parte_capital_total += round($cuotas[$i]->parte_capital,1);
            }
        }
        $configuraciones = configuraciones::all()->last();
        $comision_voucher = ($interes_total>0? $configuraciones->valor_recibo: 0);
        $valor_total += $interes_total + $parte_capital_total + $interes_moratorio + $comision_voucher;
        return  array(round($valor_total, 1), round($parte_capital_total, 1), round($interes_total, 1), round($num_cuotas_pendientes, 1), round($num_cuotas_morosas, 1), round($interes_moratorio, 1));
    }
    
    public function vista_refinanciar(Request $request){
        $cajas = Caja::where('estado','=','A')->where('deleted_at','=',null)->get();
        $caja = null;
        $fecha_actual = null;
        $ruta = $this->rutas;
        $num_cuotas_porpagar = 0;
        if(count($cajas)>0){
            $caja = $cajas[0];
            $fecha_actual = $caja->fecha_horaapert;

            $anio_mes = date('Y-m',strtotime($fecha_actual))."-01";
            $fecha_p = new DateTime($anio_mes);
            $fecha_p->modify('last day of this month');
            $fecha_p->format('Y-m-d');
            
            $credito = Credito::find($request->get('credito_id'));
            $num_cuotas_porpagar = count(Cuota::where('credito_id', '=',$credito->id)->where('fecha_programada_pago','<=',$fecha_p)->where('estado','!=','1')->where('deleted_at','=',null)->get());
            $num_cuotas_totales = count(Cuota::where('parte_capital','!=', 0)->where('credito_id','=',$credito->id)->get());
            $fecha_request = date('Y-m',strtotime($fecha_actual))."-01";
            $fecha_siguiente_cuota = date("Y-m-d",strtotime($fecha_request."+ 1 month"));
            $anio = date('Y',strtotime($fecha_siguiente_cuota)); 
            $mes = date('m',strtotime($fecha_siguiente_cuota)); 

            $cuotasPendientes = Cuota::where('estado','!=','1')->where('credito_id','=',$credito->id)->where('deleted_at','=',null)->get();
            // $cuotasPendientes = Cuota::where('estado','!=','1')->where('credito_id','=',$credito->id)->where('deleted_at','=',null)->where(DB::raw('extract( month from fecha_programada_pago)'),'>=',$mes)->where(DB::raw('extract( year from fecha_programada_pago)'),'>=',$anio)->get();

            $numero_cuotas_pendientes = count($cuotasPendientes);
            $cuota_siguiente = null;
            if($numero_cuotas_pendientes > 0){
                $cuota_siguiente = Cuota::where('credito_id','=', $credito->id)->where(DB::raw('extract( month from fecha_programada_pago)'),'=',$mes)->where(DB::raw('extract( year from fecha_programada_pago)'),'=',$anio)->where('deleted_at','=',null)->get()[0];
            }
            $saldo_restante = $cuota_siguiente != null? round($cuota_siguiente->parte_capital + $cuota_siguiente->saldo_restante,1): 0;
            
            $persona = Persona::find($credito->persona_id);
            return view($this->folderview.'.vistarefinanciacion')->with(compact('saldo_restante','ruta','credito','persona','caja','numero_cuotas_pendientes','fecha_actual','num_cuotas_porpagar','cuota_siguiente'));
        }else{
            return view($this->folderview.'.vistarefinanciacion')->with(compact('fecha_actual','caja','num_cuotas_porpagar'));
        }
    }
    public function guardar_refinanciacionAnt(Request $request){
        /**
         * 1>pagoCuota y amortizacion de saldo restante, modificando el valor de cuotas siguiente
         * 2>pagoCuota y amortizacion de saldo restante, manteniendo el valor de cuota, y disminuyendo las ultimas cuotas
         * 3>pagoCuota y amortizacion de saldo restante, acortando el numero de cuotas y modificando el valor de cuotas
         */
        
        $saldo_restante_credito = $request->get('saldo_rest');
        $credito = Credito::find($request->get('credito_id'));
        $num_cuotas_anterior_p = $request->get('num_cuotas_anterior_p');
        $num_cuotas_nuevos_p = $request->get('num_cuotas_nuevas_p');
        $monto_amortizar = $request->get('monto_amortizar');
        $persona = Persona::find($credito->persona_id);
        // $tipo = $request->get('tipomodo');
        $fecha = $request->get('fecha_ref');
        $montorestante = $saldo_restante_credito;
        $error = DB::transaction(function() use($montorestante,$saldo_restante_credito, $num_cuotas_nuevos_p,$num_cuotas_anterior_p,  $credito,$persona, $fecha, $monto_amortizar){
            
            $valor_saldo =  $saldo_restante_credito;
            $nuevo_valor_cuota =  (($credito->tasa_interes/100) * $valor_saldo) / (1 - (pow(1/(1+($credito->tasa_interes/100)), $num_cuotas_nuevos_p)));
            $cuotas = Cuota::where('credito_id','=', $credito->id)->where('estado','!=', '1')->where('deleted_at', '=', null)->get();
            $numero_cuota = $credito->periodo - $num_cuotas_anterior_p + 1;
            $ult_cuota = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($numero_cuota - 1))->where('deleted_at','=',null)->get();
            $ultima_cuota_pagada = count($ult_cuota)> 0?$ult_cuota[0]: null;
            if($num_cuotas_anterior_p == $num_cuotas_nuevos_p){
                $explod = explode('-',date("Y-m-d",strtotime($fecha)));
                $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                $interesAcumulado = 0.00;
                for($i=0;$i<$num_cuotas_anterior_p; $i++){
                    $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month"));
                    $montInteres = ($credito->tasa_interes/100) * $montorestante;
                    $interesAcumulado +=  $montInteres;
                
                    $montCapital = round($nuevo_valor_cuota, 1) - round($montInteres, 1); 
                
                    if($i < (int)($num_cuotas_nuevos_p - 1)){
                        $montorestante = $montorestante - $montCapital;
                    }
                    if($i == (int)($num_cuotas_nuevos_p - 1)){
                        $montCapital = round($montorestante, 1);
                        $montInteres =  round($nuevo_valor_cuota, 1) - $montCapital;
                        $montorestante = 0;
                    }
                    $fecha_p = new DateTime($fechacuota);
                    $fecha_p->modify('last day of this month');
                    $fecha_p->format('Y-m-d');

                    $cuotas[$i]->parte_capital = round($montCapital, 1); 
                    $cuotas[$i]->interes = round($montInteres, 1);
                    $cuotas[$i]->saldo_restante = round($montorestante, 1);
                    $cuotas[$i]->numero_cuota = $numero_cuota;
                    $cuotas[$i]->fecha_programada_pago = $fecha_p;
                    $cuotas[$i]->save();
                    $numero_cuota++;
                }
                 $credito->descripcion =  $credito->descripcion.". Credito refinanciado en la fecha: ".date('Y/m/d',strtotime($fecha)) ; 
                 $credito->save();

            }else if($num_cuotas_anterior_p > $num_cuotas_nuevos_p){
                $explod = explode('-',date("Y-m-d",strtotime($fecha)));
                $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                $interesAcumulado = 0.00;
                for($i=0;$i<$num_cuotas_anterior_p; $i++){
                     
                    if($i<$num_cuotas_nuevos_p){
                        $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month"));
                        $montInteres = ($credito->tasa_interes/100) * $montorestante;
                        $interesAcumulado +=  $montInteres;
                    
                        $montCapital = round($nuevo_valor_cuota, 1) - round($montInteres, 1); 
                    
                        if($i < (int)($num_cuotas_nuevos_p - 1)){
                            $montorestante = $montorestante - $montCapital;
                        }
                        if($i == (int)($num_cuotas_nuevos_p - 1)){
                            $montCapital = round($montorestante, 1);
                            $montInteres =  round($nuevo_valor_cuota, 1) - $montCapital;
                            $montorestante = 0;
                        }
                        $fecha_p = new DateTime($fechacuota);
                        $fecha_p->modify('last day of this month');
                        $fecha_p->format('Y-m-d');

                        $cuotas[$i]->parte_capital = round($montCapital, 1); 
                        $cuotas[$i]->interes = round($montInteres, 1);
                        $cuotas[$i]->saldo_restante = round($montorestante, 1);
                        $cuotas[$i]->numero_cuota = $numero_cuota;
                        $cuotas[$i]->fecha_programada_pago = $fecha_p;
                        $cuotas[$i]->save();
                        $numero_cuota++;
                    }else{
                        $cuotas[$i]->delete();
                    }
                }
                $credito->periodo = $numero_cuota -1 ; 
                $credito->descripcion =  $credito->descripcion.". Credito refinanciado en la fecha: ".date('Y/m/d',strtotime($fecha)) ; 
                $credito->save();
            }else if($num_cuotas_anterior_p < $num_cuotas_nuevos_p){
                $explod = explode('-',date("Y-m-d",strtotime($fecha)));
                $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                $interesAcumulado = 0.00;

                for($i=0;$i<$num_cuotas_anterior_p; $i++){
                    $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month")); 
                    $montInteres = ($credito->tasa_interes/100) * $montorestante; 
                    $interesAcumulado +=  $montInteres; 
                
                    $montCapital = round($nuevo_valor_cuota, 1) - round($montInteres, 1); 
                    $montorestante = $montorestante - $montCapital;
                    $fecha_p = new DateTime($fechacuota);
                    $fecha_p->modify('last day of this month');
                    $fecha_p->format('Y-m-d');
                
                    //$cuota = new Cuota();
                    $cuotas[$i]->parte_capital = round($montCapital, 1); 
                    $cuotas[$i]->interes = round($montInteres, 1);
                    $cuotas[$i]->interes_mora = 0.00;
                    $cuotas[$i]->saldo_restante = round($montorestante, 1);
                    $cuotas[$i]->numero_cuota = $numero_cuota;
                    $cuotas[$i]->fecha_programada_pago = $fecha_p;
                    $cuotas[$i]->estado = '0';//0=PENDIENTE; 1 = PAGADO; m = MOROSO
                    $cuotas[$i]->save();
                    $numero_cuota++;
                }
                for($i=$num_cuotas_anterior_p;$i< $num_cuotas_nuevos_p; $i++){
                    $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month")); 
                    $montInteres = ($credito->tasa_interes/100) * $montorestante; 
                    $interesAcumulado +=  $montInteres; 
                
                    $montCapital = round($nuevo_valor_cuota, 1) - round($montInteres, 1); 
                    //$montorestante = $montorestante - $montCapital;
                    if($i < (int)($num_cuotas_nuevos_p - 1)){
                        $montorestante = $montorestante - $montCapital;
                    }
                    
                    $fecha_p = new DateTime($fechacuota);
                    $fecha_p->modify('last day of this month');
                    $fecha_p->format('Y-m-d');
                    
                    if($i == (int)($num_cuotas_nuevos_p - 1)){
                        $montCapital = round($montorestante, 1);
                        $montInteres =  round($nuevo_valor_cuota, 1) - $montCapital;
                        $montorestante = 0;
                    }
                    $cuota = new Cuota();
                    $cuota->parte_capital = round($montCapital, 1); 
                    $cuota->interes = round($montInteres, 1);
                    $cuota->interes_mora = 0.00;
                    $cuota->saldo_restante = round($montorestante, 1);
                    $cuota->numero_cuota = $numero_cuota;
                    $cuota->fecha_programada_pago = $fecha_p;
                    $cuota->estado = '0';//0=PENDIENTE; 1 = PAGADO; m = MOROSO
                    $cuota->credito_id = $credito->id;
                    $cuota->save();
                    $numero_cuota++;
                }
                $credito->periodo = $numero_cuota -1 ;
                $credito->descripcion =  $credito->descripcion.". Credito refinanciado en la fecha: ".date('Y/m/d',strtotime($fecha)) ; 
                $credito->save();
            }
            if( $ultima_cuota_pagada != null){
            $ultima_cuota_pagada->parte_capital = round($ultima_cuota_pagada->parte_capital +  $monto_amortizar, 1);
            $ultima_cuota_pagada->saldo_restante = round($ultima_cuota_pagada->saldo_restante - $monto_amortizar, 1);
            $ultima_cuota_pagada->save();
            }
        });
        return $error == null?"OK":$error;
    }
    public function guardar_refinanciacion(Request $request){
        /**
         * 1>pagoCuota y amortizacion de saldo restante, modificando el valor de cuotas siguiente
         * 2>pagoCuota y amortizacion de saldo restante, manteniendo el valor de cuota, y disminuyendo las ultimas cuotas
         * 3>pagoCuota y amortizacion de saldo restante, acortando el numero de cuotas y modificando el valor de cuotas
         */

        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $res = null;

        if( $caja_id != 0){

        
            $saldo_restante_credito = $request->get('saldo_rest');
            $credito = Credito::find($request->get('credito_id'));
            $num_cuotas_anterior_p = $request->get('num_cuotas_anterior_p');
            $num_cuotas_nuevos_p = $request->get('num_cuotas_nuevas_p');
            $monto_amortizar = $request->get('monto_amortizar');
            $persona = Persona::find($credito->persona_id);
            // $tipo = $request->get('tipomodo');
            $fecha = $request->get('fecha_ref');
            $montorestante = $saldo_restante_credito;
            $error = DB::transaction(function() use($montorestante,$saldo_restante_credito, $num_cuotas_nuevos_p,$num_cuotas_anterior_p,  $credito,$persona, $fecha, $monto_amortizar, $caja_id){
                
                $valor_saldo =  $saldo_restante_credito;
                $nuevo_valor_cuota =  (($credito->tasa_interes/100) * $valor_saldo) / (1 - (pow(1/(1+($credito->tasa_interes/100)), $num_cuotas_nuevos_p)));
                $cuotas = Cuota::where('credito_id','=', $credito->id)->where('estado','!=', '1')->where('deleted_at', '=', null)->get();
                // $numero_cuota = $credito->periodo - $num_cuotas_anterior_p + 1;
                $numero_cuota = count(Cuota::where('credito_id','=', $credito->id)->where('fecha_pago','!=', null)->where('deleted_at','=', null)->get()) +1;
                $numero_ultima_cuota_pagada = ($numero_cuota -1);
                $ult_cuota = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($numero_cuota - 1))->where('deleted_at','=',null)->get();
                $ultima_cuota_pagada = count($ult_cuota)> 0?$ult_cuota[0]: null;
                if(count($cuotas) == $num_cuotas_nuevos_p){
                    $explod = explode('-',date("Y-m-d",strtotime($fecha)));
                    $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                    $interesAcumulado = 0.00;
                    for($i=0;$i<count($cuotas); $i++){
                        $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month"));
                        $montInteres = ($credito->tasa_interes/100) * $montorestante;
                        $interesAcumulado +=  $montInteres;
                        $montCapital = round($nuevo_valor_cuota, 1) - round($montInteres, 1); 
                    
                        if($i < (int)($num_cuotas_nuevos_p - 1)){
                            $montorestante = $montorestante - $montCapital;
                        }
                        if($i == (int)($num_cuotas_nuevos_p - 1)){
                            $montCapital = round($montorestante, 1);
                            $montInteres =  round($nuevo_valor_cuota, 1) - $montCapital;
                            $montorestante = 0;
                        }
                        $fecha_p = new DateTime($fechacuota);
                        $fecha_p->modify('last day of this month');
                        $fecha_p->format('Y-m-d');

                        $cuotas[$i]->parte_capital = round($montCapital, 1); 
                        $cuotas[$i]->interes = round($montInteres, 1);
                        $cuotas[$i]->saldo_restante = round($montorestante, 1);
                        $cuotas[$i]->numero_cuota = $numero_cuota;
                        $cuotas[$i]->fecha_programada_pago = $fecha_p;
                        $cuotas[$i]->save();
                        $numero_cuota++;
                    }
                    $credito->descripcion =  $credito->descripcion.". Ref. el: ".date('d/m/Y',strtotime($fecha)) ; 
                    $credito->save();

                }else if(count($cuotas) > $num_cuotas_nuevos_p){
                    $explod = explode('-',date("Y-m-d",strtotime($fecha)));
                    $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                    $interesAcumulado = 0.00;
                    for($i=0;$i<count($cuotas); $i++){
                        
                        if($i<$num_cuotas_nuevos_p){
                            $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month"));
                            $montInteres = ($credito->tasa_interes/100) * $montorestante;
                            $interesAcumulado +=  $montInteres;
                        
                            $montCapital = round($nuevo_valor_cuota, 1) - round($montInteres, 1); 
                        
                            if($i < (int)($num_cuotas_nuevos_p - 1)){
                                $montorestante = $montorestante - $montCapital;
                            }
                            if($i == (int)($num_cuotas_nuevos_p - 1)){
                                $montCapital = round($montorestante, 1);
                                $montInteres =  round($nuevo_valor_cuota, 1) - $montCapital;
                                $montorestante = 0;
                            }
                            $fecha_p = new DateTime($fechacuota);
                            $fecha_p->modify('last day of this month');
                            $fecha_p->format('Y-m-d');

                            $cuotas[$i]->parte_capital = round($montCapital, 1); 
                            $cuotas[$i]->interes = round($montInteres, 1);
                            $cuotas[$i]->saldo_restante = round($montorestante, 1);
                            $cuotas[$i]->numero_cuota = $numero_cuota;
                            $cuotas[$i]->fecha_programada_pago = $fecha_p;
                            $cuotas[$i]->save();
                            $numero_cuota++;
                        }else{
                            $cuotas[$i]->parte_capital = 0; 
                            $cuotas[$i]->interes = 0;
                            $cuotas[$i]->saldo_restante = 0;
                            $cuotas[$i]->estado = '1';
                            $cuotas[$i]->save();
                            //$cuotas[$i]->delete();
                        }
                    }
                // $credito->periodo = $numero_cuota -1 ;
                    $credito->descripcion =  $credito->descripcion.". Ref. el: ".date('d/m/Y',strtotime($fecha)) ; 
                    $credito->save();
                }else if(count($cuotas) < $num_cuotas_nuevos_p){
                    $explod = explode('-',date("Y-m-d",strtotime($fecha)));
                    $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                    $interesAcumulado = 0.00;

                    for($i=0;$i<count($cuotas); $i++){
                        $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month")); 
                        $montInteres = ($credito->tasa_interes/100) * $montorestante; 
                        $interesAcumulado +=  $montInteres; 
                    
                        $montCapital = round($nuevo_valor_cuota, 1) - round($montInteres, 1); 
                        $montorestante = $montorestante - $montCapital;
                        $fecha_p = new DateTime($fechacuota);
                        $fecha_p->modify('last day of this month');
                        $fecha_p->format('Y-m-d');
                    
                        //$cuota = new Cuota();
                        $cuotas[$i]->parte_capital = round($montCapital, 1); 
                        $cuotas[$i]->interes = round($montInteres, 1);
                        $cuotas[$i]->interes_mora = 0.00;
                        $cuotas[$i]->saldo_restante = round($montorestante, 1);
                        $cuotas[$i]->numero_cuota = $numero_cuota;
                        $cuotas[$i]->fecha_programada_pago = $fecha_p;
                        $cuotas[$i]->estado = '0';//0=PENDIENTE; 1 = PAGADO; m = MOROSO
                        $cuotas[$i]->save();
                        $numero_cuota++;
                    }
                    for($i=count($cuotas);$i< $num_cuotas_nuevos_p; $i++){
                        $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month")); 
                        $montInteres = ($credito->tasa_interes/100) * $montorestante; 
                        $interesAcumulado +=  $montInteres; 
                    
                        $montCapital = round($nuevo_valor_cuota, 1) - round($montInteres, 1); 
                        //$montorestante = $montorestante - $montCapital;
                        if($i < (int)($num_cuotas_nuevos_p - 1)){
                            $montorestante = $montorestante - $montCapital;
                        }
                        
                        $fecha_p = new DateTime($fechacuota);
                        $fecha_p->modify('last day of this month');
                        $fecha_p->format('Y-m-d');
                        
                        if($i == (int)($num_cuotas_nuevos_p - 1)){
                            $montCapital = round($montorestante, 1);
                            $montInteres =  round($nuevo_valor_cuota, 1) - $montCapital;
                            $montorestante = 0;
                        }
                        $cuota = new Cuota();
                        $cuota->parte_capital = round($montCapital, 1); 
                        $cuota->interes = round($montInteres, 1);
                        $cuota->interes_mora = 0;
                        $cuota->saldo_restante = round($montorestante, 1);
                        $cuota->numero_cuota = $numero_cuota;
                        $cuota->fecha_programada_pago = $fecha_p;
                        $cuota->estado = '0';//0=PENDIENTE; 1 = PAGADO; m = MOROSO
                        $cuota->credito_id = $credito->id;
                        $cuota->save();
                        $numero_cuota++;
                    }
                    $credito->periodo = $numero_cuota -1 ;
                    $credito->descripcion =  $credito->descripcion.". Ref. el: ".date('d/m/Y',strtotime($fecha)) ; 
                    $credito->save();
                }
                if( $ultima_cuota_pagada != null){
                    $ultima_cuota_pagada->parte_capital = round($ultima_cuota_pagada->parte_capital +  $monto_amortizar, 1);
                    $ultima_cuota_pagada->saldo_restante = round($ultima_cuota_pagada->saldo_restante - $monto_amortizar, 1);
                    $ultima_cuota_pagada->save();

                    $concepto_id_refinanc = 23;
                    
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $fecha;
                    $transaccion->monto = round($monto_amortizar,1);
                    $transaccion->concepto_id =  $concepto_id_refinanc;
                    $transaccion->descripcion = "Refinanciacion de credito";
                    $transaccion->persona_id = $persona->id;
                    $transaccion->usuario_id = (new Credito())->idUser();
                    $transaccion->caja_id = $caja_id;
                    $transaccion->cuota_parte_capital = round($monto_amortizar,1);
                    $transaccion->id_tabla = $credito->id;
                    $transaccion->inicial_tabla = 'RC';
                    $transaccion->save();

                }
            });
        }else{
            $error ="Asegurese de aperturar caja primero";
        }
        return $error == null?"OK":$error;
    }
    public function vistareporte(){
        $ruta = $this->rutas;
       
        $caja = Caja::where('estado','=','A')->where('deleted_at','=',null)->get();
        $fecha_inicio = count($caja) >0? date('Y-m-d', strtotime($caja[0]->fecha_horaapert)) : date('Y-m-d');
        return view('app.credito.vistareporte')->with(compact('fecha_inicio','ruta'));
    }

    public function reportecreditos(Request $request){
        $fechainicio = date('Y-m-d', strtotime($request->get('fechainicio')));
        $fechafinal = date('Y-m-d', strtotime($request->get('fechafinal')));
        $listaCreditos = (new Credito())->lista_creditos_desde_hasta($fechainicio, $fechafinal);

        $titulo ='Reporte de creditos desde: '.date('d/m/Y', strtotime($request->get('fechainicio')))." hasta ".date('d/m/Y', strtotime($request->get('fechafinal')));
        $view = \View::make('app.credito.reportecreditos')->with(compact('listaCreditos', 'fechainicio','fechafinal'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('L', 'A4', 'es');
        PDF::SetTopMargin(5);
        PDF::SetLeftMargin(5);
        PDF::SetRightMargin(5);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');


    }
}