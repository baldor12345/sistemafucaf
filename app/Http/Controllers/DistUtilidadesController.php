<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\DistribucionUtilidades;
use App\Http\Controllers\Controller;
use App\Configuraciones;
use App\Caja;
use App\Credito;
use App\Ahorros;
use App\Persona;
use App\Transaccion;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;
use PDF;

class DistUtilidadesController extends Controller
{
    protected $folderview      = 'app.distribucionutilidad';
    protected $tituloAdmin     = 'Distribución de Utilidades Anual';
    protected $tituloRegistrar = 'Distribución de utilidades';
    protected $rutas           = array('create' => 'distribucion_utilidades.create', 
            'edit'   => 'distribucion_utilidades.edit', 
            'search' => 'distribucion_utilidades.buscar',
            'reporte' => 'distribucion_utilidades.reporte',
            'index'  => 'distribucion_utilidades.index',
            'verdistribucion' => 'distribucion_utilidades.verdistribucion',
            'reportedistribucionPDF'=>'distribucion_utilidades.reportedistribucionPDF',
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
     * Método para generar combo provincia
     * @param  [type] $departamento_id [description]
     * @return [type]                  [description]
     */

    public function index()
    {
        $configuraciones = Configuraciones::all()->last();
        $entidad = 'Distribucion';
        $title = $this->tituloAdmin;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta = $this->rutas;
        $anioactual = date('Y');
        $anios = array();
        $anioi =2008;
        for($anyo=$anioactual; $anyo >=$anioi;  $anyo --){
            $anios[$anyo] = $anyo;
        }

        return view($this->folderview.'.admin')->with(compact('entidad','configuraciones', 'title', 'tituloRegistrar', 'ruta','anios','anioactual'));
    }

    public function buscar(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = Configuraciones::all()->last();
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $resultado = DistribucionUtilidades::listar("");
        $entidad = 'Distribucion';
        $lista = $resultado->get();
        
        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'TITULO', 'numero' => '1');
        $cabecera[] = array('valor' => 'UTILIDAD DISTRIBUIBLE', 'numero' => '1');
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '3');
        
