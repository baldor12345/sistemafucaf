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
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class AhorrosController extends Controller
{
    protected $folderview = 'app.ahorros';
    protected $tituloAdmin = 'Ahorros';
    protected $tituloRegistrar = 'Registrar ahorro';
    protected $titulo_eliminar = 'Eliminar ahorro';
    protected $titulo_vistadetalleahorro = 'Detalle de ahorros';
    protected $titulo_vistahistoricoahorro = 'Historico de ahorro';
    protected $titulo_vistaretiro  = 'Retirar ahorro';
    protected $rutas = array('create' => 'ahorros.create', 
            'edit' => 'ahorros.edit', 
            'delete' => 'ahorros.eliminar',
            'search' => 'ahorros.buscar',
            'index' => 'ahorros.index',
            'vistaretiro' =>'ahorros.vistaretiro',
            'retiro' => 'ahorros.retiro',
            'vistadetalleahorro'=> 'ahorros.vistadetalleahorro',
            'listardetalleahorro' => 'ahorros.listardetalleahorro',
            'vistahistoricoahorro' => 'ahorros.vistahistoricoahorro',
            'listarhistorico' => 'ahorros.listarhistorico',
            'generareciboahorroPDF' => 'ahorros.generareciboahorroPDF',
            'generareciboahorroPDF1' => 'ahorros.generareciboahorroPDF1',
            'generareciboretiroPDF' => 'ahorros.generareciboretiroPDF',
            
            'actualizarecapitalizacion' => 'ahorros.actualizarecapitalizacion',
            'generareportehistoricoahorrosPDF' => 'ahorros.generareportehistoricoahorrosPDF',
            'listpersonas' => 'creditos.listpersonas'
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
/************************************** ---INICIO----**************************************** */
    public function index()
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = Configuraciones::all()->last();
        $entidad = 'Ahorros';
        $title = $this->tituloAdmin;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta = $this->rutas;
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
        $configuraciones = Configuraciones::all()->last();
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $entidad = 'Ahorros';
        $nombres = Libreria::getParam($request->input('nombres'));
        $resultado = Ahorros::listar($nombres);
        $lista = $resultado->get();
        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[] = array('valor' => 'NOMBRE CLIENTE', 'numero' => '1');
        $cabecera[] = array('valor' => 'CAPITAL AHORRO S/.', 'numero' => '1');
        
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '3');
        $titulo_vistaretiro = $this->titulo_vistaretiro;
        $titulo_vistadetalleahorro = $this->titulo_vistadetalleahorro;
        $titulo_vistahistoricoahorro = $this->titulo_vistahistoricoahorro;
        $ruta             = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion = $paramPaginacion['cadenapaginacion'];
            $inicio = $paramPaginacion['inicio'];
            $fin = $paramPaginacion['fin'];
            $paginaactual = $paramPaginacion['nuevapagina'];
            $lista = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta', 'titulo_vistaretiro','titulo_vistadetalleahorro','titulo_vistahistoricoahorro','idcaja','configuraciones'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

/******************************REGISTRO DE DEPOSITO DE AHORRO******************************** */
  
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
        $configuraciones = Configuraciones::all()->last();

        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'Ahorros';
        $ahorros =  null;
        $dni = null;
        $idopcion = null;
        $ruta = $this->rutas;

        $fecha_pordefecto =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaapert));

        $cboPers = array(0=>'Seleccione...');
        $resultado = Concepto::listar('I');
        $cboConcepto = array(5=>'Deposito de ahorros');// Concepto::pluck('titulo', 'id')->all();
        $formData = array('ahorros.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('ahorros','idcaja','configuraciones','idopcion', 'dni', 'formData', 'entidad','ruta', 'boton', 'listar','cboConcepto','cboPers', 'fecha_pordefecto'));
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
        if($caja_id >0){
            $listar = Libreria::getParam($request->input('listar'), 'NO');
            $reglas = array(
                'capital' => 'required|max:20',
                'fechai' => 'required|max:20',
                'persona_id' => 'required|max:100',
                );

            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }

            $error = DB::transaction(function() use($request, $caja_id){
                $resultado = (new Ahorros())->getahorropersona($request->input('selectpersona'));
                $nuevafecha = $request->input('fechai')." ".date ( 'H:i:s');
                $id_ahorro=null;
                if(count($resultado) >0){
                    $anio_mes = date('Y-m', strtotime($nuevafecha));
                    $ahorro_actual = $resultado[0];
                    $anio_mes2 =  date('Y-m', strtotime($ahorro_actual->fechai));
                   
                    if($anio_mes > $anio_mes2){
                        $ahorro = new Ahorros();
                        $ahorro->capital = $request->input('capital');
                        $ahorro->interes = 0;
                        $ahorro->estado = 'P';
                        $ahorro->fechai = $nuevafecha;
                        $ahorro->persona_id = $request->input('selectpersona');
                        $ahorro->save();
                        $id_ahorro = $ahorro->id;

                        $ahorro_actual->estado = 'C';
                        $ahorro_actual->fechaf = $nuevafecha;
                        $ahorro_actual->save();

                    }else{
                        $capital = $ahorro_actual->capital + $request->input('capital');
                        $ahorro_actual->capital = $capital;
                        $ahorro_actual->estado = 'P';
                        $ahorro_actual->save();
                        $id_ahorro = $ahorro_actual->id;
                    }
                }else{
                    $ahorro = new Ahorros();
                    $ahorro->capital = $request->input('capital');
                    $ahorro->interes = 0;
                    $ahorro->estado = 'P';
                    $ahorro->fechai = $nuevafecha;
                    $ahorro->persona_id = $request->input('selectpersona');
                    $ahorro->save();
                    $id_ahorro = $ahorro->id;
                }
                
                //Guardar en tabla transacciones **********
                $idconcepto = $request->input('concepto');
                $transaccion = new Transaccion();
                $transaccion->fecha = $nuevafecha;
                $transaccion->monto = $request->input('capital');
                $transaccion->monto_ahorro= $request->input('capital');
                $transaccion->id_tabla = $id_ahorro;
                $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccion->concepto_id = $idconcepto;
                $transaccion->persona_id = $request->input('selectpersona');
                $transaccion->usuario_id = (new Ahorros())->idUser();
                $transaccion->caja_id =  $caja_id;
                $transaccion->save();
            });

        }else{
            $error = "Caja no aperturada, asegurese de aperturar caja primero !";
        }
        return is_null($error) ? "OK" : $error;
    }

