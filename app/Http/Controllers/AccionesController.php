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
            'index'  => 'acciones.index',
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
        $cboConfiguraciones = array('' => 'Seleccione') + Configuraciones::pluck('precio_accion', 'id')->all();
        $cboConcepto  =array(1=>'Compra de Acciones');
        $entidad        = 'Acciones';
        $acciones        = null;
        $formData       = array('acciones.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Comprar Acciones'; 
        return view($this->folderview.'.mant')->with(compact('acciones', 'formData', 'entidad', 'boton', 'listar','cboConfiguraciones','cboConcepto'));
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
            'dni'        => 'required|max:100',
            'cantidad_accion'        => 'required|max:100',
            'fechai'        => 'required|max:100',
            'configuraciones_id'        => 'required|max:100'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        //evaluando los datos que vienen del view
        $caja = Caja::where("estado","=","A")->get();
        $res = "OK";
        if(count($caja) > 0){//validamos si existe caja aperturada
            $error = DB::transaction(function() use($request){
                $idCaja = DB::table('caja')->where('estado', "A")->value('id');
                $cantidad_accion= $request->input('cantidad_accion');
                if($cantidad_accion !== ''){
                    for($i=0; $i< $cantidad_accion; $i++){
                        $acciones               = new Acciones();    
                        $acciones->estado        = 'C';
                        $acciones->fechai        = $request->input('fechai').date(" H:i:s");
                        $acciones->descripcion        = $request->input('descripcion');
                        $acciones->persona_id        = $request->input('persona_id');
                        $acciones->configuraciones_id        = $request->input('configuraciones_id');
                        $acciones->caja_id = $idCaja;
                        $acciones->save();
                    }
                }

                $fechahora_actual = date("Y-m-d H:i:s");
                $cantidad_accion = $request->input('cantidad_accion');
                $idCaja = DB::table('caja')->where('estado', "A")->value('id');
                $configuracion= Configuraciones::all();
                $result= $configuracion->last();
                $precio = $result->precio_accion;
                $monto_ingreso = ($cantidad_accion*$precio);
                //datos de la persona
                $persona_nombre = DB::table('persona')->where('id', $request->input('persona_id'))->value('nombres');
                $transaccion = new Transaccion();
                $transaccion->fecha = $fechahora_actual;
                $transaccion->monto = $monto_ingreso;
                $transaccion->concepto_id = $request->input('concepto_id');
                $transaccion->descripcion = " compro ".$cantidad_accion." acciones";
                $transaccion->persona_id = $request->input('persona_id');
                $transaccion->usuario_id =Caja::getIdPersona();;
                $transaccion->caja_id =$idCaja;
                $transaccion->save();
                
            });
            return is_null($error) ? "OK" : $error;
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
        $listAcciones        = Acciones::listAcciones($persona_id);
        $lista           = $listAcciones->get();

        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cantidad', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripcion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Voucher', 'numero' => '1');
        $titulo_detalle = $this->tituloDetalle;
        $ruta             = $this->rutas;
        $inicio           = 0;
        if (count($lista) > 0) {
            return view($this->folderview.'.detalle')->with(compact('lista', 'entidad', 'cabecera',  'ruta', 'inicio', 'persona_id'));
        }
        return view($this->folderview.'.detalle')->with(compact('lista', 'entidad', 'persona_id', 'ruta'));
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
    //venta de acciones
    public function cargarventa($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $persona = Persona::find($id);
        if (isset($listarParam)) {
            $listar = $listarParam;
        }
        $cboConfiguraciones = array('' => 'Seleccione') + Configuraciones::pluck('precio_accion', 'id')->all();
        $cboConcepto  =array(7=>'Venta de Acciones');
        $acciones        = Acciones::find($id);
        $entidad        = 'Acciones';

        $boton          = 'Vender Acciones';
        return view($this->folderview.'.venderaccion')->with(compact('acciones','persona', 'entidad', 'boton', 'listar','cboConfiguraciones','cboConcepto'));
    }

    public function guardarventa(Request $request, $id)
    {
        $idpropietario= $request->input('idpropietario');
        $idcomprador= $request->input('idcomprador');

        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'dni'        => 'required|max:100',
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
        if($cantidad_accion !== ''){            
            for($i=0; $i< $cantidad_accion; $i++){
                $idaccion= $acciones_por_persona[$i]->id;
                $error  = DB::transaction(function() use ($idaccion,$descripcion_venta){
                    $acciones                 = Acciones::find($idaccion); 
                    $acciones->estado        = 'V';
                    $acciones->descripcion        = $descripcion_venta;
                    $acciones->fechaf = date("Y-m-d H:i:s");
                    $acciones->save();
                });
            }
        }
        //funcion para registrar las acciones del comprador
        $error = DB::transaction(function() use($request){
            $idCaja = DB::table('caja')->where('estado', "A")->value('id');
            $cantidad_accion= $request->input('cantidad_accion');
            if($cantidad_accion !== ''){
                for($i=0; $i< $cantidad_accion; $i++){
                    $acciones               = new Acciones();    
                    $acciones->estado        = 'C';
                    $acciones->fechai        = date("Y-m-d H:i:s");
                    $acciones->descripcion        = "comprado del socio: ".DB::table('persona')->where('id', $request->input('idpropietario'))->value('nombres');
                    $acciones->persona_id        = $request->input('idcomprador');
                    $acciones->configuraciones_id        = $request->input('configuraciones_id');
                    $acciones->caja_id =$idCaja;
                    $acciones->save();
                }
            }

            $fechahora_actual = date("Y-m-d H:i:s");
            //registrar compra de de la persona que compra 
            $idCaja = DB::table('caja')->where('estado', "A")->value('id');
            $cant_tranferencia= $request->input('cantidad_accion');
            //datos de la persona
            $persona_comprador = DB::table('persona')->where('id', $request->input('idcomprador'))->value('nombres');
            $persona_vendedor = DB::table('persona')->where('id', $request->input('idpropietario'))->value('nombres');
            //registro de venta en la transaccion
            $transaccion = new Transaccion();
            $transaccion->fecha = date("Y-m-d H:i:s");
            $transaccion->monto = 0.0;
            $transaccion->concepto_id = $request->input('concepto_id');
            $transaccion->descripcion =  "transferencia de:  ".$request->input('cantidad_accion')." acciones del Socio ".$persona_vendedor." al Socio  ".$persona_comprador.".";
            $transaccion->usuario_id =Caja::getIdPersona();
            $transaccion->caja_id =$idCaja;
            $transaccion->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    //metodo para generar voucher en pdf
    public function generarvoucheraccionPDF($id, $cant, $fecha, Request $request)
    {    
        $detalle        = Acciones::listAcciones($id);
        $lista           = $detalle->get();
        $persona = DB::table('persona')->where('id', $id)->first();
        $monto_ahorro = DB::table('ahorros')->where('persona_id', $id)->where('estado','P')->value('importe');
        $CantAcciones = DB::table('acciones')->where('estado', 'C')->where('persona_id',$id)->count();
        $titulo = $persona->nombres.$cant;
        $view = \View::make('app.acciones.generarvoucheraccionPDF')->with(compact('lista', 'id', 'persona','cant', 'fecha','CantAcciones','monto_ahorro'));
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
}
