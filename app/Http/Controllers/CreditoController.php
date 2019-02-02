<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Credito;
use App\Persona;
use App\Cuota;
use App\Caja;
use App\Transaccion;
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use PDF;
use DateTime;
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
        'vistaaccion' => 'creditos.vistaaccion',
        'listardetallecuotas' => 'creditos.listardetallecuotas',
        'vistapagocuota' => 'creditos.vistapagocuota',
        'pagarcuota' => 'creditos.pagarcuota',
        'generareportecuotasPDF' => 'creditos.generareportecuotasPDF',
        'generarecibopagocuotaPDF' => 'creditos.generarecibopagocuotaPDF',
        'generarecibocreditoPDF' => 'creditos.generarecibocreditoPDF',
        'abrirpdf' => 'creditos.abrirpdf',
        'listpersonas' => 'creditos.listpersonas',
        'cuotasalafecha' => 'creditos.cuotasalafecha',
        'pagarcuotainteres'=>'creditos.pagarcuotainteres',
        'amortizarcuotas' => 'creditos.amortizarcuotas',
        'obtenermontototal' => 'creditos.obtenermontototal',
        'pagarcreditototal' => 'creditos.pagarcreditototal'
    );

    public function __construct(){
        $this->middleware('auth');
    }

/*************--INICIO--************************* */
    public function index(){
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        //$caja_id = Caja::where("estado","=","A")->value('id');
        //$caja_id = ($caja_id != "")?$caja_id:0;
        $configuraciones = configuraciones::all()->last();
        $entidad = 'Credito';
        $ruta = $this->rutas;
        $title = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboEstado = array(0=>'Pendientes', 1 => 'Cancelados');
        $fecha_pordefecto =count($caja) == 0?  date('Y')."-01-01": date('Y',strtotime($caja[0]->fecha_horaApert))."-01-01";
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboEstado','caja_id','configuraciones','fecha_pordefecto' ));
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
        $cabecera[]  = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]  = array('valor' => 'NOMBRE', 'numero' => '1');
        $cabecera[]  = array('valor' => 'MONTO CRÉDITO S/.', 'numero' => '1');
        $cabecera[]  = array('valor' => 'PERIODO', 'numero' => '1');
        $cabecera[]  = array('valor' => 'ESTADO', 'numero' => '1');
        $cabecera[]  = array('valor' => 'Operaciones', 'numero' => '3');

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

        //$caja_id = Caja::where("estado","=","A")->value('id');
        //$caja_id = ($caja_id != "")?$caja_id:0;
        $configuraciones = Configuraciones::all()->last();
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'Credito';
        $credito = null;
        $ruta = $this->rutas;
        $formData = array('creditos.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Registrar'; 


       // $caja = DB::table('caja')->where('id', $caja_id)->first();
        //calculos
        $ingresos =$caja[0]->monto_iniciado;
        $egresos=0;
        $saldo_en_caja =0;
        $saldo = Transaccion::getsaldo($caja_id)->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }
        $saldo_en_caja= $ingresos-$egresos;
        $cboPers = array(0=>'Seleccione...');
        $fecha_pordefecto =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaApert));
        return view($this->folderview.'.mant')->with(compact('credito', 'formData', 'entidad', 'boton', 'listar', 'configuraciones','caja_id','ruta','saldo_en_caja', 'cboPers','fecha_pordefecto'));
    }
    