/************************** MOSTRAR DETALLE (DEPOSITOS O RETIROS) *************************** */
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
        $caja = Caja::where("estado","=","A")->get();
       
        $fecha_pordefecto =count($caja) == 0?  date('Y')."-01-01": date('Y',strtotime($caja[0]->fecha_apert))."-01-01";
        return view($this->folderview.'.detalles_ahorro')->with(compact('ruta','persona', 'entidad','cbotipo','fecha_pordefecto'));
    }
    
   //Metodo para listar detalle depositos o retiros 
   public function listardetalleahorro(Request $request)
   {
       $pagina = $request->input('page');
       $filas = $request->input('filas');
       $tipo = Libreria::getParam($request->input('tipo'));
       $persona_id = Libreria::getParam($request->input('persona_id'));
       $fechainicio = Libreria::getParam($request->input('fechainicio'));
       $persona = Persona::find($persona_id);
       $entidad = "Detalleahorro";
       $resultado = (new Ahorros())->listaretirodeposito($persona_id, $fechainicio, $tipo);
       $lista = $resultado->get();

       $cabecera   = array();
       $cabecera[] = array('valor' => '#', 'numero' => '1');
       $cabecera[] = array('valor' => 'FECHA DEPOSITO.', 'numero' => '1');
       $cabecera[] = array('valor' => 'MONTO DEPOSITO S/.', 'numero' => '1');
       $cabecera[] = array('valor' => 'Operaciones', 'numero' => '2');
       $titulo_eliminar = $this->titulo_eliminar;
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
           return view($this->folderview.'.listdetahorro')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera','ruta', 'tipo','titulo_eliminar','persona'));
       }
       return view($this->folderview.'.listdetahorro')->with(compact('lista', 'entidad'));
   }


