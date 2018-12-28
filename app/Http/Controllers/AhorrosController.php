<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DateTime;
use App\Http\Requests;
use App\Ahorros;
use App\Detalle_ahorro;
use App\Concepto;
use App\Caja;
use App\Persona;
use App\Transaccion;
use App\Credito;
use App\configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class AhorrosController extends Controller
{
    protected $folderview      = 'app.ahorros';
    protected $tituloAdmin     = 'Ahorros';
    protected $tituloRegistrar = 'Registrar ahorro';
    protected $titulo_vistadetalleahorro = 'Detalle de ahorros';
    protected $titulo_vistahistoricoahorro = 'Historico de ahorro';
    protected $titulo_vistaretiro  = 'Retirar ahorro';
    protected $rutas           = array('create' => 'ahorros.create', 
            'edit'   => 'ahorros.edit', 
            'delete' => 'ahorros.eliminar',
            'search' => 'ahorros.buscar',
            'index'  => 'ahorros.index',
            'vistaretiro' =>'ahorros.vistaretiro',
            'retiro' => 'ahorros.retiro',
            'vistadetalleahorro'=> 'ahorros.vistadetalleahorro',
            'listardetalleahorro' => 'ahorros.listardetalleahorro',
            'vistahistoricoahorro' => 'ahorros.vistahistoricoahorro',
            'listarhistorico' => 'ahorros.listarhistorico',
            'generareciboahorroPDF' => 'ahorros.generareciboahorroPDF',
            'actualizarecapitalizacion' => 'ahorros.actualizarecapitalizacion'
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
/****************---INICIO----******** */
    public function index()
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();
        $entidad          = 'Ahorros';
        $title            = $this->tituloAdmin;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad','idcaja','configuraciones', 'title', 'tituloRegistrar', 'ruta'));
    }
    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    //Lista los ahorros 
    public function buscar(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();
        $pagina    = $request->input('page');
        $filas     = $request->input('filas');
        $entidad   = 'Ahorros';
        $nombres    = Libreria::getParam($request->input('nombres'));
        $resultado  = Ahorros::listar($nombres);
        $lista      = $resultado->get();
        $cabecera   = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'COD. CLIENTE', 'numero' => '1');
        $cabecera[] = array('valor' => 'NOMBRE CLIENTE', 'numero' => '1');
        $cabecera[] = array('valor' => 'CAPITAL AHORRO S/.', 'numero' => '1');
        
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '3');
        $titulo_vistaretiro = $this->titulo_vistaretiro;
        $titulo_vistadetalleahorro = $this->titulo_vistadetalleahorro;
        $titulo_vistahistoricoahorro = $this->titulo_vistahistoricoahorro;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta', 'titulo_vistaretiro','titulo_vistadetalleahorro','titulo_vistahistoricoahorro','idcaja','configuraciones'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }
/****************---Fin inicio---******** */