/*************--GUARDAR NUEVO CREDITO--************ */
    public function store(Request $request){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $res = null;
        if($caja_id != 0){
            $numCreditos = Credito::where('estado','=','0')->where('persona_id','=', $request->get('selectnom'))->get();
            $valid = true;
            if(count($numCreditos) >= 2){
                $valid=false;
                $res = "El socio o cliente ya cuenta con 2 créditos por lo cual ya no puede obtener más.!";
            }else if(count($numCreditos) == 1){
                if($request->input('periodo') == '1'){
                    $valid=true;
                }else{
                    $valid=false;
                    $res = "El socio o Cliente ya cuenta con un crédito, solo se le permite un crédito mas a una sola cuota.!";
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
                $error = DB::transaction(function() use($request, $caja_id){
                    $configuraciones = Configuraciones::all()->last();
                    $periodo = $request->input('periodo');
                    $fechainicio = $request->input('fechacredito').date(" H:i:s");//**** */
                    $fechafinal = strtotime ( '+'.$periodo.' month' , strtotime ( $fechainicio));
                    $fechafinal = date( 'Y-m-d' , $fechafinal).date(" H:i:s");
                    $valorcredito = $request->get('valor_credito');
                    $descripcion = $request->get('descripcion');
                    $persona_id = $request->input('selectnom');
                    $pers_aval_id = $request->input('selectaval');
                    $tasa_interes = $request->input('tasa_interes');
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
                   
                    if($pers_aval_id != '0'){

                        $credito->pers_aval_id = $pers_aval_id;
                    }

                    $credito->save();
                   
                    $montorestante =  $valorcredito;
                    $valor_cuota =  (($tasa_interes/100) * $valorcredito) / (1 - (pow(1/(1+($tasa_interes/100)), $periodo)));
                    $fecha_actual = $fechainicio;
                    $explod = explode('-',date("Y-m-d",strtotime($fecha_actual)));
                    $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                    $interesAcumulado = 0.00;
                    for($i=0;$i<(int)$periodo; $i++){
                        $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month")); 
                        $montInteres = ($tasa_interes/100) * $montorestante; 
                        $interesAcumulado +=  $montInteres; 
                        $montCapital = ($valor_cuota - $montInteres); 
                        $montorestante = $montorestante - $montCapital;
                        
                        $fecha_p = new DateTime($fechacuota);
                        $fecha_p->modify('last day of this month');
                        $fecha_p->format('Y-m-d');

                        $cuota = new Cuota();
                        $cuota->parte_capital = $this->rouNumber($montCapital , 4); 
                        $cuota->interes = $this->rouNumber($montInteres , 4);
                        $cuota->interes_mora = 0.00;
                        $cuota->saldo_restante =$this->rouNumber($montorestante , 4);
                        $cuota->numero_cuota = $i + 1;
                        $cuota->fecha_programada_pago = $fecha_p;
                        $cuota->estado = '0';//0=PENDIENTE; 1 = PAGADO; 2 = MOROSO
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

/*************-- MODAL ELIMINAR CREDITO--*************** */
    public function eliminar($id, $listarLuego){
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $transaccion_credito = Transaccion::where("id_tabla",'=',$id)->where('inicial_tabla','=','CR')->get();
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
       

        $modelo = Credito::find($id);
        $entidad = 'Credito';
        $formData = array('route' => array('creditos.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
       $boton = "Eliminar";
        if($caja_id == $transaccion_credito[0]->caja_id){
            return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
        }else{
            $mensaje = "¡Error! El registro no se puede eliminar, pertenece a una caja anterior.!";
            return view('app.ahorros.mensajealerta')->with(compact('modelo', 'formData', 'entidad', 'listar','mensaje'));
        }
        
    }

/*************--BORRAR CREDITO--***************** */
    public function destroy($id){
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $cuotas = Cuota::where('credito_id','=',$id)->get();

        $error = DB::transaction(function() use($id, $cuotas){
           /* for($i=0; $i<count($cuotas); $i++){
                $cuotas[$i]->delete();
            }*/
            foreach($cuotas as $cuota){
                $cuota->delete();
            }
            $transaccion_credito = Transaccion::where("id_tabla",'=',$id)->where('inicial_tabla','=','CR')->get()[0];
            $transaccion_credito->delete();
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
    public function vistapagocuota(Request $request, $cuota_id, $listarluego, $entidadr="nan"){
        $numero= $entidadr;
        $entidad_recibo = $entidadr;
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        //$caja_id = Caja::where("estado","=","A")->value('id');
       // $caja_id = ($caja_id != "")?$caja_id:0;
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
        $fechapago =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaApert));
        $boton = 'Registrar'; 
        $ruta = $this->rutas;
        $cuota = Cuota::find($cuota_id);
        $cuota->interes_mora = $interes_moratorio;
        $credito2 = Credito::find($cuota->credito_id);
        if($numero == 2){
            $formData = array('creditos.pagarcuotainteres');
            $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad_cuota, 'autocomplete' => 'off');
            return view($this->folderview.'.pagarcuota')->with(compact('cuota', 'entidad_cuota', 'entidad_credito','entidad_recibo', 'credito','credito2', 'formData','listar','ruta','fechapago'));
        }else{
            $formData = array('creditos.pagarcuota');
            $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad_cuota, 'autocomplete' => 'off');
            return view($this->folderview.'.pagarcuota')->with(compact('cuota', 'entidad_cuota', 'entidad_credito','entidad_recibo', 'credito','credito2', 'formData','listar','ruta','fechapago'));
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
                $comision_voucher = 0.2;

                //Actualiza cuota a estado cancelado
                $cuota = Cuota::find($id_cuota);
                $cuota->estado = 1;
                $cuota->interes_mora = ($valor_mora == null?0:$valor_mora);
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
                $transaccion2->usuario_id = Credito::idUser();
                $transaccion2->caja_id = $caja_id;
                $transaccion2->comision_voucher = $comision_voucher;
                $transaccion2->save();
            
                //registramos en caja el pago cuota
                $monto = $cuota->parte_capital;
                $parte_capital =  $cuota->parte_capital;
                $cuota_interes = 0;
                $cuota_interesMora = 0;
                //if(date('Y-m',strtotime($fecha_pago)) >= date('Y-m', strtotime($cuota->fecha_programada_pago))){
                    $monto += $cuota->interes+ $cuota->interes_mora;
                    $parte_capital = $cuota->parte_capital;
                    $cuota_interes = $cuota->interes;
                    $cuota_interesMora = $cuota->interes_mora;
                //}
                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $fecha_pago;
                $transaccion->monto = $monto;
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = "Pago de Cuota";
                $transaccion->persona_id = $id_cliente;
                $transaccion->usuario_id = Credito::idUser();
                $transaccion->caja_id = $caja_id;
                $transaccion->cuota_parte_capital = $parte_capital;
                $transaccion->cuota_interes = $cuota_interes;
                $transaccion->cuota_mora = $cuota_interesMora ;
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
                $comision_voucher = 0.2;

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
                $transaccion2->usuario_id = Credito::idUser();
                $transaccion2->caja_id = $caja_id;
                $transaccion2->comision_voucher = $comision_voucher;
                $transaccion2->save();
                //registramos en caja el pago cuota
                $monto = $cuota->parte_capital;
                $parte_capital =  $cuota->parte_capital;
               
                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $fecha_pago;
                $transaccion->monto = $cuota->interes;
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = "Pago de interes Cuota";
                $transaccion->persona_id = $id_cliente;
                $transaccion->usuario_id = Credito::idUser();
                $transaccion->caja_id = $caja_id;
                $transaccion->cuota_parte_capital = 0;
                $transaccion->cuota_interes = $cuota->interes;
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
        //$caja_id = Caja::where("estado","=","A")->value('id');
        //$caja_id = ($caja_id != "")?$caja_id:0;

        $configuraciones = configuraciones::all()->last();
        $credito = Credito::find($credito_id);
        $persona = Persona::find($credito->persona_id);
        $entidad_cuota = 'Cuota';
        $entidad_credito = 'Credito';
        $cboacciones = array('1'=>'Pago de cuotas pendientes',//pago de cuota pendiente a la fecha
            '2'=>'Pago de interes (Cuota/Pendiente)',//pagar solo el interes de la cuota pendiente
            '3'=>'Amortizar cuotas',//cancelacion de cuotas para reducir interes y acortar el plazo
            '4'=>'Cancelar todo'
        );
    
           
            $anioInicio = 2007;
            $anioactual = explode('-',date('Y-m-d'))[0];
            $mesactual = explode('-',date('Y-m-d'))[1];
            
            $fecha_actual = date('Y-m-d');
        $fechacaducidad = Date::parse($credito->fechai)->format('Y/m/d');
        $fechacaducidad = date("Y-m-d",strtotime($fechacaducidad."+ ".$credito->periodo." month"));
        $ruta = $this->rutas;
        $fecha_pordefecto =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaApert));
        return view($this->folderview.'.vistaoperacion')->with(compact('credito','anios','meses','anioactual','mesactual','credito_id','cboacciones', 'entidad_cuota','entidad_credito','fechacaducidad','caja_id','configuraciones', 'ruta', 'persona','fecha_actual','fecha_pordefecto'));
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
            $res = Credito::getpersonacredito($persona_id);
            return response()->json($res);
        }
    }

/*************--REFINANCIACIÓN--***************** */
    public function refinanciacion($persona_id, $credito_id){
        $nueva_fecha = $request->input('nueva_fecha');
        $anio = date('m', strtotime($nueva_fecha));
        $mes = date('Y',strtotime($nueva_fecha));
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $monto_amortizacion = $request->input('monto_pago');
        $cuota = Cuota::where(DB::raw('extract( month from fecha_programada_pago)'),'=',$mes)->where(DB::raw('extract( year from fecha_programada_pago)'),'=',$anio)->first();
        $monto_cuota = $cuota->parte_capital + $cuota->interes + $cuota->interes_mora;

        $diferencia = $monto_amortizacion - $monto_cuota;
        
        if(($diferencia<0?-1*$diferencia:$diferencia) < 0.3 ){
            $cuota->estado = '1';
            $cuota->fecha_pago = $nueva_fecha.' '.date('H:i:s');
            $cuota->save();

            $concepto_id_pagocuota = 4;
            $transaccion = new Transaccion();
            $transaccion->fecha = $nueva_fecha.' '.date('H:i:s');
            $transaccion->monto = $monto_cuota;
            $transaccion->concepto_id =  $concepto_id_pagocuota;
            $transaccion->descripcion = "Pago de Cuota";
            $transaccion->persona_id = $persona_id;
            $transaccion->usuario_id = Credito::idUser();
            $transaccion->caja_id = $caja_id;
            $transaccion->cuota_parte_capital = $cuota->parte_capital;
            $transaccion->cuota_interes = $cuota->interes;
            $transaccion->cuota_mora = $cuota->interes_mora;
            $transaccion->save();
        }else{
            if($monto_amortizacion<$monto_cuota){
                $numero_cuota = $cuota->numero_cuota;
                $saldo_restante = $cuota->saldo_restante + $cuota->parte_capital + $cuota->interes + $cuota->interes_mora - $monto_amortizacion;
                $parte_capital_s = $cuota->parte_capital + $cuota->parte_capital + $cuota->interes + $cuota->interes_mora - $monto_amortizacion;

                $cuota->estado = 2;//pago Parcial
                $cuota->fecha_pago = $nueva_fecha.' '.date('H:i:s');
                $cuota->parte_capital = $monto_amortizacion - $cuota->interes - $cuota->monto_mora;
                $cuota->save();

                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $nueva_fecha.' '.date('H:i:s');
                $transaccion->monto = $monto_amortizacion;
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = "Pago parcial de cuota";
                $transaccion->persona_id = $persona_id;
                $transaccion->usuario_id = Credito::idUser();
                $transaccion->caja_id = $caja_id;
                $transaccion->cuota_parte_capital = round($cuota->parte_capital, 4);
                $transaccion->cuota_interes = round($cuota->interes, 4);
                $transaccion->cuota_mora = round($cuota->interes_mora, 4);
                $transaccion->save();

                $credito = Credito::find($cuota->credito_id);

                $numero_cuota +=1;
                if($numero_cuota <= $credito->periodo){
                    $cuota_siguiente = Cuota::where('numero_cuota','=',$numero_cuota)->where('credito_id','=',$cuota->credito_id)->first();
                    $cuota_siguiente->interes = round(0.025 * $saldo_restante, 4);
                    $cuota_siguiente->parte_capital = round($parte_capital_s, 4);
                    $cuota_siguiente->save();
                }else{
                    $cuota_extra = new Cuota();
                    $cuota_extra->credito_id = $credito->id;
                    $cuota_extra->interes = round(0.025 * $saldo_restante,4);
                    $cuota_extra->parte_capital =round( $saldo_restante,4);
                    $fechacuota = date('Y-m', strtotime($cuota->fecha_pago)).'-01';
                    $fecha_p = new DateTime($fechacuota);
                        $fecha_p->modify('last day of this month');
                        $fecha_p->format('Y-m-d');
                    $cuota_extra->fecha_programada_pago = $fecha_p;
                    $cuota_extra->caja_id = $caja_id;
                    $cuota_extra->saldo_restante = 0;
                    $cuota_extra->estado = 0;
                    $cuota_extra->interes_mora = 0;
                    $cuota_extra->numero_cuota = $numero_cuota;
                    $cuota_extra->save();
                }

                
            }else{


            }


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

        //return $cuotas->toJson();
        return response()->json($cuotas);
    }

/*************--AMORTIZAR CUOTAS--************ */
    public function amortizarcuotas(Request $request){
       
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $res = null;
        $transaccion_id = 0;
        if($caja_id != 0){
            $error = DB::transaction(function() use($request, $caja_id){

                $id_credito = $request->get('credito_id');
                $credito = Credito::find($id_credito);
            
                $fecha_pago = $request->get('fechaop').date(" H:i:s");
                $persona = Persona::find((int)$request->get('persona_id'));
                $montoTotal = $request->get('monto_suma');
                $cantidadDatos = $request->get('cantidadmarcados');
                $descripcion ="Amortizacion de las cuotas N°: ";
                $num_cuotaFinal = 0;
                for($i=0; $i<$cantidadDatos; $i++){
                    $cuota = Cuota::find($request->get('cuota_id'.$i));
                    $descripcion = $descripcion."".$cuota->numero_cuota.",";
                    $cuota->estado = '1';// pagado
                    $cuota->fecha_pago = $fecha_pago;
                    $cuota->save();
                    if($cuota->numero_cuota > $num_cuotaFinal ){
                        $num_cuotaFinal = $cuota->numero_cuota;
                    }
                }
                if($num_cuotaFinal == $credito->periodo){
                    $credito->estado = 1;
                    $credito->save();
                }
                $cuotasRestantes = Cuota::where('credito_id', '=', $credito->id)->where('estado','!=','1')->where('deleted_at','=', null)->orderBy('numero_cuota', 'ASC')->get();
                $fecha_actual = $fecha_pago;
                $explod = explode('-',date("Y-m-d",strtotime($fecha_actual)));
                $fechacuota = date($explod[0].'-'.$explod[1].'-01');
                for($j=0; $j<count($cuotasRestantes); $j++){
                    $fechacuota = date("Y-m-d",strtotime($fechacuota."+ 1 month")); 
                    $fecha_p = new DateTime($fechacuota);
                    $fecha_p->modify('last day of this month');
                    $fecha_p->format('Y-m-d');
                    $cuotasRestantes[$j]->fecha_programada_pago = $fecha_p;
                    $cuotasRestantes[$j]->save();
                }
                $comision_voucher = 0.2;

                //registra la comision por voucher en caja si desea imprimirlo
                $concepto_id = 8;
                $transaccion2 = new Transaccion();
                $transaccion2->fecha = $fecha_pago;
                $transaccion2->monto = $comision_voucher;
                $transaccion2->concepto_id = $concepto_id;
                $transaccion2->descripcion ='Comision por Recibo Pago (Amortizacion de cuotas)';
                $transaccion2->persona_id = $persona->id;
                $transaccion2->usuario_id = Credito::idUser();
                $transaccion2->caja_id = $caja_id;
                $transaccion2->comision_voucher = $comision_voucher;
                $transaccion2->save();

                //registramos en caja el valor de cuotas amortizadas
                $id_cuotap = $request->get('cuotap');
                $valor_partecapital =0;
                $interescuota =0;
                
                if($id_cuotap !=0){
                    $cuotap = Cuota::find($id_cuotap);
                    $valor_partecapital = $cuotap->parte_capital;
                    $interescuota = $cuotap->interes;
                }
                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $fecha_pago;
                $transaccion->monto = $montoTotal;
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = $descripcion;
                $transaccion->persona_id = $persona->id;
                $transaccion->usuario_id = Credito::idUser();
                $transaccion->caja_id = $caja_id;
                $transaccion->cuota_parte_capital = $valor_partecapital;
                $transaccion->cuota_interes = $interescuota;
                $transaccion->cuota_mora = 0;
                $transaccion->save();
                $transaccion_id = $transaccion->id;
            });
            $res = $error;
        }else{
            $res = 'Caja no aperturada, asegurece de aperturar primero para registrar alguna transacción.!';
        }
       
        $res =  is_null($res) ? "OK" : $res;
        $respuesta = array($res, $transaccion_id);
        return $respuesta;
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
                $monto_total = $request->get('monto_suma');
                $capital_total = $request->get('capital_total');
                $interes_total = $request->get('interes_total');
                $fecha_pago = $request->get('fechaop');

                $cuotas = Cuota::where('credito_id','=', $credito->id)->where('estado','!=', '1')->where('deleted_at','=', null)->get();
                for($i=0; $i<count($cuotas); $i++){
                    $cuotas[$i]->estado = '1';
                    $cuotas[$i]->save();
                }
                $credito->estado = 1;
                $credito->save();

                $concepto_id_pagocuota = 4;
                $transaccion = new Transaccion();
                $transaccion->fecha = $fecha_pago;
                $transaccion->monto = $monto_total;
                $transaccion->concepto_id =  $concepto_id_pagocuota;
                $transaccion->descripcion = "Cancelado total el credito";
                $transaccion->persona_id = $persona->id;
                $transaccion->usuario_id = Credito::idUser();
                $transaccion->caja_id = $caja_id;
                $transaccion->cuota_parte_capital = $capital_total;
                $transaccion->cuota_interes = $interes_total;
                $transaccion->cuota_mora = 0;
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
        for($i =0; $i<count($cuotas); $i++){
            if(date('Y-m', strtotime($cuotas[$i]->fecha_programada_pago)) <= $anio_mes){
                $valor_total += $cuotas[$i]->parte_capital + $cuotas[$i]->interes;
                $interes_total += $cuotas[$i]->interes;
                $parte_capital_total += $cuotas[$i]->parte_capital ;
            }else{
                $valor_total += $cuotas[$i]->parte_capital;
                $parte_capital_total += $cuotas[$i]->parte_capital ;
            }
        }
        return  array($valor_total, $parte_capital_total, $interes_total);
    }

}