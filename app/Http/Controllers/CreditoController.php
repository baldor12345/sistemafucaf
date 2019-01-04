<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Credito;
use App\Persona;
use App\Cuota;
use App\caja;
use App\Transaccion;
use App\configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use PDF;

class CreditoController extends Controller{

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
        'listardetallecuotas' => 'creditos.listardetallecuotas',
        'vistapagocuota' => 'creditos.vistapagocuota',
        'pagarcuota' => 'creditos.pagarcuota',
        'generareportecuotasPDF' => 'creditos.generareportecuotasPDF',
        'generarecibopagocuotaPDF' => 'creditos.generarecibopagocuotaPDF',
        'generarecibocreditoPDF' => 'creditos.generarecibocreditoPDF',
        'abrirpdf' => 'creditos.abrirpdf'
    );

    public function __construct(){
        $this->middleware('auth');
    }
/*************--INICIO--************************* */
    public function index(){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $configuraciones = configuraciones::all()->last();
        $entidad = 'Credito';
        $ruta = $this->rutas;
        $title = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboEstado = array(0=>'Pendientes', 1 => 'Cancelados');
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboEstado','caja_id','configuraciones' ));
    }

/*************--LISTAR CREDITOS--**************** */
    public function buscar(Request $request){
        $entidad = 'Credito';
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $fechabusqueda = Libreria::getParam($request->input('fechabusqueda'));
        $estadobusqueda = Libreria::getParam($request->input('estadobusqueda'));
        $nombreclientebusqueda =$request->input('txtbusquedanombre');

        $resultado = Credito::listar($nombreclientebusqueda,$fechabusqueda,$estadobusqueda);
        $lista = $resultado->get();
        $cabecera = array();
        $cabecera[]  = array('valor' => '#', 'numero' => '1');
        $cabecera[]  = array('valor' => 'CODIGO', 'numero' => '1');
        $cabecera[]  = array('valor' => 'NOMBRE', 'numero' => '1');
        $cabecera[]  = array('valor' => 'MONTO CRÉDITO S/.', 'numero' => '1');
        $cabecera[]  = array('valor' => 'PERIODO', 'numero' => '1');
        $cabecera[]  = array('valor' => 'ESTADO', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Operaciones', 'numero' => '2');

        $ruta = $this->rutas;
        $titulo_detalle = $this->tituloDetallecredito;
        if (count($lista) > 0) {
            $clsLibreria = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion = $paramPaginacion['cadenapaginacion'];
            $inicio = $paramPaginacion['inicio'];
            $fin = $paramPaginacion['fin'];
            $paginaactual = $paramPaginacion['nuevapagina'];
            $lista = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera','titulo_detalle', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad','titulo_detalle'));
    }