/*******************REGISTRO DE DEPOSITO DE AHORRO******************************* */
  
   /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     //metodo para abrir Modal registro de nuevo deposito 
    public function create(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Ahorros';
        $ahorros        =  null;
        $dni = null;
        $idopcion = null;
        $ruta             = $this->rutas;
        $resultado      = Concepto::listar('I');
        $cboConcepto  = array(5=>'Deposito de ahorros');// Concepto::pluck('titulo', 'id')->all();
        $formData       = array('ahorros.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('ahorros','idcaja','configuraciones','idopcion', 'dni', 'formData', 'entidad','ruta', 'boton', 'listar','cboConcepto'));
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
        $caja = Caja::where("estado","=","A")->get();
        $msjeah = null;
        $p_id = $request->input('persona_id');
        $t_id = null;

        

        if(count($caja) >0){
            $listar     = Libreria::getParam($request->input('listar'), 'NO');
            $reglas = array(
                'capital'         => 'required|max:20',
                'fechai'      => 'required|max:20',
                'persona_id'    => 'required|max:100',
                );

            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }

            $error = DB::transaction(function() use($request, $caja, $t_id){
                $resultado = Ahorros::getahorropersona($request->input('persona_id'));
                $ahorro = $resultado[0];
                if(count($ahorro) >0){
                    $capital = $ahorro->capital + $request->input('capital');
                    $ahorro->capital = $capital;
                    $ahorro->estado = 'P';
                    $ahorro->save();
                }else{
                    $ahorro = new Ahorros();
                    $ahorro->capital = $request->input('capital');
                    $ahorro->interes = 0;
                    $ahorro->estado = 'P';
                    $ahorro->fechai = $request->input('fechai');
                    $ahorro->persona_id = $request->input('persona_id');
                    $ahorro->save();
                }
                
                //Guardar en tabla transacciones **********
                $fechahora_actual = date("Y-m-d H:i:s");
                $idconcepto = $request->input('concepto');
                $transaccion = new Transaccion();
                $transaccion->fecha = $request->input('fechai');
                $transaccion->monto = $request->input('capital');
                $transaccion->id_tabla = $ahorro->id;
                $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccion->concepto_id = $idconcepto;
                $transaccion->persona_id = $ahorro->persona_id;
                $transaccion->usuario_id = Ahorros::idUser();
                $transaccion->caja_id =  $caja[0]->id;
                $transaccion->save();
                $t_id = $transaccion->id;
                //Guarda el valor de comision voucher en caja
                $transaccion1 = new Transaccion();
                $transaccion1->fecha = $fechahora_actual;
                $transaccion1->monto = 0.10;
                //$transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccion1->concepto_id = 9;//id de concepto comision voucher
                $transaccion1->descripcion = "Impresion de voucher deposito ahorros";
                $transaccion1->persona_id = $ahorro->persona_id;
                $transaccion1->usuario_id = Ahorros::idUser();
                $transaccion1->caja_id =  $caja[0]->id;
                $transaccion1->save();
            });

            $msjeah = $error;
        }else{
            $msjeah = "Caja no aperturada, asegurese de aperturar caja primero !";
        }
        $respuesta=array(is_null($msjeah) ? "OK" : $msjeah, $p_id, $t_id);
        return $respuesta;
    }
/**********************************Fin registro deposito************************************** */

/**********************************MOSTRAR DETALLE (DEPOSITOS O RETIROS) ************************************** */
   //Metodo para  abrir modal detalle (depositos o retiros ) 
   public function vistadetalleahorro($persona_id, Request $request){
        $existe = Libreria::verificarExistencia($persona_id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $ruta = $this->rutas;
        $persona = Persona::find($persona_id);
        $cbotipo = array('I'=>'Depositos','E'=>'Retiros');
        $entidad = "Detalleahorro";
        return view($this->folderview.'.detalles_ahorro')->with(compact('ruta','persona', 'entidad','cbotipo'));
    }
    
   //Metodo para listar detalle depositos o retiros 
   public function listardetalleahorro(Request $request)
   {
       $pagina = $request->input('page');
       $filas  = $request->input('filas');
       $tipo = Libreria::getParam($request->input('tipo'));
       $persona_id = Libreria::getParam($request->input('persona_id'));
       $fechainicio = Libreria::getParam($request->input('fechainicio'));
       
       $entidad = "Detalleahorro";
       $resultado  = Ahorros::listaretirodeposito($persona_id, $fechainicio, $tipo);
       $lista      = $resultado->get();

       $cabecera   = array();
       $cabecera[] = array('valor' => '#', 'numero' => '1');
       $cabecera[] = array('valor' => 'FECHA DEPOSITO.', 'numero' => '1');
       $cabecera[] = array('valor' => 'MONTO DEPOSITO S/.', 'numero' => '1');
       $cabecera[] = array('valor' => 'Operaciones', 'numero' => '1');
       
       $ruta = $this->rutas;
       if (count($lista) > 0) {
           $clsLibreria     = new Libreria();
           $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
           $paginacion      = $paramPaginacion['cadenapaginacion'];
           $inicio          = $paramPaginacion['inicio'];
           $fin             = $paramPaginacion['fin'];
           $paginaactual    = $paramPaginacion['nuevapagina'];
           $lista           = $resultado->paginate($filas);
           $request->replace(array('page' => $paginaactual));
           return view($this->folderview.'.listdetahorro')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera','ruta', 'tipo'));
       }
       return view($this->folderview.'.listdetahorro')->with(compact('lista', 'entidad'));
   }

/**********************************Fin mostrar detalle************************************** */

/**********************************MOSTRAR HISTORICO CAPITAL E INTERES MENSAL EN UN AÑO************************************** */
   //Metodo para abrir Modal historico de capital + interes 
   public function vistahistoricoahorro ($persona_id, Request $request){
        $ruta = $this->rutas;
        $fecha_actual = date('Y-m-d'); 
        $datosfac = explode("-", $fecha_actual);
        $anioactual = $datosfac[0];
        $cboanio = array(''.$anioactual=>''.$anioactual,
        ''.($anioactual-1)=>''.($anioactual-1),
        ''.($anioactual-2)=>''.($anioactual-2),
        ''.($anioactual-3)=>''.($anioactual-3),
        ''.($anioactual-4)=>''.($anioactual-4),
        ''.($anioactual-5)=>''.($anioactual-5),);

        $titulo_vistahistoricoahorro = $this->titulo_vistahistoricoahorro;
        $entidad = "Detallehistorico";
        return view($this->folderview.'.vistadetallehistorico')->with(compact('ruta','persona_id', 'entidad','cboanio','titulo_vistahistoricoahorro'));
    }

   //Metodo para listar historico
   public function listarhistorico(Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $persona_id = Libreria::getParam($request->input('persona_id'));
        $anio = Libreria::getParam($request->input('cboanio'));
        $entidad = "Detallehistorico";
        $resultado = Ahorros::listarhistorico($persona_id,$anio);
        
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'CAPITAL S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'INTERES S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'MES', 'numero' => '1');
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
            return view($this->folderview.'.listdetallehistorico')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta'));
        }
        return view($this->folderview.'.listdetallehistorico')->with(compact('lista', 'entidad'));
    }