        $ruta = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion = $paramPaginacion['cadenapaginacion'];
            $inicio = $paramPaginacion['inicio'];
            $fin = $paramPaginacion['fin'];
            $paginaactual = $paramPaginacion['nuevapagina'];
            $lista = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta','idcaja','configuraciones'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function create(Request $request)
    {
        $anio = $request->input('anio');
        $entidad = 'Distribucion';
       /* $ditr = DistribucionUtilidades::where(DB::raw('extract( year from fechai)'),'=',($anio))->get();
        if(count($ditr)<=0){*/
            $caja = Caja::where("estado","=","A")->get();

            


            $idcaja = count($caja) == 0? 0: $caja[0]->id;
            $configuraciones = Configuraciones::all()->last();
            $listar = Libreria::getParam($request->input('listar'), 'NO');
            
            $ruta = $this->rutas;
            $sumUBAcumulado = DistribucionUtilidades::sumUBDacumulado($anio);
            //$anio = date('Y') - 1; 
            $intereses = ($sumUBAcumulado[0]==null)?0:$sumUBAcumulado[0];
            $otros = $sumUBAcumulado[1];
            $gastosDUActual = DistribucionUtilidades::gastosDUactual($anio);

            $int_pag_acum= $gastosDUActual[0];
            $otros_acumulados= $gastosDUActual[1];
            $gastadmacumulado = $gastosDUActual[2];
            
            //$dist_u_anterior = DB::table('transaccion')->where(DB::raw('extract( year from fechai)'),'=',($anio-1))->get();
            
            $dist_u_anterior = DistribucionUtilidades::where(DB::raw('extract( year from fechai)'),'=',($anio-1))->get();
            $du_anterior= (count($dist_u_anterior)>0)?$dist_u_anterior[0]->ub_duactual: 0;
            $gast_du_anterior=(count($dist_u_anterior)>0)?$dist_u_anterior[0]->gastos_duactual: 0;
            $utilidad_neta =round((($intereses + $otros - $du_anterior) - ($gastadmacumulado + $int_pag_acum + $otros_acumulados - $gast_du_anterior )),1);
            $utilidad_dist = round($utilidad_neta - 2*0.1*$utilidad_neta, 1);

            $acciones_mensual=  DistribucionUtilidades::list_total_acciones_mes($anio)->get();
            $numero_acciones_hasta_enero=  DistribucionUtilidades::num_acciones_anio_anterior($anio)->get();
            $acciones_mes  =0;
            $indice1 = 0;
            $j1=12;
            for($i=1; $i<=12; $i++){
                if((($indice1<count($acciones_mensual))?$acciones_mensual[$indice1]->mes:"") == $i){
                    $acciones_mes += $acciones_mensual[$indice1]->cantidad_mes * $j1;
                    $j1--;
                    $indice1++;
                }
            }
            $porcentaje_ditribuible = 100;
            $porcentaje_ditr_faltante = 0;
            $saldo_caja_distribuible = round($this->getSaldoCaja($caja) - $this->getInteresPagado_mesactual($caja->fecha_horaApert) - $this->getGastosAdmin_mesactual($caja->fecha_horaApert), 1);
            if($saldo_caja_distribuible < $utilidad_neta){
                $porcentaje_ditribuible = round(($saldo_caja_distribuible/$utilidad_neta)*100, 2);
                $porcentaje_ditr_faltante  = round(100.00 - $porcentaje_ditribuible, 2);
            }

            $existe = 0;
            $anio_actual=$anio;
            $formData = array('distribucion_utilidades.store');
            $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
            return view($this->folderview.'.mant')->with(compact('existe','intereses','otros','configuraciones','idcaja', 'gastadmacumulado', 'formData', 'entidad','ruta', 'otros_acumulados', 'listar','du_anterior', 'int_pag_acum','utilidad_dist','acciones_mensual','anio','anio_actual','listasocios','gast_du_anterior','acciones_mes','utilidad_neta','numero_acciones_hasta_enero', 'porcentaje_ditribuible','porcentaje_ditr_faltante'));
       /* }else{
            $existe = 1;
            return view($this->folderview.'.mant')->with(compact('existe','entidad'));
        }*/
        
    }