/****************** MOSTRAR HISTORICO CAPITAL E INTERES MENSAL EN UN AÑO  ******************* */
   //Metodo para abrir Modal historico de capital + interes 
   public function vistahistoricoahorro ($persona_id, Request $request){
        $ruta = $this->rutas;
        $cboanio = array();
        $anioInicio = 2007;
        $anioactual = date('Y');
        $mesactual = date('m');
        for($anyo=$anioactual; $anyo>=$anioInicio; $anyo --){
            $cboanio[$anyo] = $anyo;
        }

        $titulo_vistahistoricoahorro = $this->titulo_vistahistoricoahorro;
        $entidad = "Detallehistorico";
        $caja = Caja::where("estado","=","A")->get();
        $anio_pordefecto =count($caja) == 0?  date('Y'): date('Y',strtotime($caja[0]->fecha_apert));
        $persona = Persona::find($persona_id);
        return view($this->folderview.'.vistadetallehistorico')->with(compact('ruta','persona', 'entidad','cboanio','titulo_vistahistoricoahorro', 'anio_pordefecto'));
    }

   //Metodo para listar historico
   public function listarhistorico(Request $request){

        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $persona_id = Libreria::getParam($request->input('persona_id'));
        $anio = Libreria::getParam($request->input('cboanio'));
        $entidad = "Detallehistorico";
      
        $resultado =   (new Ahorros())->listarhistorico($persona_id,$anio);
        
        $lista = $resultado->get();
        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'CAPITAL S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'INTERES S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'MES', 'numero' => '1');
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
            return view($this->folderview.'.listdetallehistorico')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta'));
        }
        return view($this->folderview.'.listdetallehistorico')->with(compact('lista', 'entidad'));
    }

/************************************ RETIRAR AHORROS *************************************** */
    //Metodo para abrir modal de retiro
    public function vistaretiro($persona_id, $listarLuego){
        $resultado = (new Ahorros())->getahorropersona($persona_id);
        
        if(count($resultado)>0){
            $ahorro = $resultado[0];
        }else{
            $ahorro = null;
        }
        $persona = Persona::find($persona_id);
        $entidad  = 'Ahorros';
        $ruta = $this->rutas;
        $titulo_vistaretiro = $this->titulo_vistaretiro;

        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        $saldo_en_caja = 0;
        if($caja_id != 0){
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
        }
        
        $fecha_pordefecto =count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaapert));
        return view($this->folderview.'.vistaretirarahorro')->with(compact('ahorro','persona','entidad','entidad', 'ruta','titulo_vistaretiro','saldo_en_caja','caja_id','fecha_pordefecto'));
    }
    //Metodo para registrar el retiro
    public function retiro(Request $request){
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        
        $error = null;
        if($caja_id != 0){
            $ahorro_id = $request->get('ahorro_id');
            $monto_retiro = Libreria::getParam($request->input('montoretiro'));
            $persona_id = Libreria::getParam($request->input('persona_id'));
            
            $error = DB::transaction(function() use($ahorro_id,$monto_retiro, $persona_id,$request, $caja_id){
              
                $fechafin = $request->input('fechar')." ".date('H:i:s');
                $ahorro = Ahorros::find($ahorro_id);
                $capital = $ahorro->capital - $monto_retiro;
               
                if(round($capital,1) <= 0.09){
                    $ahorro->capital = 0;
                }else{
                    $ahorro->capital = $capital;
                }
                $ahorro->save();
            
                $idconcepto = 6;
                $transaccion1 = new Transaccion();
                $transaccion1->fecha = $fechafin;
                $transaccion1->monto = round($monto_retiro,1);
                $transaccion1->monto_ahorro = round($monto_retiro, 1);
                $transaccion1->id_tabla = $ahorro->id;
                $transaccion1->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccion1->concepto_id = $idconcepto;
                $transaccion1->descripcion = "Retiro de S/. ".$monto_retiro." de ahorros";
                $transaccion1->persona_id = $persona_id;
                $transaccion1->usuario_id = Ahorros::idUser();
                $transaccion1->caja_id =  $caja_id;
                $transaccion1->save();
            });
        }else{
            $error = "Caja no aperturada, asegurese de aperturar caja primero !";
        }

        
        return is_null($error) ? "OK" : $error;
    }
    
/*********************************** ACTUALIZA AHORROS ************************************** */
    public function actualizardatosahorros(){
        $config = Configuraciones::all('configuraciones')->last();
        $tasa_interes_ahorro  = $config->tasa_interes_ahorro;
        $lista_ahorros = DB::table('ahorros')->where('fechaf','!=', null)->get();
        $fecha_actual = date('Y-m-d');
        $fecha_hora_actual = date("Y-m-d H:i:s");
        $sfechA = explode('-',$fecha_actual);
        $mesA = $sfech[1];

        $fecha_ah = date("Y-m-d", strtotime($lista_ahorros[0]->fechai));
        $sfechH = explode('-',$fecha_ah);
        $mesH = $sfechH[1];
        $dif_mes = $mesA - $mesH;
        if($dif_mes >0){
            foreach ($lista_ahorros as $key => $value) {
                if($value->id != null){
                    $error = DB::transaction(function() use($value, $fecha_hora_actual, $tasa_interes_ahorro ){
                        $ahorro_ant = Ahorros::find($value->id);
                        $ahorro_ant->fechaf = $fecha_hora_actual;
                        $ahorro_ant-save();
    
                        $ahorro = new Ahorros();
                        $ahorro->fechai = $fecha_hora_actual;
                        $ahorro->capital = $value->capital + $tasa_interes_ahorro * $value->capital;
                        $ahorro->interes = $tasa_interes_ahorro * $value->capital;
                        $ahorro->persona_id = $value->persona_id;
                        $ahorro->save();
                    });
                }
            }
        }
    }