/**********************************Fin mostar historico************************************** */

/********************************** RETIRAR AHORROS ************************************** */
    //Metodo para abrir modal de retiro
    public function vistaretiro($persona_id, $listarLuego){
        $resultado = Ahorros::getahorropersona($persona_id);
        $ahorro = $resultado[0];
        $persona = Persona::find($persona_id);
        //$ahorro   = Ahorros::find($id);
        $entidad  = 'Ahorros';
        $ruta = $this->rutas;
        $titulo_vistaretiro = $this->titulo_vistaretiro;
        return view($this->folderview.'.vistaretirarahorro')->with(compact('ahorro','persona','entidad','entidad', 'ruta','titulo_vistaretiro'));
    }
    //Metodo para registrar el retiro
    public function retiro(Request $request){
        $ahorro_id = $request->get('ahorro_id');
        $monto_retiro = Libreria::getParam($request->input('montoretiro'));
        $persona_id = Libreria::getParam($request->input('persona_id'));
        $error = DB::transaction(function() use($ahorro_id,$monto_retiro, $persona_id){
            $fechahora_actual = date("Y-m-d H:i:s");
            $ahorro = Ahorros::find($ahorro_id);
            $capital = $ahorro->capital - $monto_retiro;
            $ahorro->capital = $capital;
            $ahorro->save();

            //Guardar en tabla transacciones **********
            $caja = Caja::where("estado","=","A")->get();
            $idconcepto = 6;
            //Guarda el valor de retiro en caja
            $transaccion = new Transaccion();
            $transaccion->fecha = $fechahora_actual;
            $transaccion->monto = $monto_retiro;
            $transaccion->id_tabla = $ahorro->id;
            $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
            $transaccion->concepto_id = $idconcepto;
            $transaccion->descripcion = "Retiro de S/. ".$monto_retiro." de ahorros";
            $transaccion->persona_id = $persona_id;
            $transaccion->usuario_id = Ahorros::idUser();
            $transaccion->caja_id =  $caja[0]->id;
            $transaccion->save();

            //Guarda el valor de comision voucher en caja
            $transaccion = new Transaccion();
            $transaccion->fecha = $fechahora_actual;
            $transaccion->monto = 0.10;
            //$transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
            $transaccion->concepto_id = 9;//id de concepto comision voucher
            $transaccion->descripcion = "Impresion de voucher retiro ahorros";
            $transaccion->persona_id = $persona_id;
            $transaccion->usuario_id = Ahorros::idUser();
            $transaccion->caja_id =  $caja[0]->id;
            $transaccion->save();
        });
        return is_null($error) ? "OK" : $error;
    }