    public function getSaldoCaja($caja){
        $monto_inicio = round($caja->monto_iniciado, 1);
        $egresos=0;
        $ingresos=0;
        $saldo_en_caja =0;
        $saldo = Transaccion::getsaldo($caja->id)->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }
        $saldo_en_caja= round($ingresos,1) + round($monto_inicio,1) - round($egresos, 1);
        return $saldo_en_caja;
    }
    public function getInteresPagado_mesactual($fecha){
        //fecha en mes y año separados
        $fecha = date('Y-m-d', strtotime($fecha));
        $fechaf = explode("-", $fecha);
        $anio =  $fechaf[0];
        $month = $fechaf[1];

        $lista = Caja::listEgresos($month,$anio)->get();
        //calculo del total de egresos del mes actual por persona
        $sum_interes_pagado_mes_actual=0;
        if(count($lista) >0 ){
            for($i=0; $i<count($lista); $i++){
                $sum_interes_pagado_mes_actual += round($lista[$i]->interes_ahorro,1);
            }
        }else{
            $sum_interes_pagado_mes_actual=0;
        }
        return $sum_interes_pagado_mes_actual;
    }
    public function getGastosAdmin_mesactual($fecha){
        $fecha = date('Y-m-d', strtotime($fecha));
        $fechaf = explode("-", $fecha);
        $anio =  $fechaf[0];
        $month = $fechaf[1];
        $lista_por_conceptoAdmin = Caja::listEgresos_por_conceptoAdmin($anio,$month)->get();

        // calculo del total de egresos del mes actual por concepto
        $sum_gasto_administrativo_mes_actual=0;
        if(count($lista_por_conceptoAdmin) >0 ){
            for($i=0; $i<count($lista_por_conceptoAdmin); $i++){
                $sum_gasto_administrativo_mes_actual += $lista_por_conceptoAdmin[$i]->transaccion_monto;
            }
        }
        return $sum_gasto_administrativo_mes_actual;
    }
   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //Metodo para registrar deposito
    public function store(Request $request)
    {
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;

        $error = null;
        $anio = $request->input('anio');
         $ditr = DistribucionUtilidades::where(DB::raw('extract( year from fechai)'),'=',($anio))->get();
        if(count($ditr)<=0){
            if($caja_id >0){
              
                $listar = Libreria::getParam($request->input('listar'), 'NO');
                $error = DB::transaction(function() use($request, $caja_id, $anio){
                $distribucion = new DistribucionUtilidades();
                $distribucion->gast_admin_acum = $this->rouNumber($request->input('gast_ad_acum'), 7);
                $distribucion->int_pag_acum = $this->rouNumber($request->input('int_pag_acum'), 7);
                $distribucion->otros_acum = $this->rouNumber($request->input('otros_acum'), 7);
                $distribucion->ub_duactual = $this->rouNumber($request->input('ub_duactual'), 7);
                $distribucion->titulo = "FINANCIERA UNICA DE CREDITO Y AHORRO FAMILIAR, LAS BRISAS - CHICLAYO: <br> DITRIBUCION DE UTILIDADES EN EL AÑO ".$anio;
                $distribucion->intereses = $this->rouNumber($request->input('intereses'), 7);
                $distribucion->utilidad_distribuible = $request->input('utilidad_distr');
                $distribucion->otros = $this->rouNumber($request->input('otros'), 7);
                $distribucion->gastos_duactual = $this->rouNumber($request->input('gast_duactual'), 7);
                $distribucion->fechai = date($anio.'-01-01');
                $distribucion->fechaf = date($anio.'-12-31');
                $distribucion->save();

                $num_socios = $request->input('numerosocios');
                $caja = Caja::where("estado","=","A")->get()[0];
                for($i=0;$i<$num_socios;$i++){
                    //$caja = Caja::where("estado","=","A")->get()[0];
                    $transaccion = new Transaccion();
                    $transaccion->usuario_id = Credito::idUser();
                    $transaccion->persona_id = $request->input('persona_id'.$i);
                    $transaccion->caja_id = $caja_id;
                    $transaccion->fecha = $caja->fecha_horaApert;
                    $transaccion->concepto_id = 19; // distribucion d eutilidad
                    $transaccion->monto = $this->rouNumber($request->input('monto'.$i), 7);
                    $transaccion->utilidad_distribuida = $this->rouNumber($request->input('monto'.$i), 7);
                    $transaccion->save();
                    if($request->input('ahorrar'.$i) == '1'){
                        $resultado = Ahorros::getahorropersona($request->input('persona_id'.$i));
                    $ahorro=null;
                        if(count($resultado) >0){
                            $ahorro = $resultado[0];
                            $capital = $ahorro->capital + $request->input('monto'.$i);
                            $ahorro->capital = $this->rouNumber($capital, 7);
                            $ahorro->estado = 'P';
                            $ahorro->save();
                        }else{
                            $ahorro = new Ahorros();
                            $ahorro->capital =$this->rouNumber( $request->input('monto'.$i), 7);
                            $ahorro->interes = 0;
                            $ahorro->estado = 'P';
                            $ahorro->fechai = $caja->fecha_horaApert;
                            $ahorro->persona_id = $request->input('persona_id'.$i);
                            $ahorro->save();
                        }

                        $transaccion = new Transaccion();
                        $transaccion->fecha = $caja->fecha_horaApert;
                        $transaccion->monto = $this->rouNumber($request->input('monto'.$i), 7);
                        $transaccion->monto_ahorro= $this->rouNumber($request->input('monto'.$i), 7);
                        $transaccion->id_tabla = $ahorro->id;
                        $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                        $transaccion->concepto_id = 5;
                        $transaccion->persona_id = $request->input('persona_id'.$i);
                        $transaccion->usuario_id = Credito::idUser();
                        $transaccion->caja_id =  $caja->id;
                        $transaccion->save();
                    }
                }
                /***Fondo Social */
                $fsocial = Persona::personas('11111111');
                $resultS = Ahorros::getahorropersona($fsocial[0]->id);
                $ahorroS = null;
                
                if(count($resultS) >0){
                    $ahorroS = $resultS[0];
                    $capital = $ahorroS->capital + $request->input('fsocial');
                    $ahorroS->capital = $this->rouNumber($capital, 7);
                    $ahorroS->estado = 'P';
                    $ahorroS->save();
                }else{
                    $ahorroS = new Ahorros();
                    $ahorroS->capital = $this->rouNumber($request->input('fsocial'), 7);
                    $ahorroS->interes = 0;
                    $ahorroS->estado = 'P';
                    $ahorroS->fechai = $caja->fecha_horaApert;
                    $ahorroS->persona_id = $fsocial[0]->id;
                    $ahorroS->save();
                }
                $transaccionS = new Transaccion();
                $transaccionS->fecha = $caja->fecha_horaApert;
                $transaccionS->monto = $this->rouNumber($request->input('fsocial'), 7);
                $transaccionS->monto_ahorro= $this->rouNumber($request->input('fsocial'), 7);
                $transaccionS->id_tabla = $ahorroS->id;
                $transaccionS->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccionS->concepto_id = 5;
                $transaccionS->persona_id = $fsocial[0]->id;
                $transaccionS->usuario_id = Credito::idUser();
                $transaccionS->caja_id =  $caja->id;
                $transaccionS->save();

                $transaccionF = new Transaccion();
                $transaccionF->usuario_id = Credito::idUser();
                $transaccionF->persona_id = $fsocial[0]->id;
                $transaccionF->caja_id =$caja->id;
                $transaccionF->fecha = $caja->fecha_horaApert;
                $transaccionF->concepto_id = 19; // distribucion d eutilidad
                $transaccionF->monto = $this->rouNumber($request->input('fsocial'), 7);
                $transaccionF->utilidad_distribuida = $this->rouNumber($request->input('fsocial'), 7);
                $transaccionF->save();

                /***Reserva Legal */
                $rlegal = Persona::personas('22222222');
                $resultR = Ahorros::getahorropersona($rlegal[0]->id);
                $ahorroL = null;
                if(count($resultR) >0){
                    $ahorroL = $resultR[0];
                    $capital = $ahorroS->capital + $request->input('rlegal');
                    $ahorroL->capital = $this->rouNumber($capital, 7);
                    $ahorroL->estado = 'P';
                    $ahorroL->save();
                }else{
                    $ahorroL = new Ahorros();
                    $ahorroL->capital = $this->rouNumber($request->input('rlegal'), 7);
                    $ahorroL->interes = 0;
                    $ahorroL->estado = 'P';
                    $ahorroL->fechai = $caja->fecha_horaApert;
                    $ahorroL->persona_id = $rlegal[0]->id;
                    $ahorroL->save();
                }
                $transaccionL = new Transaccion();
                $transaccionL->fecha = $caja->fecha_horaApert;
                $transaccionL->monto = $this->rouNumber($request->input('rlegal'), 7);
                $transaccionL->monto_ahorro= $this->rouNumber($request->input('rlegal'), 7);
                $transaccionL->id_tabla = $ahorroL->id;
                $transaccionL->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccionL->concepto_id = 5;
                $transaccionL->persona_id = $rlegal[0]->id;
                $transaccionL->usuario_id = Credito::idUser();
                $transaccionL->caja_id =  $caja->id;
                $transaccionL->save();

                $transaccionR = new Transaccion();
                $transaccionR->usuario_id = Credito::idUser();
                $transaccionR->persona_id = $rlegal[0]->id;
                $transaccionR->caja_id =$caja->id;
                $transaccionR->fecha = $caja->fecha_horaApert;
                $transaccionR->concepto_id = 19; // distribucion d eutilidad
                $transaccionR->monto = $this->rouNumber($request->input('rlegal'), 7);
                $transaccionR->utilidad_distribuida = $this->rouNumber($request->input('rlegal'), 7);
                $transaccionR->save();

                });
            }else{
                $error = "Caja no aperturada, asegurese de aperturar caja primero !";
            }

        }else{
            $error = "Ya existe una distribucion de utilidades para el año indicado.!";
        }
        return is_null($error) ? "OK" : $error;
    }

    public function verdistribucion($distribucion_id){
        
        $distribucion = DistribucionUtilidades::find($distribucion_id);

        $anio =date('Y',strtotime($distribucion->fechai));
        $entidad = 'Distribucion';
      
            $ruta = $this->rutas;
        
            $intereses =$distribucion->intereses; //($sumUBAcumulado[0]==null)?0:$sumUBAcumulado[0];
            $otros = $distribucion->otros;//$sumUBAcumulado[1];
            $gastosDUActual = $distribucion->gastos_duactual;//DistribucionUtilidades::gastosDUactual($anio);

            $int_pag_acum= $distribucion->int_pag_acum; //$gastosDUActual[0];
            $otros_acumulados=  $distribucion->otros_acum;// $gastosDUActual[1];
            $gastadmacumulado = $distribucion->gast_admin_acum;//$gastosDUActual[2];
         
            $dist_u_anterior = DistribucionUtilidades::where(DB::raw('extract( year from fechai)'),'=',($anio-1))->get();
            $du_anterior= (count($dist_u_anterior)>0)?$dist_u_anterior[0]->ub_duactual: 0;
            $gast_du_anterior=(count($dist_u_anterior)>0)?$dist_u_anterior[0]->gastos_duactual: 0;
            $utilidad_neta =round((($intereses + $otros - $du_anterior) - ($gastadmacumulado + $int_pag_acum + $otros_acumulados - $gast_du_anterior )), 1);
            $utilidad_dist = round($utilidad_neta - 2*0.1*$utilidad_neta, 1);

            $acciones_mensual=  DistribucionUtilidades::list_total_acciones_mes($anio)->get();
            $acciones_mes  =0;
            $indice1 = 0;
            $j1=12;
            for($i=1; $i<=12; $i++){
                if((($indice1<count($acciones_mensual))?$acciones_mensual[$indice1]->mes:"") == $i){
                    $acciones_mes += $acciones_mensual[$indice1]->cantidad_mes * $j1;
                    $j1--;
                    $indice1++;
                }
            }
            $existe = 0;
            $reporte =0;
            $anio_actual=$anio+1;
        return view($this->folderview.'.vistadistribucion')->with(compact('distribucion','reporte','existe','intereses','otros', 'gastadmacumulado', 'entidad','ruta', 'otros_acumulados', 'listar','du_anterior', 'int_pag_acum','utilidad_dist','acciones_mensual','anio','anio_actual','listasocios','gast_du_anterior','acciones_mes','utilidad_neta'));
      
    }

    public function reportedistribucionPDF($distribucion_id=0)
    {   

        $distribucion = DistribucionUtilidades::find($distribucion_id);

        $anio =date('Y',strtotime($distribucion->fechai));
        $entidad = 'Distribucion';
    
        $intereses =$distribucion->intereses; //($sumUBAcumulado[0]==null)?0:$sumUBAcumulado[0];
        $otros = $distribucion->otros;//$sumUBAcumulado[1];
        $gastosDUActual = $distribucion->gastos_duactual;//DistribucionUtilidades::gastosDUactual($anio);

        $int_pag_acum= $distribucion->int_pag_acum; //$gastosDUActual[0];
        $otros_acumulados=  $distribucion->otros_acum;// $gastosDUActual[1];
        $gastadmacumulado = $distribucion->gast_admin_acum;//$gastosDUActual[2];
        
        $dist_u_anterior = DistribucionUtilidades::where(DB::raw('extract( year from fechai)'),'=',($anio-1))->get();
        $du_anterior= (count($dist_u_anterior)>0)?$dist_u_anterior[0]->ub_duactual: 0;
        $gast_du_anterior=(count($dist_u_anterior)>0)?$dist_u_anterior[0]->gastos_duactual: 0;
        $utilidad_neta =round((($intereses + $otros - $du_anterior) - ($gastadmacumulado + $int_pag_acum + $otros_acumulados - $gast_du_anterior )), 1);
        $utilidad_dist = round($utilidad_neta - 2*0.1*$utilidad_neta, 1);

        $acciones_mensual=  DistribucionUtilidades::list_total_acciones_mes($anio)->get();
        $acciones_mes  =0;
        $indice1 = 0;
        $j1=12;
        for($i=1; $i<=12; $i++){
            if((($indice1<count($acciones_mensual))?$acciones_mensual[$indice1]->mes:"") == $i){
                $acciones_mes += $acciones_mensual[$indice1]->cantidad_mes * $j1;
                $j1--;
                $indice1++;
            }
        }
        $existe = 0;
        $reporte =1;
        $anio_actual=$anio+1;


        $j=12;
        $indice=0;
        $sumatotal_acc_mes = 0;
        
        for($i=1; $i<=12; $i++){
            if((($indice<count($acciones_mensual))?$acciones_mensual[$indice]->mes:"") == $i){
                $sumatotal_acc_mes += $acciones_mensual[$indice]->cantidad_mes * $j;
                $j--;
                $indice++;
            }
        }
        $factores_mes=array();
        $f=0;
        $factor = ($sumatotal_acc_mes>0)?$utilidad_dist/$sumatotal_acc_mes: 0;
        for ($i=12; $i >0 ; $i--) { 
            $factores_mes[$f] = $i * $factor;
            $f++;
        }

        $distrib_util = "";
        $socios = Persona::where('tipo','=','SC')->orwhere('tipo','=','S')->get();
        for($i=0; $i< count($socios); $i++){
            
            $listaAcciones = DistribucionUtilidades::list_por_persona($socios[$i]->id, $anio)->get();
            $num_accionesenero = DistribucionUtilidades::list_enero($socios[$i]->id, ($anio-1))->get();
            
            $utilidades = array();
            if(count($listaAcciones)>0){
               $distrib_util = $distrib_util.'<tr><td rowspan="2">'.($i+1).'</td><td rowspan="2" colspan="2">'.$socios[$i]->nombres.' '.$socios[$i]->apellidos.'</td>';
                $l=0;
                $sumtotalAcciones =0;
                for($j=1; $j<=12; $j++){
                    $numaccciones = 0;
                    if($j == 1){
                        $numaccciones = count($num_accionesenero)>0?$num_accionesenero[0]->cantidad_total:0;
                    }
                        
                    if(((($l)<count($listaAcciones))?$listaAcciones[$l]->mes:"") == $j){
                        $numaccciones += $listaAcciones[$l]->cantidad_mes;
                        $distrib_util = $distrib_util."<td>".($numaccciones>0?$numaccciones: '-')."</td>";
                        $utilidades[$j-1] = $factores_mes[$j-1] * $numaccciones;
                        $sumtotalAcciones += $numaccciones;
                        $l++;
                    }else{
                        $distrib_util = $distrib_util."<td>-</td>";
                        $utilidades[$j-1] = 0;
                    }
                }
                $distrib_util = $distrib_util."<td>-</td><td>".(round($sumtotalAcciones,1) > 0? round($sumtotalAcciones,1): '-')."</td></tr><tr>";
                    $sumtotal_util = 0;
                for($j=1; $j<=12; $j++){
                    $distrib_util = $distrib_util."<td>".(round($utilidades[$j-1],1) >0?round($utilidades[$j-1],1): '-')."</td>";
                    $sumtotal_util += $utilidades[$j-1];
                }
                
                $distrib_util = $distrib_util."<td>-</td><td>".round($sumtotal_util,1)."</td></tr>";
            }
        }
        $titulo =$distribucion->titulo;
        $distribucion->titulo = "FINANCIERA UNICA DE CREDITO Y AHORRO FAMILIAR, LAS BRISAS - CHICLAYO: DITRIBUCION DE UTILIDADES EN EL AÑO ";
        $view = \View::make('app.distribucionutilidad.reportedist')->with(compact('distribucion','reporte','existe','intereses','otros', 'gastadmacumulado', 'entidad','ruta', 'otros_acumulados', 'listar','du_anterior', 'int_pag_acum','utilidad_dist','acciones_mensual','anio','anio_actual','listasocios','gast_du_anterior','acciones_mes','utilidad_neta', 'distrib_util'));
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

    public function rouNumber($numero, $decimales) { 
        $factor = pow(10, $decimales); 
        return (round($numero*$factor)/$factor);
    }

}
