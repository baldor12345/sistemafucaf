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

class CreditoController extends Controller
{

    protected $folderview      = 'app.credito';
    protected $tituloAdmin     = 'Credito';
    protected $tituloRegistrar = 'Registrar credito';
    protected $tituloModificar = 'Modificar credito';
    protected $tituloEliminar  = 'Eliminar credito';
    protected $titulo_detalle  = 'Detalle de crédito';
    protected $rutas           = array('create' => 'creditos.create', 
            'edit'     => 'creditos.edit', 
            'delete'   => 'creditos.eliminar',
            'search'   => 'creditos.buscar',
            'buscarcuota'   => 'creditos.buscarcuota',
            'index'    => 'creditos.index',
            'pagarcuota'    => 'creditos.pagarcuota',
            'detallecredito'    => 'creditos.detallecredito',
            'guardarcredito'    => 'creditos.guardarcredito',
            'generarecibopagocuotaPDF' => 'creditos.generarecibopagocuotaPDF',
            'generareportecuotasPDF' => 'creditos.generareportecuotasPDF'
            
        );

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function buscar(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Credito';
        $fechai             = Libreria::getParam($request->input('fecha'));
        $estado             = Libreria::getParam($request->input('estado'));
        $nombreAcreditado             = Libreria::getParam($request->input('nombreAcr'));
        $nombreAcreditado = strtoupper($nombreAcreditado);
        $resultado        = Credito::listar($nombreAcreditado,$fechai, $estado);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'CODIGO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto crédito S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Periodo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar','titulo_detalle', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad','titulo_detalle'));
    }