/*************************** GENERAR VOUCHER DEPOSITO AHORRO PDF **************************** */
    //metodo para generar voucher ahorro en pdf
    public function generareciboahorroPDF($transaccion_id = 0)
    {   
        if($transaccion_id == 0){
            $transaccion = Transaccion::all()->last();
        }else{
            $transaccion = Transaccion::find($transaccion_id);
        }
        $persona = Persona::find($transaccion->persona_id);
        $fechaahorro = $transaccion->fecha;
        $fechacreate = $transaccion->created_ad;
        $numoperacion = 01;
        $codcliente = $persona->codigo;
        $nombrecliente = $persona->nombres.' '.$persona->apellidos; 
        $montoahorrado = $transaccion->monto;
        $ahorroactual = DB::table('ahorros')->where('persona_id', $persona->id)->where('fechaf','=',null)->value('capital');
        $titulo ='Voucher-ahorro-'.$persona->codigo;
        $view = \View::make('app.ahorros.reciboAhorro')->with(compact('fechaahorro','fechacreate', 'numoperacion', 'codcliente','nombrecliente', 'montoahorrado','ahorroactual'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        PDF::SetLeftMargin(0);
        PDF::SetRightMargin(0);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

/*************************** GENERAR VOUCHER RETIRO AHORRO PDF **************************** */
    //metodo para generar voucher ahorro en pdf
    public function generareciboretiroPDF($transaccion_id = 0)
    {   
       
        if($transaccion_id == 0){
            $transaccion = Transaccion::all()->last();
        }else{
            $transaccion = Transaccion::find($transaccion_id);
        }
        $persona = Persona::find($transaccion->persona_id);
        $fecharetiro = $transaccion->fecha;
        $numoperacion = 01;
        $codcliente = $persona->codigo;
        $nombrecliente = $persona->nombres.' '.$persona->apellidos; 
        $montoretirado = $transaccion->monto;
        $titulo ='Voucher-retiro-'.$persona->codigo;
        $view = \View::make('app.ahorros.reciboretiroahorro')->with(compact('fecharetiro', 'numoperacion', 'codcliente','nombrecliente', 'montoretirado'));
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
    
/*************************** GENERAR REPORTE HISTORICO DE AHORRO PDF **************************** */
    //metodo para generar voucher ahorro en pdf
    public function generareportehistoricoahorrosPDF($persona_id=0,$anyo)
    {   
        $anio = $anyo;
        $resultado = (new Ahorros())->listarhistorico($persona_id,$anio);
        $lista = $resultado->get();
        $persona = Persona::find($persona_id);

        //$ahorroactual = DB::table('ahorros')->where('persona_id', $persona->id)->where('fechaf','=',null)->value('capital');
        $titulo ='Reporte Historico de ahorros -'.$persona->codigo." - ".$anio;
        $view = \View::make('app.ahorros.reportehistoricoahorros')->with(compact('lista', 'persona', 'anio'));
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


/************************************* OTRAS FUNCIONES ************************************** */
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
            // echo("anio: ".$datofdep1[0]);
            
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
                        ['capital' => $capital, 'interes' =>$interes ,  'fecha_capitalizacion' => $fechacapt, 'ahorros_id' => $ahorro_id]
                    );
                }
            }
        }
        return "Datos actualizados";
    }
    //funcion que devuelve el nombre del mes pasandole numero mes
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
/********************************** Fin otras funciones ************************************* */
    
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
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $ahorros = Ahorros::find($id);
        $ahorros->fecha_deposito = date("Y-m-d", strtotime($ahorros->fecha_deposito));
        $persona = Persona::find($ahorros->persona_id);
        $transaccion = Transaccion::getTransaccion($ahorros->id,'AH');
        $dni = $persona->dni;
        $idopcion = $transaccion[0]->concepto_id;

        $entidad = 'Ahorros';
        $cboConcepto = Concepto::pluck('titulo', 'id')->all();
        $formData = array('ahorros.update', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Modificar';
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
            $listar = Libreria::getParam($request->input('listar'), 'NO');
            $reglas = array(
                'importe' => 'required|max:20',
                'fecha_deposito' => 'required|max:100',
                'interes' => 'required|max:100',
                'persona_id' => 'required|max:100',
                );

            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }
            $error = DB::transaction(function() use($request, $id){
                $ahorros  = Ahorros::find($id);
                $ahorros->importe = $request->input('importe');
                $ahorros->fecha_deposito = $request->input('fecha_deposito');
                
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
            $msjeah = $error;
        }else{
            $msjeah = "Caja no aperturada, aperture primero. !";
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
        $existe = Libreria::verificarExistencia($id, 'transaccion');
        if ($existe !== true) {
            return $existe;
        }
        $error = null;
        $transact = Transaccion::find($id);
        $caja = Caja::find($transact->caja_id);

        if($caja->estado == 'A'){
            $error = DB::transaction(function() use($id, $transact ){
                if($transact->concepto_id == 5 ){
                    $ahorros = Ahorros::find($transact->id_tabla);
                    $ahorros->capital = ($ahorros->capital - $transact->monto);
                    if($ahorros->capital < 0.1){
                        $ahorros->estado = 'P';
                        $ahorros->capital = 0;
                        $ahorros->delete();
                    }
                    //$ahorros->save();
                    
                }else{
                    $ahorros = Ahorros::find($transact->id_tabla);
                    $ahorros->capital = ($ahorros->capital + $transact->monto);
                    $ahorros->save();
                }
                
                $transaccion = Transaccion::find($transact->id);
                $transaccion->delete();
            });   
        
        }else{
            $error = "Error! El registro que intenta eliminar está asociado a una caja actualmente cerrada.";
        }
        
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
        $existe = Libreria::verificarExistencia($id, 'transaccion');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $titulo_eliminar = $this->titulo_eliminar;
        $transaccion = Transaccion::find($id);
        $caja = Caja::find($transaccion->caja_id);

        $modelo = Ahorros::find($id);
        $entidad = 'Ahorros';

        $formData = array('route' => array('ahorros.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Eliminar';
        $mensaje = "¡Error! El registro no se puede eliminar, está asociado a una caja actualmente cerrada.";
        //saldo en caja
        $ingresos =$caja->monto_iniciado;
        $egresos=0;
        $saldo_en_caja =0;
        $saldo = Transaccion::getsaldo($caja->id)->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }
        $saldo_en_caja= $ingresos-$egresos;
        //*************************** */
        $ahorro = Ahorros::where('estado','=', 'P')->get();
        $confirm = true;
        if($caja->estado == 'A'){
            if($transaccion->concepto_id == 5 ){
                if($transaccion->monto > $saldo_en_caja){
                    $confirm=false;
                    $mensaje = "¡Error! El registro no se puede eliminar, debido a que el saldo en caja (S/.: ".round($saldo_en_caja,1).") es menor al monto de ahorro (S/.: ".round($transaccion->monto, 1).") que intenta eliminar.";
                }
            }
        }else{
            $confirm = false;
        }
        // if($transaccion->id_tabla != $ahorro[0]->id){
        //     $confirm=false;
        //     $mensaje = "¡Error! El registro no se puede eliminar, Existen ahorros o retiros mas actuales que modificaron el monto actual.!";
        // }
       
        if($confirm){
            return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'titulo_eliminar'));
        }else{
            return view($this->folderview.'.mensajealerta')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar', 'titulo_eliminar','mensaje'));
        }
        
    }

    public function verahorro($id, Request $request){
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $ruta = $this->rutas;
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $ahorros = Ahorros::find($id);
        $ahorros->fecha_deposito = date("Y-m-d", strtotime($ahorros->fecha_deposito));
        $persona = Persona::find($ahorros->persona_id);
        $titulo_retirar = $this->titulo_retirar;
        $entidad = 'Ahorros';
        $interes_mes = $ahorros->interes;
        $monto_inicial = $ahorros->importe;
        //$montofinal =  pow((100+$interes_mes)/100,$periodo)*$monto_inicial;
        $montofinal =0;// $this->rouNumber($montofinal,2);
        $formData = array('ahorros.retirar', $id);
        $formData = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton = 'Retirar';
        return view($this->folderview.'.detalles_ahorro')->with(compact('ahorros','ruta','montofinal','persona', 'formData', 'entidad', 'boton', 'listar','titulo_retirar'));
    }
}
?>