/*************--MODAL NUEVO CREDITO--************ */
    public function create(Request $request){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $configuraciones = configuraciones::all()->last();
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'Credito';
        $credito = null;
        $ruta = $this->rutas;
        $formData = array('creditos.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('credito', 'formData', 'entidad', 'boton', 'listar', 'configuraciones','caja_id','ruta'));
    
    }
/*************--REGISTRO DE CREDITO--************ */
    public function store(Request $request){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $res = null;
        if($caja_id != 0){
            $reglas =array(
                'valor_credito' => 'required|max:20',
                'periodo' => 'required|max:50|integer',
                'tasa_interes' => 'required|max:20',
                'persona_id' => 'required|max:20'
            );
            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }
            $configuraciones = configuraciones::all()->last();
            $error = DB::transaction(function() use($request, $caja_id){
                $configuraciones = configuraciones::all()->last();
                $periodo = $request->input('periodo');
                $fechainicio = $request->input('fechacredito').date(" H:i:s");//**** */
                $fechafinal = strtotime ( '+'.$periodo.' month' , strtotime ( $fechainicio));
                $fechafinal = date( 'Y-m-d' , $fechafinal);
                $valorcredito = $request->get('valor_credito');
                $descripcion = $request->get('descripcion');
                $persona_id = $request->get('persona_id');
                $pers_aval_id= $request->get('pers_aval_id');
                $tasa_interes = $request->input('tasa_interes');
                $imprimivoucher = $request->get('imprimir_voucher');
                $tasa_multa = $configuraciones->tasa_interes_multa;

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

                $montorestante =  $valorcredito;
                $valor_cuota =  ($tasa_interes * $valorcredito) / (1 - (pow(1/(1+$tasa_interes), $periodo)));
                $fecha_actual = $fechainicio;
                $interesAcumulado = 0.00;
                for($i=0;$i<(int)$periodo; $i++){
                    $fecha_actual = date("Y-m-d",strtotime($fecha_actual."+ 1 month")); 
                    $montInteres = $tasa_interes * $montorestante; 
                    $interesAcumulado +=  $montInteres; 
                    $montCapital =$valor_cuota - $montInteres; 
                    $montorestante = $montorestante - $montCapital;

                    $cuota = new Cuota();
                    $cuota->parte_capital = $this->rouNumber($montCapital , 1); 
                    $cuota->interes = $this->rouNumber($montInteres , 1);
                    $cuota->interes_mora = 0.00;
                    $cuota->saldo_restante =$this->rouNumber($montorestante , 1);
                    $cuota->numero_cuota = $i + 1;
                    $cuota->fecha_programada_pago = $fecha_actual;
                    $cuota->estado = '0';//0=PENDIENTE; 1 = PAGADO; 2 = MOROSO
                    $cuota->credito_id = $credito->id;
                    $cuota->save();
                }
                //comision voucher si esque desea imprimirlo
                if($imprimivoucher == 1){
                    $concepto_id = 8;
                    $transaccion2 = new Transaccion();
                    $transaccion2->fecha = $fechainicio;
                    $transaccion2->monto = 0.1;
                    $transaccion2->concepto_id = $concepto_id;
                    $transaccion2->descripcion ='Comision por recibo credito';
                    $transaccion2->persona_id = $persona_id;
                    $transaccion2->usuario_id = Credito::idUser();
                    $transaccion2->caja_id = $caja_id;
                    $transaccion2->comision_voucher = 0.1;
                    $transaccion2->save();
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
                $transaccion->save();
            });
            $ultimo_credito = Credito::all()->last();
            $res = $error;
        }else{
            $res = 'Caja no aperturada, asegurece de aperturar primero para registrar alguna transacción.!';
        }
        $ultimo_credito = Credito::all()->last();
        $res = is_null($res) ? "OK" : $res;
        $respuesta = array($res, $ultimo_credito->id);
        return $respuesta;
    }
/*************--ELIMINAR CREDITO--*************** */
    public function eliminar($id, $listarLuego){
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo = Credito::find($id);
        $entidad = 'Credito';
        $formData = array('route' => array('credito.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
/*************--BORRAR CREDITO--***************** */
    public function destroy($id){
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $credito = Credito::find($id);
            $credito->delete();
        });
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
    public function vistapagocuota(Request $request, $cuota_id){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $existe = Libreria::verificarExistencia($cuota_id, 'cuota');
        if ($existe !== true) {
            return $existe;
        }
        $listar = Libreria::getParam($request->input('listar'), 'SI');

        $entidad_credito = 'Credito';
        $entidad_cuota = 'Cuota';
        $credito = null;
        
        $formData = array('creditos.pagarcuota');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad_cuota, 'autocomplete' => 'off');
        $boton = 'Registrar'; 
        $ruta = $this->rutas;
        $cuota = Cuota::find($cuota_id);
        $credito2 = Credito::find($cuota->credito_id);
        return view($this->folderview.'.pagarcuota')->with(compact('cuota', 'entidad_cuota', 'entidad_credito', 'credito','credito2', 'formData','listar','ruta'));
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
                $fecha_pago = $request->get('fecha_pago').date(" H:i:s");
                $id_cliente = $request->get('id_cliente');
                $imprimir_voucher = $request->get('imprimir_voucherpago');
                $comision_voucher = 0.2;

                //Actualiza cuota a estado cancelado
                $cuota = Cuota::find($id_cuota);
                $cuota->estado = 1;
                $cuota->fecha_pago = $fecha_pago;
                $cuota->save();

                //registra la comision por voucher en caja si desea imprimirlo
                $concepto_id = 8;
                if($imprimir_voucher==1){
                    $transaccion2 = new Transaccion();
                    $transaccion2->fecha = $fecha_pago;
                    $transaccion2->monto = $comision_voucher;
                    $transaccion2->concepto_id = $concepto_id;
                    $transaccion2->descripcion ='Comision por Recibo Pago Cuota';
                    $transaccion2->persona_id = $id_cliente;
                    $transaccion2->usuario_id = Credito::idUser();
                    $transaccion2->caja_id = $caja_id;
                    $transaccion2->comision_voucher = $comision_voucher;
                    $transaccion2->save();
                }
                //registramos en caja el pago cuota
                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $fecha_pago;
                $transaccion->monto = $cuota->parte_capital + $cuota->interes+ $cuota->interes_mora;
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = "Pago de Cuota";
                $transaccion->persona_id = $id_cliente;
                $transaccion->usuario_id = Credito::idUser();
                $transaccion->caja_id = $caja_id;
                $transaccion->cuota_parte_capital = $cuota->parte_capital;
                $transaccion->cuota_interes = $cuota->interes;
                $transaccion->cuota_mora = $cuota->interes_mora;
                $transaccion->save();

                //Modificamos el estado de credito si ya se cancelo todas las cuotas
                $credito = Credito::find($id_credito);
                if($credito->periodo == $cuota->numero_cuota){
                    $credito->estado = 1;
                    $credito->save();
                }
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
/*************--LISTAR DETALLE CUOTAS--********** */
    public function listardetallecuotas(Request $request){
        $credito_id =  $request->input('credito_id');
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $entidad_cuota = 'Cuota';
        $entidad_credito = 'Credito';
        
        $resultado = Cuota::listar($credito_id);
        $lista = $resultado->get();

        $cabecera = array();
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
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '2');

        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;

        $configuraciones = configuraciones::all()->last();
        $credito = Credito::find($credito_id);
       
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
            return view($this->folderview.'.listdetallecredito')->with(compact('lista','credito', 'paginacion', 'inicio', 'fin', 'entidad_credito','entidad_cuota', 'cabecera','titulo_pagocuota','credito_id','ruta','caja_id'));
        }
        return view($this->folderview.'.listdetallecredito')->with(compact('lista','entidad_credito','entidad_cuota'));
    }
/*************--REPORTE DE CUOTAS PDF--********** */
    public function generareportecuotasPDF($credito_id){   
        $resultado = Cuota::listar($credito_id);
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
        $cuota = Cuota::find($cuota_id);
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

        
        $ahorroactual = DB::table('ahorros')->where('persona_id', $persona->id)->where('fechaf','=',null)->value('capital');
        $titulo ='Voucher-Pago cuota-'.$persona->codigo;
        $view = \View::make('app.credito.recibopagocuotapdf')->with(compact('cuota','credito', 'persona', 'periodocredito','numoperacion', 'cuota_s','cuota_s1', 'cuota_s2'));
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

/*************--RECIBO CREDITO PDF--********** */
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
    /*
    public function abrirpdf(){
        $idcuota =1;
        return view($this->folderview.'.modalvisualisarpdf')->with(compact('idcuota'));
    }*/

}