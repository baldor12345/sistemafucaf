<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Acciones;
use App\Persona;
use App\Caja;
use App\Concepto;
use App\Transaccion;
use App\HistorialAccion;
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use PDF;

class AccionesController extends Controller
{
    protected $folderview      = 'app.acciones';
    protected $tituloAdmin     = 'Acciones';
    protected $tituloRegistrar = 'Compra de Acciones';
    protected $tituloModificar = 'Modificar acciones';
    protected $tituloDetalle = 'Detalle de Acciones';
    protected $tituloVenta = 'Venta de Acciones';
    protected $tituloEliminar  = 'Eliminar acciones';
    protected $rutas           = array('create' => 'acciones.create', 
            'edit'   => 'acciones.edit', 
            'listacciones' => 'acciones.listacciones',
            'delete' => 'acciones.eliminar',
            'search' => 'acciones.buscar',
            'cargarventa'   => 'acciones.cargarventa',
            'updateventa'   => 'acciones.updateventa',
            'reciboaccionpdf' => 'acciones.reciboaccionpdf',
            'reciboaccionventapdf' => 'acciones.reciboaccionventapdf',
            'index'  => 'acciones.index',
            'listpersonas' => 'acciones.listpersonas',
            'buscaraccion'=> 'acciones.buscaraccion'
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
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Acciones';
        $codigo             = Libreria::getParam($request->input('codigo'));
        $nombres             = Libreria::getParam($request->input('nombres'));
        $dni             = Libreria::getParam($request->input('dni'));
        $resultado        = Acciones::listar($codigo, $nombres, $dni);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'NOMBRES', 'numero' => '1');
        $cabecera[]       = array('valor' => 'ACCIONES', 'numero' => '1');
        $cabecera[]       = array('valor' => 'PRECIO ACCION', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '2');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_ventaaccion = $this->tituloVenta;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_ventaaccion', 'ruta','idcaja'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad','idcaja'));
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
        $entidad          = 'Acciones';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','idcaja'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $cboPersona = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboConfiguraciones = Configuraciones::pluck('precio_accion', 'id')->all();
        $cboConcepto  =array(1=>'Compra de Acciones');
        $cboContribucion  =array(11=>'Contribución de Ingreso');
        $entidad        = 'Acciones';
        $acciones        = null;
        $ruta = $this->rutas;
        $cboPers = array(0=>'Seleccione...');
        $formData       = array('acciones.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Comprar Acciones'; 
        return view($this->folderview.'.mant')->with(compact('acciones', 'formData', 'entidad', 'boton', 'listar','cboConfiguraciones','cboConcepto','ruta','cboContribucion','cboPers'));
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
        
        
        //evaluando los datos que vienen del view
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;

        $res = null;
        if(count($caja_id) != 0){//validamos si existe caja aperturada
            $reglas = array(
                'selectnom'        => 'required',
                'cantidad_accion'        => 'required|max:100',
                'fechai'        => 'required|max:100',
                'configuraciones_id'        => 'required|max:100'
            );

            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }

            $error = DB::transaction(function() use($request){
                $idCaja = DB::table('caja')->where('estado', "A")->value('id');
                $cantidad_accion= $request->input('cantidad_accion');
                if($cantidad_accion !== ''){

                    $can_moment = DB::table('acciones')->where('estado','C')->count();
                    $codigo = (count($can_moment) ==0)?0:$can_moment;
                    for($i=0; $i< $cantidad_accion; $i++){
                        $codigo++;
                        $acciones               = new Acciones();    
                        $acciones->estado        = 'C';
                        $acciones->fechai        = $request->input('fechai').date(" H:i:s");
                        if(strlen($codigo) == 1){
                            $acciones->codigo = "000000".($codigo);
                        }
                        if(strlen($codigo) == 2){
                            $acciones->codigo = "00000".($codigo);
                        }
                        if(strlen($codigo) == 3){
                            $acciones->codigo = "0000".($codigo);
                        }
                        if(strlen($codigo) == 4){
                            $acciones->codigo = "000".($codigo);
                        }
                        $acciones->descripcion        = $request->input('descripcion');
                        $acciones->persona_id        = $request->input('selectnom');
                        $acciones->configuraciones_id        = $request->input('configuraciones_id');
                        $acciones->caja_id = $idCaja;
                        $acciones->concepto_id        = $request->input('concepto_id');
                        $acciones->save();
                    }
                }

                if($cantidad_accion !== ''){
                    $historial_accion               = new HistorialAccion();    
                    $historial_accion->cantidad        =  $request->input('cantidad_accion');
                    $historial_accion->estado        = 'C';
                    $historial_accion->fecha        = $request->input('fechai');
                    $historial_accion->descripcion        = $request->input('descripcion');
                    $historial_accion->persona_id        = $request->input('selectnom');
                    $historial_accion->configuraciones_id        = $request->input('configuraciones_id');
                    $historial_accion->caja_id = $idCaja;
                    $historial_accion->concepto_id        = $request->input('concepto_id');
                    $historial_accion->save();
                    
                }

                $cantidad_accion = $request->input('cantidad_accion');
                $idCaja = DB::table('caja')->where('estado', "A")->value('id');
                $configuracion= Configuraciones::all();
                $result= $configuracion->last();
                $precio = $result->precio_accion;
                $monto_ingreso = ($cantidad_accion*$precio);
                //datos de la persona
                $persona_nombre = DB::table('persona')->where('id', $request->input('selectnom'))->value('nombres');

                //comision voucher si esque desea imprimirlo
                $transaccion = new Transaccion();
                $transaccion->fecha = $request->input('fechai').date(" H:i:s");
                $transaccion->monto = $monto_ingreso;
                $transaccion->acciones_soles = $monto_ingreso;
                $transaccion->concepto_id = $request->input('concepto_id');
                $transaccion->descripcion = " compro ".$cantidad_accion." acciones";
                $transaccion->persona_id = $request->input('selectnom');
                $transaccion->inicial_tabla ="AC";
                $transaccion->usuario_id =Caja::getIdPersona();
                $transaccion->caja_id =$idCaja;
                $transaccion->save();

                $contribucion = $request->input('monto');
                if($contribucion != ''){
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $request->input('fechai').date(" H:i:s");
                    $transaccion->monto = $contribucion;
                    $transaccion->concepto_id = $request->input('contribucion_id');
                    $transaccion->descripcion = "aporte como nuevo socio";
                    $transaccion->usuario_id =Caja::getIdPersona();
                    $transaccion->inicial_tabla ="AC";
                    $transaccion->caja_id =$idCaja;
                    $transaccion->save();
                }
                


            });
            $ultima_accion = Acciones::all()->last();
            $cant = $request->input('cantidad_accion');
            $fecha = $request->input('fechai').date(" H:i:s");
            $res = $error;
        }else{
            $res = "No hay una caja aperturada, por favor aperture una caja ...!";
        }
        $ultima_accion = Acciones::all()->last();
        $res = is_null($res) ? "OK" : $res;
        $respuesta = array($res, $ultima_accion->persona_id, $cant, $fecha);
        return $respuesta;
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'acciones');
        if ($existe !== true) {
            return $existe;
        }

        $listAcciones        = Acciones::listAcciones($id);
        $listAcc           = $listAcciones->get();

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $cboEstado        = [''=>'Seleccione']+ array('C'=>'Compra','V'=>'Venta' );
        $cboPersona = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboConfiguraciones = array('' => 'Seleccione') + Configuraciones::pluck('precio_accion', 'id')->all();
        $acciones        = Acciones::find($id);
        $entidad        = 'Acciones';
        $formData       = array('acciones.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('acciones', 'formData', 'entidad', 'boton', 'listar','listAcc', 'cboEstado','cboPersona','cboConfiguraciones'));
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
        $existe = Libreria::verificarExistencia($id, 'acciones');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'persona_id'       => 'required|max:20|unique:acciones,persona_id,'.$id.',id,deleted_at,NULL',
            'configuraciones_id'       => 'required|max:20|unique:acciones,configuraciones_id,'.$id.',id,deleted_at,NULL',
            'cadenaAcciones'      => 'required|max:100'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            
            if($request->input('cadenaAcciones') !==''){
                $valores = explode(",", $request->input('cadenaAcciones'));
                for( $i=0; $i< count($valores); $i++){
                    $accion=  explode(":", $valores[$i]);
                    for( $j=1; $j< $accion[0]+1; $j++){
                        $acciones                 = Acciones::find($id);  
                        $acciones->estado        = $accion[1];
                        $acciones->fecha        = $accion[2];
                        $acciones->persona_id        = $request->input('persona_id');
                        $acciones->configuraciones_id        = $request->input('configuraciones_id');
                        
                        $acciones->save();
                    }
                }
            }
        });
        return is_null($error) ? "OK" : $error;
    }

    public function listacciones($persona_id, Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;

        $entidad = "Accion1";
        $titulo_detalle = $this->tituloDetalle;
        $ruta             = $this->rutas;
        $inicio           = 0;
    
        return view($this->folderview.'.detalle')->with(compact('lista', 'entidad', 'persona_id', 'ruta','idcaja','titulo_detalle'));
    }

    public function buscaraccion(Request $request){
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad ='Accion1';
        $persona_id      = Libreria::getParam($request->input('persona_id'));
        $resultado        = Acciones::listAcciones($persona_id);
        $lista            = $resultado->get();
     

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripcion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Voucher', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '1');
      
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $inicio           = 0;

        $Month = array(1=>'Enero',
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
            return view($this->folderview.'.listdetalle')->with(compact('concepto_id','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio','Month','titulo_eliminar'));
        }
        return view($this->folderview.'.listdetalle')->with(compact('concepto_id','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio', 'Month','titulo_eliminar'));
    
    }




    //listar el objeto persona por dni
    public function getPersona(Request $request, $dni){
        
        if($request->ajax()){
            $personas = Persona::personas($dni);
            return response()->json($personas);
        }
    }

    //listar la cantidad de acciones acumuladas 
    public function getListCantAcciones(Request $request, $persona_id){
        if($request->ajax()){
            $CantAcciones = Acciones::cant_acciones_acumuladas($persona_id);
            return response()->json($CantAcciones);
        }
    }

    //listar la cantidad de acciones acumuladas por persona
    public function getListCantAccionesPersona(Request $request, $persona_id, $n){
        $id_persona = intval($persona_id);
        if($request->ajax()){
            $CantAcciones = DB::table('acciones')->where('persona_id',$id_persona)->where('estado', 'C')->count();
            return response()->json($CantAcciones);
        }
    }

    //venta de acciones
    public function cargarventa($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $cant_acciones = DB::table('acciones')->where('estado','C')->where('persona_id',$id)->count();

        $listar = "NO";
        $ruta = $this->rutas;
        $persona = Persona::find($id);
        if (isset($listarParam)) {
            $listar = $listarParam;
        }
        $cboPers = array(0=>'Seleccione...');
        $nom = '  dni: '.$persona->dni.'   nom: '.$persona->nombres.' '.$persona->apellidos;
        $cboConfiguraciones = Configuraciones::pluck('precio_accion', 'id')->all();
        $cboConcepto  =array(2=>'Venta de Acciones');
        $acciones        = Acciones::find($id);
        $entidad        = 'Acciones';

        $boton          = 'Vender Acciones';
        return view($this->folderview.'.venderaccion')->with(compact('acciones','persona', 'entidad', 'boton', 'listar','cboConfiguraciones','cboConcepto','ruta','nom','cant_acciones','cboPers'));
    }

    public function guardarventa(Request $request, $id)
    {
        $idpropietario= $request->input('idpropietario');
        $idcomprador= $request->input('selectnom');

        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }

        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;

        $res = null;
        if(count($caja_id) != 0){//validamos si existe caja aperturada

            $reglas = array(
                'selectnom'        => 'required',
                'cantidad_accion'        => 'required|max:100',
                'configuraciones_id'        => 'required|max:100'
            );

            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                    return $validacion->messages()->toJson();
            }

            $listar        = Libreria::getParam($request->input('listar'), 'NO');
            $acciones_por_persona = DB::table('acciones')->where('persona_id', $id)->where('estado', "C")->get();
            $descripcion_venta=$request->input('descripcion');
            $cantidad_accion= $request->input('cantidad_accion');
            $fechaf = $request->input('fechai').date(" H:i:s");
            $concepto_id = $request->input('concepto_id');

            $res = null;

            //funcion para registrar las acciones del comprador
            $vendidas = Acciones::where('estado','C')->where('persona_id',$request->input('idpropietario'))->limit($request->input('cantidad_accion'))->orderBy('fechai', 'ASC')->get();

            $idCaja = DB::table('caja')->where('estado', "A")->value('id');
            $error = DB::transaction(function() use($request, $vendidas, $descripcion_venta, $fechaf, $concepto_id, $idCaja ){
                $cantidad_accion= $request->input('cantidad_accion');

                foreach($vendidas as $value){
                    $acciones                 = Acciones::find($value->id); 
                    $acciones->estado        = 'V';
                    $acciones->descripcion        = $descripcion_venta;
                    $acciones->fechaf = $fechaf;
                    $acciones->concepto_id        = $concepto_id;
                    $acciones->save();

                    $acciones               = new Acciones();    
                    $acciones->estado        = 'C';
                    $acciones->fechai        = $request->input('fechai').date(" H:i:s");
                    $acciones->descripcion        = "comprado del socio: ".DB::table('persona')->where('id', $value->persona_id)->value('nombres');
                    $acciones->persona_id        = $request->input('selectnom');
                    $acciones->codigo               =$value->codigo;
                    $acciones->configuraciones_id        = $request->input('configuraciones_id');
                    $acciones->caja_id =$idCaja;
                    $acciones->concepto_id        =  1;
                    $acciones->save();
                }

                if($cantidad_accion !== ''){
                    $historial_accion               = new HistorialAccion();    
                    $historial_accion->cantidad        = $request->input('cantidad_accion');
                    $historial_accion->estado        = 'V';
                    $historial_accion->fecha        = $request->input('fechai');
                    $historial_accion->descripcion        = $request->input('descripcion');
                    $historial_accion->persona_id        = $request->input('idpropietario');
                    $historial_accion->configuraciones_id        = $request->input('configuraciones_id');
                    $historial_accion->caja_id = $idCaja;
                    $historial_accion->concepto_id        = $request->input('concepto_id');
                    $historial_accion->save();
                    
                }

                if($cantidad_accion !== ''){
                    $historial_accion               = new HistorialAccion();    
                    $historial_accion->cantidad        = $request->input('cantidad_accion');
                    $historial_accion->estado        = 'C';
                    $historial_accion->fecha        = $request->input('fechai');
                    $historial_accion->descripcion        = $request->input('descripcion');
                    $historial_accion->persona_id        = $request->input('selectnom');
                    $historial_accion->configuraciones_id        = $request->input('configuraciones_id');
                    $historial_accion->caja_id = $idCaja;
                    $historial_accion->concepto_id        = $request->input('concepto_id');
                    $historial_accion->save();
                    
                }

                //registrar compra de de la persona que compra 
                $idCaja = DB::table('caja')->where('estado', "A")->value('id');
                $cant_tranferencia= $request->input('cantidad_accion');
                //datos de la persona
                $persona_comprador = DB::table('persona')->where('id', $request->input('selectnom'))->value('nombres');
                $persona_vendedor = DB::table('persona')->where('id', $request->input('idpropietario'))->value('nombres');
                //registro de venta en la transaccion

                $transaccion = new Transaccion();
                $transaccion->fecha = $request->input('fechai').date(" H:i:s");
                $transaccion->monto = 0.0;
                $transaccion->concepto_id = $request->input('concepto_id');
                $transaccion->descripcion =  "transferencia de:  ".$request->input('cantidad_accion')." acciones del Socio ".$persona_vendedor." al Socio  ".$persona_comprador.".";
                $transaccion->usuario_id =Caja::getIdPersona();
                $transaccion->inicial_tabla ="AC";
                $transaccion->caja_id =$idCaja;
                $transaccion->save();
                
            });

            $cant = $request->input('cantidad_accion');
            $fecha = $request->input('fechai').date(" H:i:s");
            $id_vendedor = $request->input('idpropietario');
            $id_comprador = $request->input('selectnom');
            $res = $error;
        }else{
            $res = "No hay una caja aperturada, por favor aperture una caja ...!";
        }
        $res = is_null($res) ? "OK" : $res;
        $respuesta = array($res, $id_vendedor, $id_comprador, $cant, $fecha);
        return $respuesta;
    }

    //metodo para generar voucher en pdf
    public function generarvoucheraccionPDF($id, $cant, $fecha, Request $request)
    {    

        $cantidad =  Acciones::where('estado','C')->where('persona_id',$id)->where('fechai',$fecha)->where('deleted_at','=',null)->count();

        $persona = DB::table('persona')->where('id', $id)->first();
        $monto_ahorro = DB::table('ahorros')->where('persona_id', $id)->where('estado','P')->value('capital');
        $CantAcciones = DB::table('acciones')->where('estado', 'C')->where('persona_id',$id)->where('deleted_at','=',null)->count();
        $titulo = $persona->nombres.$cantidad;
        $view = \View::make('app.acciones.generarvoucheraccionPDF')->with(compact('lista', 'id', 'persona','cantidad', 'fecha','CantAcciones','monto_ahorro'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        //PDF::SetLeftMargin(40);
        PDF::SetRightMargin(110);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

    //metodo para generar voucher en pdf
    public function generarvoucheraccionventaPDF($id, $cant, $fecha, Request $request)
    {    
        $cantidad =  Acciones::where('estado','C')->where('persona_id',$id)->where('fechai',$fecha)->where('deleted_at','=',null)->count();

        $persona = DB::table('persona')->where('id', $id)->first();
        $monto_ahorro = DB::table('ahorros')->where('persona_id', $id)->where('estado','P')->value('capital');
        $CantAcciones = DB::table('acciones')->where('estado', 'V')->where('persona_id',$id)->where('deleted_at','=',null)->count();
        $titulo = $persona->nombres.$cant;
        $view = \View::make('app.acciones.generarvoucheraccionventaPDF')->with(compact('lista', 'id', 'persona','cantidad', 'fecha','CantAcciones','monto_ahorro'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        //PDF::SetLeftMargin(40);
        PDF::SetRightMargin(110);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

    //generar voucher a la hora de la compra de acciones
    public function reciboaccionpdf($id, $cant, $fecha){  
        $detalle        = Acciones::listAcciones($id);
        
        $lista           = $detalle->get();
        $persona = DB::table('persona')->where('id', $id)->first();
        $monto_ahorro = DB::table('ahorros')->where('persona_id', $id)->where('estado','P')->value('capital');
        $CantAcciones = DB::table('acciones')->where('estado', 'C')->where('persona_id',$id)->count();
        $titulo = $persona->nombres.$cant;

        $view = \View::make('app.acciones.voucheraccionPDF')->with(compact('lista', 'id', 'persona','cant', 'fecha','CantAcciones','monto_ahorro'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        //PDF::SetLeftMargin(40);
        PDF::SetRightMargin(110);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }


    //recibo para vender acciones 
    public function reciboaccionventapdf($id_vendedor, $id_comprador, $cant, $fecha){  
        $detalle        = Acciones::listAcciones($id_vendedor);
        $lista           = $detalle->get();

        //id del vendedor
        $vendedor = DB::table('persona')->where('id', $id_vendedor)->first();

        $comprador = DB::table('persona')->where('id', $id_comprador)->first();

        $monto_ahorroComprador = DB::table('ahorros')->where('persona_id', $id_comprador)->where('estado','P')->value('capital');
        $CantAccioneComprador = DB::table('acciones')->where('estado', 'C')->where('persona_id',$id_comprador)->count();
        $titulo = $comprador->nombres.$cant;

        $view = \View::make('app.acciones.reciboaccionventapdf')->with(compact('lista', 'id', 'vendedor','comprador','cant', 'fecha','CantAccioneComprador','monto_ahorroComprador'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        //PDF::SetLeftMargin(40);
        PDF::SetRightMargin(110);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }


    //metodo para generar normas en pdf
    public function generarnormasaccionPDF(Request $request)
    {    
        $list        = Acciones::list_acciones_persona();
        $lista           = $list->get();

        $cant = DB::table('acciones')->where('estado', 'C')->count();

        $year = date("Y");
        $month = date("m");
        $day = date("d");

        $titulo = "normas_acciones";
        $view = \View::make('app.acciones.generarnormasaccionPDF')->with(compact('lista', 'cant','year','month','day'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        PDF::SetLeftMargin(20);
        //PDF::SetRightMargin(110);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }

    public function listpersonas(Request $request){
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $tags = Persona::where("dni",'ILIKE', '%'.$term.'%')->orwhere("nombres",'ILIKE', '%'.$term.'%')->orwhere("apellidos",'ILIKE', '%'.$term.'%')->limit(5)->get();
        $formatted_tags = [];
        foreach ($tags as $tag) {
            if($tag->tipo == 'S '){
                $formatted_tags[] = ['id' => $tag->id, 'text' => $tag->nombres." ".$tag->apellidos];
            }else{
                //$formatted_tags[] = ['id'=> '', 'text'=>"seleccione socio"];
            }
        }

        return \Response::json($formatted_tags);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $existe = Libreria::verificarExistencia($id, 'acciones');
        if ($existe !== true) {
            return $existe;
        }

        $error = DB::transaction(function() use($id){
            $acciones = Acciones::find($id);
            $fecha = $acciones->fechai;
            $configuraciones = Configuraciones::find($acciones->configuraciones_id);

            $concepto_id = $acciones->concepto_id;
            $persona_id = $acciones->persona_id;

            $transacciones = Transaccion::where('persona_id',$acciones->persona_id)->where('fecha',$acciones->fechai)->where('inicial_tabla','AC')->get();
            $cantidad = ((($transacciones[0]->monto)/($configuraciones->precio_accion))-1);
            $caja_id =$transacciones[0]->caja_id;

            $transacciones[0]->delete();
            $acciones->delete();

            $transaccion = new Transaccion();
            $transaccion->fecha = $fecha;
            $transaccion->monto = ($cantidad*($configuraciones->precio_accion));
            $transaccion->acciones_soles = ($cantidad*($configuraciones->precio_accion));
            $transaccion->concepto_id = $concepto_id;
            $transaccion->descripcion = " compro ".$cantidad." acciones";
            $transaccion->persona_id = $persona_id;
            $transaccion->inicial_tabla ="AC";
            $transaccion->usuario_id =Caja::getIdPersona();
            $transaccion->caja_id =$caja_id;
            $transaccion->save();
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
        $existe = Libreria::verificarExistencia($id, 'acciones');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Acciones::find($id);
        $entidad  = 'Accion1';
        $formData = array('route' => array('acciones.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

}