    /** Par listar las cuotas */
    public function buscarcuota(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Cuota';
        $entidad1         = 'Credito';
        $idcredito        = Libreria::getParam($request->input('idcredito'));
        

        $resultado = Cuota::listar($idcredito);
        $lista = $resultado->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => 'Fech. Pag', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Num. Cuota', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto Cuota S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Capital S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Interes', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fech. Real Pag', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Interes Mora', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Mont. Real Cuota s/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Saldo s/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');

        
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();
        $resultado1 = Credito::obtenercredito($idcredito);
        $credito = $resultado1[0];
       
        $fechacaducidad = Date::parse($credito->fechai)->format('Y/m/d');
        $fechacaducidad = date("Y-m-d",strtotime($fechacaducidad."+ ".$credito->periodo." month"));
        $ruta           = $this->rutas;

        
        
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.listdet')->with(compact('lista','credito', 'paginacion', 'inicio', 'fin', 'entidad','entidad1', 'cabecera','titulo_detalle','idcredito','ruta','caja','idcaja'));
        }
        return view($this->folderview.'.listdet')->with(compact('lista','credito', 'paginacion', 'inicio', 'fin', 'entidad','entidad1', 'cabecera','titulo_detalle','idcredito','ruta','caja','idcaja'));
       }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     
    public function index()
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();
        $entidad          = 'Credito';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboEstado        = array(0=>'Pendientes', 1 => 'Cancelados');
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboEstado','idcaja','configuraciones' ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();
        
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $entidad      = 'Credito';
        $credito  = null;
        $formData     = array('creditos.store');
        $formData     = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton        = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('credito', 'formData', 'entidad', 'boton', 'listar'));
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
        $caja = Caja::where("estado","=","A")->get();
        $acreditado = Persona::find($request->get('idcl'));
        $configuraciones = configuraciones::all()->last();
        $r = $this->validaAcreditado($acreditado, $request->get('valor_credito'),$configuraciones->precio_accion,$request->input('periodo'));
        $res = "OK";
       if(count($caja) > 0){//validamos si existe caja aperturada
           if($r == ""){
                $error = DB::transaction(function() use($request){
                    $credito       = new Credito();
                    $configuraciones = configuraciones::all()->last();
                    $credito->valor_credito = $request->get('valor_credito');
                    $credito->periodo = $request->input('periodo');
                    $credito->tasa_interes = $request->input('tasa_interes');
                    $credito->tasa_multa = $configuraciones->tasa_interes_multa;
                    //$fecha =Libreria::getParam($request->input('fecha'));
                    $credito->fechai = $request->input('fechacred');
                    $nuevafecha = strtotime ( '+'.$request->input('periodo').' month' , strtotime ( $credito->fechai ) ) ;
                    $nuevafecha = date ( 'Y-m-d' , $nuevafecha );
                    $credito->fechaf = $nuevafecha;
                    $credito->estado = '0';
                    $credito->descripcion = $request->get('descripcion');
                    $credito->persona_id = $request->get('idcl'); //$request->input('idpersona');
                    $credito->pers_aval_id = $request->get('idavl');
                    $credito->save();

                    $montocredito =  $credito->valor_credito;
                    $montorestante =  $credito->valor_credito;

                    $periodo = $credito->periodo;
                    $interes = $credito->tasa_interes/100;

                    $valor_cuota =  ($interes * $montocredito) / (1 - (pow(1/(1+$interes), $periodo)));
                    $valor_cuota =  $valor_cuota;
                    $fecha_actual = $credito->fechai;
                    $i = 0;
                    $interesAcumulado = 0.00;
                    for($i=0;$i< (int)$request->get('periodo'); $i++){
                        //sumo 1 mes
                        $fecha_actual = date("Y-m-d",strtotime($fecha_actual."+ 1 month")); 
                        $montInteres = $interes *$montorestante; 
                        $interesAcumulado +=  $montInteres; 

                        $montCapital = $valor_cuota - $montInteres; 
                        $montorestante = $montorestante - $montCapital;

                        $cuota       = new Cuota();
                        $cuota->parte_capital =$this->rouNumber($montCapital,2);
                        $cuota->interes = $this->rouNumber($montInteres,2);
                        $cuota->interes_mora = 0;
                        $cuota->saldo_restante = $this->rouNumber($montorestante,2);
                        $cuota->numero_cuota = $i + 1;
                        $cuota->fecha_programada_pago = $fecha_actual;
                        $cuota->estado = '0';//0=PENDIENTE; 1 = PAGADO; 2 = MOROSO
                        $cuota->credito_id = $credito->id;
                        $cuota->save();

                    }
                    $caja = Caja::where("estado","=","A")->get();
                    $fechahora_actual = date('Y-m-d H:i:s');
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $fechahora_actual;
                    $transaccion->monto = $credito->valor_credito;

                    $clso = $request->input('cltipo') == 'S'? "socio":"cliente";
                    $transaccion->concepto_id = 3;//$request->input('concepto');
                    $transaccion->descripcion = " ".$request->input('descripcion').".(Crédito de S/. ".$credito->valor_credito." al ".$clso." ".$request->input('clnombres').")";
                    $transaccion->persona_id = $request->get('idcl');
                    $transaccion->usuario_id = Credito::idUser();
                    $transaccion->caja_id = $caja[0]->id;
                    $transaccion->monto_credito = $credito->valor_credito;
                    $transaccion->save();
                });
                $res = is_null($error) ? "OK" : $error;
            }else{
                $res = $r;
            }
        }else{
           $res = "No hay una caja aperturada, por favor aperture una caja ...!";
        }
       return $res;
    }

    public function rouNumber($numero, $decimales) { 
        $factor = pow(10, $decimales); 
        return (round($numero*$factor)/$factor);
    }
    /**
     * Metodo para validar Cliente acreditado
     */
    public function validaAcreditado($acreditado, $valorCredito, $precioaccion, $periodo){
        
        $respuesta = "";
        if(trim($acreditado->tipo) == "S"){
            $acciones = DB::table('acciones')
            ->where('persona_id',"=", $acreditado->id)
            ->where('estado',"=", '0')
            ->select(DB::raw('count(*) as numero_acciones'))
            ->get();

            $capital_actual = $acreditado->ingreso_personal + $acreditado->ingreso_familiar;
            if( $valorCredito <= (0.2 * $capital_actual + $acciones[0]->numero_acciones * $precioaccion)){// verifica si el valor de credito solicitado no supere el 20% del capital social + valor de acciones
                $credito = DB::table('credito')
                ->where('persona_id',"=", $acreditado->id)
                ->where('estado',"=", '0')//0 = credito no cancelado aun
                ->select(DB::raw('count(*) as numcreditos'))
                ->get();
                if($credito[0]->numcreditos >= 1 & $periodo > 1){
                    $respuesta = "El Socio cuenta con un credito activo, por lo que solo se permite uno mas con periodo de 1 mes !";
                }
                if($credito[0]->numcreditos >= 2){
                    $respuesta = "El Socio ya cuenta con 2 creditos, no se permite mas de 2 !";
                }
            }else{
                $respuesta = "El Socio solo puede obtener un credito con un monto máximo de s/. ".((0.2 * $capital_actual ) + ($acciones[0]->numero_acciones * $precioaccion))." !";
            }
        }else{
            $capital_actual = $acreditado->ingreso_personal + $acreditado->ingreso_familiar ;
            if( $valorCredito<= 0.2 * $capital_actual){
                $credito = DB::table('credito')
                ->where('persona_id',"=", $acreditado->id)
                ->where('estado',"=", '0')//0 = credito no cancelado aun
                ->select(DB::raw('count(*) as numcreditos'))
                ->get();
                if($credito[0]->numcreditos >= 1 & $periodo > 1){
                    $respuesta = "El Cliente cuenta con un credito activo, por lo que solo se permite uno mas con periodo de 1 mes !";
                }
                if($credito[0]->numcreditos >= 2){
                    $respuesta = "El Cliente ya cuenta con 2 creditos, no se permite mas de 2 !";
                }
            }else{
                $respuesta = "El Cliente solo puede obtener un credito con un monto máximo de s/. ".(0.2*$capital_actual)." !";
            }
        }
        return $respuesta;
    }

    public function detallecredito(Request $request, $idcredito){
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();

        $resultado = Credito::obtenercredito($idcredito);
        $credito = $resultado[0];
        $entidad      = 'Cuota';
        $entidad1 = 'Credito';
       
        $fechacaducidad = Date::parse($credito->fechai)->format('Y/m/d');
        $fechacaducidad = date("Y-m-d",strtotime($fechacaducidad."+ ".$credito->periodo." month"));
        $ruta           = $this->rutas;
        return view($this->folderview.'.detallecredito')->with(compact('credito','idcredito', 'entidad','entidad1', 'lista','fechacaducidad','titulo_detalle','idcaja','configuraciones', 'ruta'));
    }

    public function guardarcredito(Request $request){


         $reglas = array(
            'valor_credito'         => 'required|max:20',
            'periodo'        => 'required|max:5',
            'tasa_interes'      => 'required|max:20',
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return "Asegurese de rellenar todos los campos obligatorios (*)";
        }

        $caja = Caja::where("estado","=","A")->get();
        $acreditado = Persona::find($request->get('idcl'));
        $configuraciones = configuraciones::all()->last();
        $r = $this->validaAcreditado($acreditado, $request->get('valor_credito'),$configuraciones->precio_accion, $request->input('periodo'));
        $res = "OK";
       if(count($caja) > 0){
           if($r == ""){
                $error = DB::transaction(function() use($request){
                    $credito       = new Credito();
                    $configuraciones = configuraciones::all()->last();
                    $credito->valor_credito = $request->get('valor_credito');
                    $credito->periodo = $request->input('periodo');
                    $credito->tasa_interes = $request->input('tasa_interes');
                    $credito->tasa_multa = $configuraciones->tasa_interes_multa;
                    //$fecha =Libreria::getParam($request->input('fecha'));
                    $credito->fechai = $request->input('fechacred').date(" H:i:s");//**** */
                    $nuevafecha = strtotime ( '+'.$request->input('periodo').' month' , strtotime ( $credito->fechai ) ) ;
                    $nuevafecha = date( 'Y-m-d' , $nuevafecha );
                    $credito->fechaf = $nuevafecha;
                    $credito->estado = '0';
                    $credito->descripcion = $request->get('descripcion');
                    $credito->persona_id = $request->get('idcl'); //$request->input('idpersona');
                    $credito->pers_aval_id = $request->get('idavl');
                    $credito->save();

                    $montocredito =  $credito->valor_credito;
                    $montorestante =  $credito->valor_credito;

                    $periodo = $credito->periodo;
                    $interes = $credito->tasa_interes/100;

                    $valor_cuota =  ($interes * $montocredito) / (1 - (pow(1/(1+$interes), $periodo)));
                    
                    $fecha_actual = $credito->fechai;
                    $i = 0;
                    $interesAcumulado = 0.00;
                    for($i=0;$i< (int)$request->get('periodo'); $i++){
                        //sumo 1 mes
                        $fecha_actual = date("Y-m-d",strtotime($fecha_actual."+ 1 month")); 
                        $montInteres = $interes *$montorestante; 
                        $interesAcumulado +=  $montInteres; 
                        $montCapital =$valor_cuota - $montInteres; 
                        $montorestante = $montorestante - $montCapital;
                        $cuota = new Cuota();
                        $cuota->parte_capital = $this->rouNumber($montCapital , 1); 
                        $cuota->interes = $this->rouNumber($montInteres , 1);
                        $cuota->interes_mora = 0.00;
                        $cuota->saldo_restante =$this->rouNumber(($montorestante) , 1);
                        $cuota->numero_cuota = $i + 1;
                        $cuota->fecha_programada_pago = $fecha_actual;
                        $cuota->estado = '0';//0=PENDIENTE; 1 = PAGADO; 2 = MOROSO
                        $cuota->credito_id = $credito->id;
                        $cuota->save();

                    }



                    $caja = Caja::where("estado","=","A")->get();
                    //$fechahora_actual = date('Y-m-d H:i:s');
//comision voucher
                    $transaccion2 = new Transaccion();
                    $transaccion2->fecha = $request->input('fechacred').date(" H:i:s");;
                    $transaccion2->monto = 0.1;
                    $transaccion2->concepto_id = 8;
                    $transaccion2->descripcion ='Comision por recibo credito';
                    $transaccion2->persona_id = $request->get('idcl');
                    $transaccion2->usuario_id = Credito::idUser();
                    $transaccion2->caja_id = $caja[0]->id;
                    $transaccion2->comision_voucher = 0.1;
                    $transaccion2->save();


//registro credito
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $request->input('fechacred').date(" H:i:s");;
                    $transaccion->monto = $credito->valor_credito;

                    $clso = $request->input('cltipo') == 'S'? "socio":"cliente";
                    $transaccion->concepto_id = 3;//$request->input('concepto');
                    $transaccion->descripcion = " ".$request->input('descripcion').".(Crédito de S/. ".$credito->valor_credito." al ".$clso." ".$request->input('clnombres').")";
                    $transaccion->persona_id = $request->get('idcl');
                    $transaccion->usuario_id = Credito::idUser();
                    $transaccion->caja_id = $caja[0]->id;
                    $transaccion->monto_credito = $credito->valor_credito;
                    
                    $transaccion->save();
                });
                $res = is_null($error) ? "OK" : $error;
            }else{
                $res = $r;
            }
        }else{
           $res = "No hay una caja aperturada, por favor aperture una caja ...!";
        }
       return $res;
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
    public function edit(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $listar   = Libreria::getParam($request->input('listar'), 'NO');
        $credito = Credito::find($id);
        $entidad  = 'Credito';
        $formData = array('credito.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('credito', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($request, $id){
            $credito       = Credito::find($id);
            
            $credito->save();
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

    /**
     * Función para confirmar la eliminación de un registrlo
     * @param  integer $id          id del registro a intentar eliminar
     * @param  string $listarLuego consultar si luego de eliminar se listará
     * @return html              se retorna html, con la ventana de confirmar eliminar
     */
    public function eliminar($id, $listarLuego)
    {
        $existe = Libreria::verificarExistencia($id, 'credito');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Credito::find($id);
        $entidad  = 'Credito';
        $formData = array('route' => array('credito.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
//***************************************************************************************** */
    public static function pagarcuota(Request $request){
        $id_cuota = $request->get('id_cuota');
        $caja = Caja::where("estado","=","A")->get();
        $res = null;
        if(count($caja) > 0){// VALIDA SI CAJA ESTA APERTURADA
            $error = DB::transaction(function() use($request, $id_cuota){
                $fechahora_actual = date('Y-m-d H:i:s');
                $credito = Credito::find((int)$request->get('id_crd'));
                $cuota   = Cuota::find($id_cuota);
                $cuota->estado = 1;
                $cuota->fecha_pago = $fechahora_actual;
                $cuota->save();
//comision por voucher
                $transaccion2 = new Transaccion();
                $transaccion2->fecha = $fechahora_actual;
                $transaccion2->monto = 0.2;
                $transaccion2->concepto_id = 8;
                $transaccion2->descripcion ='Comision por recibo cuota';
                $transaccion2->persona_id = $request->get('id_cliente');
                $transaccion2->usuario_id = Credito::idUser();
                $transaccion2->caja_id = $request->get('id_caja');
                $transaccion2->comision_voucher = 0.2;
                $transaccion2->save();

//pago cuota
                $transaccion = new Transaccion();
                $transaccion->fecha = $fechahora_actual;
                $transaccion->monto = $cuota->parte_capital + $cuota->interes+ $cuota->interes_mora;
                $transaccion->concepto_id = 4;//$request->input('concepto');
                $transaccion->descripcion = " Pago de cuota N° ".$cuota->numero_cuota."/".$credito->periodo." de S/. ".$transaccion->monto;
                $transaccion->persona_id = $request->get('id_cliente');
                $transaccion->usuario_id = Credito::idUser();
                $transaccion->caja_id = $request->get('id_caja');
                $transaccion->cuota_parte_capital = $cuota->parte_capital;
                $transaccion->cuota_interes = $cuota->interes;
                $transaccion->cuota_mora = $cuota->interes_mora;
                $transaccion->save();

                
                
                if($credito->periodo == $cuota->numero_cuota){// valida si se cancela totalmente todas las cuotas y modifica el estado del credito
                    $credito->estado = 1;// estado : 1 = Cancelado totalmente
                    $credito->save();
                }

            });
            $res = $error;
        }else{
            $res = "No hay una caja aperturada, por favor aperture una caja ...!";
        }
        return is_null($res) ? "OK" : $res;
    }

    //listar el objeto persona por dni
    public function getPersona(Request $request, $dni){
        
        if($request->ajax()){
            $personas = Persona::personas($dni);
            return response()->json($personas);
        }
    }

/*************************** GENERAR VOUCHER DEPOSITO AHORRO PDF **************************** */
    //metodo para generar voucher ahorro en pdf
    public function generarecibopagocuotaPDF($cuota_id)
    {   
        $cuota = Cuota::find($cuota_id);
        $credito = Credito::find($cuota->credito_id);
        $persona = Persona::find($credito->persona_id);
        $periodocredito = $credito->periodo;
        $numoperacion = 00;
        $cuota_s = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 1))->first();
        $cuota_s1 = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 2))->first();
        $cuota_s2 = Cuota::where('credito_id','=',$credito->id)->where('numero_cuota','=',($cuota->numero_cuota + 3))->first();

        $ahorroactual = DB::table('ahorros')->where('persona_id', $persona->id)->where('fechaf','=',null)->value('capital');
        $titulo ='Voucher-Pago cuota-'.$persona->codigo;
        $view = \View::make('app.credito.recibopagocuota')->with(compact('cuota','credito', 'persona', 'periodocredito','numoperacion', 'cuota_s','cuota_s1', 'cuota_s2'));
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
/************************************ Fin generar voucher *********************************** */

/*************************** GENERAR REPORTE CUOTAS PDF **************************** */
    //metodo para generar reporte de cuotas en pdf
    public function generareportecuotasPDF($credito_id)
    {   
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
/************************************ Fin generar reporte *********************************** */
}