/**********************************Fin retirar************************************** */
    //Metodo para redondear numero decimal
    public function rouNumber($numero, $decimales) { 
        $factor = pow(10, $decimales); 
        return (round($numero*$factor)/$factor);
    }

    //Metodo para actualizar la la tabla ahorros
    public function actualizarecapitalizacion(){
        $listaahorros = Ahorros::where('estado','P')->get();
        foreach ($listaahorros as $ahorro) {
            $fecha_actual1 = date('Y-m-d'); 
            $fecha_dep1 = date("Y-m-d", strtotime($ahorro->fecha_deposito));
            $datosfac1 = explode("-", $fecha_actual1);
            $datofdep1 = explode("-", $fecha_dep1); 
            echo("anio: ".$datofdep1[0]);
            
            $fechadeposito1 = new DateTime (''.$datofdep1[0].'-'.$datofdep1[1].'-'.$datofdep1[2]);
            $fechafinal1 = new DateTime (''.$datosfac1[0].'-'.$datosfac1[1].'-'.$datosfac1[2]);
            $diferencia1 = $fechadeposito1-> diff($fechafinal1);
            $cantmeses1 = ($diferencia1->y * 12) + $diferencia1->m;
            $capital = $ahorro->importe;
            $interes = 0;
            $fechacapt = $ahorro->fecha_deposito;
            $ahorro_id = $ahorro->id;
            $interesAh = $ahorro->interes;
            for($i=0;$i<$cantmeses1; $i++){
                $interes =  $interesAh/100 * $capital;
                $capital += $interes;
                $fechacapt = date("Y-m-d",strtotime($fechacapt."+ 1 month"));
                $listDet = Detalle_ahorro::where('fecha_capitalizacion','=',$fechacapt )->where('ahorros_id','=', $ahorro_id)->get();
                if(count($listDet)<1){
                    $resp = Detalle_ahorro::updateOrCreate(
                        ['capital' => $capital, 'interes' =>round( $interes , 2, PHP_ROUND_HALF_UP) ,  'fecha_capitalizacion' => $fechacapt, 'ahorros_id' => $ahorro_id]
                    );
                }
            }
        }
        return "Datos actualizados";
    }

    //metodo para generar voucher ahorro en pdf
    public function generareciboahorroPDF($persona_id, $transaccion_id)
    {    
        $transaccion = Transaccion::find($transaccion_id);
        $persona = Persona::find($persona_id);

        $fechaahorro = $transaccion->fecha;
        $numoperacion = 01;
        $codcliente = $persona->codigo;
        $nombrecliente = $persona->nombres.' '.$persona->apellidos; 
        $montoahorrado = $transaccion->monto;
        $ahorroactual = DB::table('ahorros')->where('persona_id', $persona_id)->where('fechaf','=',null)->value('capital');
        $titulo ='Voucher-ahorro-'.$persona->codigo;
        $view = \View::make('app.ahorros.reciboahorro')->with(compact('fechaahorro', 'numoperacion', 'codcliente','nombrecliente', 'montoahorrado','ahorroactual'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(20);
        PDF::SetLeftMargin(40);
        PDF::SetRightMargin(40);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }
/***============================================================================================================================****/
/***============================================================================================================================****/
/***======\\\\\\\\\\\\\\\=====///==========///====\\\\\\\\\\\\\======\\\\\\\\\\\\\\\\=========\\\\\\\\\\\\\\\===================****/
/***======///=================///==========///====///================///==========///=========///===============================****/
/***======///=================///==========///====///================///==========///=========///===============================****/
/***======///=================///==========///====///================///==========///=========///===============================****/
/***======///=================///==========///====///================///==========///=========///===============================****/
/***======\\\\\\\\\\\\\\\=====///==========///====///================///==========///=========\\\\\\\\\\\\\\\===================****/
/***======///=================///==========///====///================\\\\\\\\\\\\\\\\=========///===============================****/
/***======///=================///==========///====///================///==========///=========///===============================****/
/***======///=================///==========///====///================///==========///=========///===============================****/
/***======///=================///==========///====///================///==========///=========///===============================****/
/***======///=================///==========///====///================///==========///=========///===============================****/
/***======///==================\\\\\\\\\\\\\\=====\\\\\\\\\\\\\\=====///==========///=========///===============================****/
/***======///======================\\\\\=============================///==========///=========///===============================****/
/***============================================================================================================================****/
/***============================================================================================================================****/

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNombreMes($NumeroMes){
        $meses = array(
            1 => "Enero",
            2 => "Febrero",
            3 => "Marzo",
            4 => "Abril",
            5 => "Mayo",
            6 => "Junio",
            7 => "Julio",
            8 => "Agosto",
            9 => "Septiembre",
            10 => "Octubre",
            11 => "Noviembre",
            12 => "Diciembre",
        );
        return $meses[$NumeroMes];

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
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $configuraciones = configuraciones::all()->last();
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $ahorros        = Ahorros::find($id);
        $ahorros->fecha_deposito = date("Y-m-d", strtotime($ahorros->fecha_deposito));
        $persona = Persona::find($ahorros->persona_id);
        $transaccion = Transaccion::getTransaccion($ahorros->id,'AH');
        $dni = $persona->dni;
        $idopcion = $transaccion[0]->concepto_id;

        $entidad        = 'Ahorros';
        $cboConcepto  = Concepto::pluck('titulo', 'id')->all();
        $formData       = array('ahorros.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('ahorros','dni','idopcion', 'formData', 'entidad', 'boton', 'listar', 'cboConcepto','configuraciones'));
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
        $caja = Caja::where("estado","=","A")->get();
        $msjeah = null;
        if(count($caja) >0){
            $existe = Libreria::verificarExistencia($id, 'ahorros');
            if ($existe !== true) {
                return $existe;
            }
            $listar     = Libreria::getParam($request->input('listar'), 'NO');
            $reglas = array(
                'importe'         => 'required|max:20',
                'fecha_deposito'      => 'required|max:100',
                'interes'    => 'required|max:100',
                'persona_id'    => 'required|max:100',
                );

            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }
            $error = DB::transaction(function() use($request, $id){
                $ahorros  = Ahorros::find($id);
                $ahorros->importe = $request->input('importe');
                $ahorros->fecha_deposito = $request->input('fecha_deposito');
                //$fecha_fin = date("Y-m-d",strtotime($ahorros->fecha_inicio."+ ".$ahorros->periodo." month"));
            // $ahorros->fecha_fin = $fecha_fin;
                $ahorros->interes = $request->input('interes');
                $ahorros->persona_id = $request->input('persona_id');
                $ahorros->save();
                /// REGISTRO EN CAJA
                $idconcepto = $request->input('concepto');

                $list = Transaccion::getTransaccion($id,'AH');
                $transaccion = Transaccion::find($list[0]->id);
                $transaccion->monto = $ahorros->importe;
                $transaccion->id_tabla = $ahorros->id;
                $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccion->concepto_id = $idconcepto;
                $transaccion->descripcion = "";
                $transaccion->persona_id = $ahorros->persona_id;
                $transaccion->save();
            });
            $msjeah  = $error;
        }else{
            $msjeah  = "Caja no aperturada, aperture primero. !";
        }
        return is_null($msjeah) ? "OK" : $msjeah;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $ahorros = Ahorros::find($id);
            $ahorros->delete();
            $list = Transaccion::getTransaccion($id,'AH');
            $transaccion = Transaccion::find($list[0]->id);
            $transaccion->delete();
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
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Ahorros::find($id);
        $entidad  = 'Ahorros';
        $formData = array('route' => array('ahorros.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }
    public function verahorro($id, Request $request){
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $ruta             = $this->rutas;
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $ahorros        = Ahorros::find($id);
        $ahorros->fecha_deposito = date("Y-m-d", strtotime($ahorros->fecha_deposito));
        $persona = Persona::find($ahorros->persona_id);
        $titulo_retirar = $this->titulo_retirar;
        $entidad        = 'Ahorros';
            $interes_mes = $ahorros->interes;
            $monto_inicial = $ahorros->importe;
        //$montofinal =  pow((100+$interes_mes)/100,$periodo)*$monto_inicial;
        $montofinal =0;// $this->rouNumber($montofinal,2);
        $formData       = array('ahorros.retirar', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Retirar';
        return view($this->folderview.'.detalles_ahorro')->with(compact('ahorros','ruta','montofinal','persona', 'formData', 'entidad', 'boton', 'listar','titulo_retirar'));
    
    }
}
?>