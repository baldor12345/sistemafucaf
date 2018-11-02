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
            'index'    => 'creditos.index',
            'detallecredito'    => 'creditos.detallecredito',
            'guardarcredito'    => 'creditos.guardarcredito',
            
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
        $cabecera[]       = array('valor' => 'Nombre Acreditado', 'numero' => '1');
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
        echo("comfiguracion: ".$configuraciones->tasa_interes_credito);
       
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
        $r = $this->validaAcreditado($acreditado, $request->get('valor_credito'),$configuraciones->precio_accion);
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
                    $valor_cuota =  $this->rouNumber($valor_cuota, 2);
                    $fecha_actual = $credito->fechai;
                    $i = 0;
                    $interesAcumulado = 0.00;
                    for($i=0;$i< (int)$request->get('periodo'); $i++){
                        //sumo 1 mes
                        $fecha_actual = date("Y-m-d",strtotime($fecha_actual."+ 1 month")); 
                        $montInteres = $this->rouNumber($interes *$montorestante , 2); 
                        $interesAcumulado +=  $montInteres; 

                        $montCapital = $this->rouNumber(($valor_cuota - $montInteres) , 2); 
                        $montorestante = $this->rouNumber(($montorestante - $montCapital) , 2);

                        $cuota       = new Cuota();
                        $cuota->parte_capital = $montCapital;
                        $cuota->interes = $montInteres;
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
    public function validaAcreditado($acreditado, $valorCredito, $precioaccion){
        
        $respuesta = "";
        if(trim($acreditado->tipo) == "S"){
            $acciones = DB::table('acciones')
            ->where('persona_id',"=", $acreditado->id)
            ->where('estado',"=", '0')
            ->select(DB::raw('count(*) as numero_acciones'))
            ->get();

            $capital_actual = $acreditado->ingreso_personal + $acreditado->ingreso_familiar + $acciones[0]->numero_acciones * $precioaccion;
            

            if( $valorCredito <= 0.2 * $capital_actual){
                $credito = DB::table('credito')
                ->where('persona_id',"=", $acreditado->id)
                ->where('estado',"=", '0')//0 = credito no cancelado aun
                ->select(DB::raw('count(*) as numcreditos'))
                ->get();

                if($credito[0]->numcreditos >= 2){
                    $respuesta = "El socio supera el numero de creditos ...!";
                }
            }else{
                $respuesta = "El valor de credito que desea solicitar supera el maximo admitido ...!";
            }
            
        }else{
            
            $capital_actual = $acreditado->ingreso_personal + $acreditado->ingreso_familiar ;
           
            if( $valorCredito<= 0.2 * $capital_actual){
               
                $credito = DB::table('credito')
                ->where('persona_id',"=", $acreditado->id)
                ->where('estado',"=", '0')//0 = credito no cancelado aun
                ->select(DB::raw('count(*) as numcreditos'))
                ->get();

                if($credito[0]->numcreditos >= 2){
                    $respuesta = "El cliente supera el numero de creditos ...!";
                }
            }else{
                $respuesta = "El valor de credito que desea solicitar supera el maximo admitido ...!";
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
        $entidad      = 'Credito';
       
        $fechacaducidad = Date::parse($credito->fechai)->format('Y/m/d');
        
        $fechacaducidad = date("Y-m-d",strtotime($fechacaducidad."+ ".$credito->periodo." month"));
        $lista = Cuota::listar($idcredito)->get();
        return view($this->folderview.'.detallecredito')->with(compact('credito', 'entidad', 'lista','fechacaducidad','titulo_detalle','idcaja','configuraciones'));
    }

    public function guardarcredito(Request $request){


         $reglas = array(
            'valor_credito'         => 'required|max:20',
            'periodo'        => 'required|max:5',
            'tasa_interes'      => 'required|max:20',
            'fechacred'            => 'required',
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return "Asegurese de rellenar todos los campos obligatorios (*)";
        }

        $caja = Caja::where("estado","=","A")->get();
        $acreditado = Persona::find($request->get('idcl'));
        $configuraciones = configuraciones::all()->last();
        $r = $this->validaAcreditado($acreditado, $request->get('valor_credito'),$configuraciones->precio_accion);
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
                    $valor_cuota =  $this->rouNumber($valor_cuota, 2);
                    $fecha_actual = $credito->fechai;
                    $i = 0;
                    $interesAcumulado = 0.00;
                    for($i=0;$i< (int)$request->get('periodo'); $i++){
                        //sumo 1 mes
                        $fecha_actual = date("Y-m-d",strtotime($fecha_actual."+ 1 month")); 
                        $montInteres = $this->rouNumber($interes *$montorestante , 2); 
                        $interesAcumulado +=  $montInteres; 

                        $montCapital = $this->rouNumber(($valor_cuota - $montInteres) , 2); 
                        $montorestante = $this->rouNumber(($montorestante - $montCapital) , 2);

                        $cuota       = new Cuota();
                        $cuota->parte_capital = $montCapital;
                        $cuota->interes = $montInteres;
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

}