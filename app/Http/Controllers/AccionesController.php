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
use App\Pagos;
use App\HistorialAccion;
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use PDF;
use DateTime;

class AccionesController extends Controller
{
    protected $folderview      = 'app.acciones';
    protected $tituloAdmin     = 'Compra y Venta de Acciones';
    protected $tituloRegistrar = 'Devolucion de Capital';
    protected $tituloModificar = 'Modificar acciones';
    protected $tituloDetalle = 'Detalle de Acciones';
    protected $tituloResumen = 'Lista de Socios que ya compraron acciones en esta fecha!';
    protected $tituloVenta = 'Venta de Acciones';
    protected $tituloEliminar  = 'Eliminar acciones';
    protected $rutas           = array('create' => 'acciones.create', 
            'edit'   => 'acciones.edit', 
            'listacciones' => 'acciones.listacciones',
            'delete' => 'acciones.eliminar',
            'search' => 'acciones.buscar',

            'cargarcompra' => 'acciones.cargarcompra',
            'guardarcompra' => 'acciones.guardarcompra',

            'cargarventa'   => 'acciones.cargarventa',
            'updateventa'   => 'acciones.updateventa',
            'reciboaccionpdf' => 'acciones.reciboaccionpdf',
            'reciboaccionventapdf' => 'acciones.reciboaccionventapdf',
            'index'  => 'acciones.index',
            'listpersonas' => 'acciones.listpersonas',
            'buscaraccion'=> 'acciones.buscaraccion',

            'generarreport' => 'acciones.generarreport',
            'modalreporte' => 'acciones.modalreporte',
            'reporteporperiodoPDF'=> 'acciones.reporteporperiodoPDF',


            'listresumen' => 'acciones.listresumen',
            'buscarresumen' => 'acciones.buscarresumen'
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

        $nombres           = $request->input('dni');
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Acciones';

        $caja_fecha = Caja::where("estado","=","A")->value('fecha_horaapert');
        $caja_fecha = ($caja_fecha != "")?$caja_fecha:date('Y-m-d');

        $resultado        = Acciones::listar($caja_fecha, $nombres);
        $lista            = $resultado->get();
        
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DNI', 'numero' => '1');
        $cabecera[]       = array('valor' => 'CODIGO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'NOMBRES', 'numero' => '1');
        $cabecera[]       = array('valor' => 'MOVIMIENTOS', 'numero' => '2');
        $cabecera[]       = array('valor' => 'OPERACIONES', 'numero' => '2');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_registrar = $this->tituloRegistrar;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_ventaaccion','titulo_registrar', 'ruta','idcaja'));
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
        $titulo_resumen = $this->tituloResumen;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','idcaja','titulo_resumen'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $result = DB::table('caja')->where('estado', 'A')->first();
        $ingresos = $result->monto_iniciado;
        $egresos = 0;
        $diferencia = 0;
        $saldo = Transaccion::getsaldo($result->id)->get();
        for($i = 0; $i<count( $saldo ); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }

        $diferencia= round($ingresos-$egresos,1);


        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $cboConcepto  =array(22=>'Devolucion de Capital');
        $entidad        = 'Acciones';
        $acciones        = null;
        $config = Configuraciones::All()->last();
        $precio_accion = $config->precio_accion;
        $id_config = $config->id;

        $caja = Caja::where("estado","=","A")->get();
        $fecha_caja = count($caja) == 0? 0: Date::parse($caja[0]->fecha_horaapert)->format('Y-m-d');

        $ruta = $this->rutas;
        $cboPers = array(0=>'Seleccione...');
        $formData       = array('acciones.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Devolucion del Capital'; 
        return view($this->folderview.'.mant')->with(compact('diferencia', 'acciones', 'formData', 'entidad', 'boton', 'id_config','listar','cboConfiguraciones','cboConcepto','ruta','cboContribucion','cboPers','precio_accion','fecha_caja'));
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

        $error = null;
        if(count($caja_id) != 0){//validamos si existe caja aperturada
            $reglas = array(
                'selectnom'        => 'required',
                'cantidad_accion'        => 'required|max:100',
                'fechai'        => 'required|max:100',
            );

            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }

            $error = DB::transaction(function() use($request){
                $idCaja = DB::table('caja')->where('estado', "A")->value('id');
                $cantidad_accion= $request->input('cantidad_accion');

                if($cantidad_accion !== ''){
                    $accion_x_persona = DB::table('acciones')->where('persona_id',$request->input('selectnom'))->where('tipo','A')->where('deleted_at',null)->orderBy('id','ASC')->get();
                    if(count($accion_x_persona)!=0){
                        $cont =0;
                        for($i=0; $i<count($accion_x_persona); $i++){
                            if($cantidad_accion > $cont ){
                                $acciones               = Acciones::find($accion_x_persona[$i]->id);    
                                $acciones->tipo        = 'I';
                                $acciones->fechaf        = $request->input('fechai').date(" H:i");
                                $acciones->descripcion        = $request->input('descripcion');
                                $acciones->save();
                                $cont++;
                            }
                        }
                    }
                }

                $transaccion = new Transaccion();
                $transaccion->fecha =           $request->input('fechai').date(" H:i");
                $transaccion->monto =           -$request->get('total');
                $transaccion->rec_capital =     -$request->get('total');
                $transaccion->concepto_id =     $request->input('concepto_id');
                $transaccion->persona_id =      $request->input('selectnom');
                $transaccion->descripcion =     $request->input('descripcion');
                $transaccion->usuario_id      = Caja::getIdPersona();
                $transaccion->caja_id =         $idCaja;
                $transaccion->save();

            });
        }else{
            $error = "Caja no aperturada!";
        }
        return is_null($error) ? "OK" : $error;
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

    public function modalreporte($persona_id, Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;

        $entidad = "Accion5";
        $titulo_detalle = $this->tituloDetalle;
        $ruta             = $this->rutas;
        $inicio           = 0;

        $anioactual = date('Y');
        $anios = array();
        $anioi =2008;

        for($anyo=$anioactual; $anyo >=$anioi;  $anyo --){
            $anios[$anyo] = $anyo;
        }

        $mesactual = date('m');
        $meses = array();
        $meses['01'] ="Enero";
        $meses['02'] ="Febrero";
        $meses['03'] ="Marzo";
        $meses['04'] ="Abril";
        $meses['05'] ="Mayo";
        $meses['06'] ="Junio";
        $meses['07'] ="Julio";
        $meses['08'] ="Agosto";
        $meses['09'] ="Septiembre";
        $meses['10'] ="Octubre";
        $meses['11'] ="Noviembre";
        $meses['12'] ="Diciembre";

    
        return view($this->folderview.'.modalreporte')->with(compact('anioactual', 'mesactual', 'meses', 'anios', 'lista', 'entidad', 'persona_id', 'ruta','idcaja','titulo_detalle'));
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
        $cabecera[]       = array('valor' => 'fv', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Descripcion', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Voucher', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '1');
      
        $titulo_eliminar  = $this->tituloEliminar;
        $ruta             = $this->rutas;
        $inicio           = 0;

        $caja = Caja::where("estado","=","A")->get();
        $fecha_caja = count($caja) == 0? date('Y-m'): Date::parse( $caja[0]->fecha_horaapert )->format('Y-m') ;

        $cant_acciones = Acciones::where('estado','C')->where('persona_id',$persona_id)->where('deleted_at',null)->where('tipo','A')->count();

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
            return view($this->folderview.'.listdetalle')->with(compact('concepto_id','cant_acciones','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio','Month','titulo_eliminar','fecha_caja'));
        }
        return view($this->folderview.'.listdetalle')->with(compact('concepto_id','cant_acciones','lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio', 'Month','titulo_eliminar','fecha_caja'));
    
    }

    public function generarreport(Request $request)
    {
        $res = null;
        $persona_id = $request->get('persona_id');
        $anio = $request->get('anio');
        $monthi = $request->get('monthi');
        $monthf = $request->get('monthf');
        $res = is_null($res) ? "OK" : $res;
        $respuesta = array($res, $persona_id, $anio,$monthi, $monthf);
        return $respuesta;
    }
    

    public function reporteporperiodoPDF($persona_id, $anio, $monthi, $monthf, Request $request)
    {    
       
        $accionesC        = Acciones::listAccionPorMesCompradas($persona_id, $anio, $monthi, $monthf);
        $accionesV        = Acciones::listAccionPorMesVendidas($persona_id, $anio, $monthi, $monthf);
        $listaC            = $accionesC->get();
        $listaV            = $accionesV->get();

        $datos_persona  = Persona::find($persona_id);
        $inicio =0;

        $meses = array();
        $meses[1] ="Enero";
        $meses[2] ="Febrero";
        $meses[3] ="Marzo";
        $meses[4] ="Abril";
        $meses[5] ="Mayo";
        $meses[6] ="Junio";
        $meses[7] ="Julio";
        $meses[8] ="Agosto";
        $meses[9] ="Septiembre";
        $meses[10] ="Octubre";
        $meses[11] ="Noviembre";
        $meses[12] ="Diciembre";


        $titulo = "reporte ".$datos_persona->nombres;
        $view = \View::make('app.acciones.reporteporperiodoPDF')->with(compact('listaC', 'listaV', 'meses', 'anio','inicio','datos_persona','presidente','tesorero'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('P','A4',0);
        PDF::SetTopMargin(5);
        //PDF::SetLeftMargin(40);
        //PDF::SetRightMargin(40);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
 
        PDF::Output($titulo.'.pdf', 'I');

            //esta es ===============================================================================================================================>
        // $error = null;
        // $contador = 0;
        // $accion_vendidas = Acciones::where('estado','V')->where('deleted_at',null)->get();
        // $error = DB::transaction(function() use($accion_vendidas, $contador){
        //     foreach ($accion_vendidas as $key => $value) {
        //         $Acciones_com = Acciones::where('estado','C')->where('codigo',$value->codigo)->get();
        //         if(count($Acciones_com) > 0){
        //             $accione_com = $Acciones_com[0];
        //             $value->fecha_transf = $value->fechai;
        //             $value->save();
        //             $accione_com->fecha_transf = $value->fechaf;
        //             $accione_com->fechai = $value->fechai;
        //             $accione_com->save();

        //             $contador ++;
        //             echo(' - '.$accione_com->codigo);
        //         }
        //     }
        // });
        // echo("Contador: ".$contador);
        
        // $listaf = Acciones::where('estado','C')->where('fecha_transf',null)->get();
        // foreach ($listaf as $key => $value) {
        //     $value->fecha_transf = $value->fechai;
        //     $value->save();
        // }

        // $lista_acciones_enero = Acciones::where('estado','C') ->where(DB::raw('extract( year from fechai)'),'=',2019) ->where(DB::raw('extract( month from fechai)'),'=',1)->get();
        // $error = DB::transaction(function() use($lista_acciones_enero){

        //     foreach ($lista_acciones_enero as $key => $ac_c) {
        //         $acc = Acciones::where('codigo', $ac_c->codigo)->where('deleted_at',null)->get();
        //         if(count($acc)>0){
        //             $acc_v = $acc[0];
        //             $acompra = Acciones::find($ac_c->id);

        //             $acompra->fechai = $acc_v->fechai;
        //             $acompra->save();
        //             echo("codigo: ".$ac_c->codigo);
        //         }
        //     }
        // });

        // echo($error);

        /*
        $accion_vendidas = DB::select("select * from(select  *, count(*) over(partition by codigo) n from acciones) as a where n =3 and persona_id = 16 ");
    
                $error = DB::transaction(function() use($accion_vendidas){
                    $contador=0;
                foreach ($accion_vendidas as $key => $value) {
                //    echo(" - ".$value->persona_id);
                   if($value->n ==3 ){

                       $accion_primero = Acciones::where('codigo', $value->codigo)->where('estado', 'V')->get();
                       $accion_tercero = Acciones::where('codigo', $value->codigo)->where('estado', 'C')->get();
                       if(count($accion_tercero)>0){
                        // echo( " :".$contador++);
                        
                            $ac_segundo = Acciones::find($value->id);
                            $ac_primero = $accion_primero[0];
                            $ac_tercero = $accion_tercero[0];
                            echo( " :".$ac_tercero->persona_id."-".$contador++." ");
                            $ac_primero->fecha_transf = $ac_primero->fechai;
                            $ac_primero->save();
    
                            $ac_segundo->fechai = $ac_primero->fechai;
                            $ac_segundo->fecha_transf = $ac_primero->fechaf;
                            $ac_segundo->save();
    
                            $ac_tercero->fechai = $ac_primero->fechai;
                            $ac_tercero->fecha_transf = $ac_segundo->fechaf;
                            $ac_tercero->save();
                       }
                   }
                //    else if($value->n ==2){
                //     $acciones = Acciones::where('codigo', $value->codigo)->where('persona_id', 7)->orderby('id',ASC)->get();
                //         if(count($acciones)>0){
                //             echo( " :".$contador++);
                //             $ac_segundo = Acciones::find($value->id);
                //             $ac_tercero = Acciones::find($acciones[0]->id);
                            
                //             $ac_segundo->fecha_transf = $ac_segundo->fechai;
                //             $ac_segundo->save();
    
                //             $ac_tercero->fechai = $ac_segundo->fechai;
                //             $ac_tercero->fecha_transf = $ac_segundo->fechaf;
                //             $ac_tercero->save();
                //         }
                //    }
                   
                }
            });
            */
/*
            $acciones_vendidas = Acciones::where('estado','V')->where('fecha_transf',null)->where('deleted_at',null)->get();
            $contador=0;
            foreach ($acciones_vendidas as $key => $ac_venta) {
                
                $ac_comp = Acciones::where('codigo',$ac_venta->codigo)->where('estado','C')->get();
                if(count($ac_comp)>0){
                    $ac_venta->fecha_transf = $ac_venta->fechai;
                    $ac_venta->save();

                    $ac_compra = $ac_comp[0];
                    $ac_compra->fechai = $ac_venta->fechai;
                    $ac_compra->fecha_transf = $ac_venta->fechaf;
                    $ac_compra->save();
                    echo( " :".$ac_compra->persona_id."-".$contador++." ");

                }
            }
            */
            // $acciones_comp = Acciones::where('estado','C')->where('fecha_transf',null)->where('deleted_at',null)->get();
            // $contador=0;
            // foreach ($acciones_comp as $key => $ac_compra) {
            //     $ac_compra->fecha_transf = $ac_compra->fechai;
            //     $ac_compra->save();
            //     echo( " :".$ac_compra->persona_id."-".$contador++." ");
            // }
            

        /* consultas posgresql
        
        SELECT persona_id, fechai, fecha_transf, fechaf, descripcion FROM acciones where  tipo = 'I' and deleted_at is null

        select * from historial_accion where estado='V'

        SELECT *  FROM historial_accion where  deleted_at is null;

        SELECT SUM(CANTIDAD) as ventas FROM historial_accion where estado = 'V' and deleted_at is null;

        98

        select * from acciones where codigo = '0000495'

        select * from acciones where updated_at > date('2020-02-27')

        #CONSULTA PRA LAS INACTIVAS
        select * from acciones where TIPO = 'I' AND DELETED_AT IS NULL ORDER BY FECHAI ASC

        SELECT COUNT(*), acciones.persona_id, extract( year from acciones.fechai), extract( month from acciones.fechai) FROM ACCIONES WHERE PERSONA_ID = 25 AND deleted_at is null group by extract( year from acciones.fechai), extract( month from acciones.fechai), acciones.persona_id order by extract( year from acciones.fechai) 

        SELECT COUNT(*),  extract( month from acciones.fechai) FROM ACCIONES WHERE extract( year from acciones.fechai) = 2018 AND deleted_at is null group by extract( month from acciones.fechai)  order by extract( MONTH from acciones.fechai) 

*/

        
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

    //listar cantidad de acciones compradas en la fecha 
    public function get_acciones_fecha(Request $request, $persona_id, $fecha, $x, $z){
        $fecha = Date::parse($fecha)->format('Y-m-d');
        $persona_id = intval($persona_id);
        if($request->ajax()){
            $year = Date::parse($fecha)->format('Y');
            $month = Date::parse($fecha)->format('m');
            $day = Date::parse($fecha)->format('d');
            $dato = Acciones::where('persona_id',$persona_id)->where(DB::raw('extract(year from fechai)'),'=',$year)->where(DB::raw('extract(month from fechai)'),'=',$month)->where(DB::raw('extract(day from fechai)'),'=',$day)->where('deleted_at',null)->count();
            return response()->json($dato);
        }
    }

    //compra de acciones
    public function cargarcompra($id, Request $request){
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }

        $cboPersona = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboConfiguraciones = Configuraciones::pluck('precio_accion', 'id')->all();
        $cboConcepto  =array(1=>'Compra de Acciones');
        $cboContribucion  =array(11=>'Contribución de Ingreso');
        $entidad        = 'Acciones';
        $acciones        = null;
        $config = Configuraciones::All()->last();
        $precio_accion = $config->precio_accion;
        $id_config = $config->id;

        $cantaccionpersona = Acciones::where('estado','C')->where('deleted_at',null)->where('persona_id',$id)->count();
        $cant_menos_id_select = Acciones::where('estado','C')->where('deleted_at',null)->where('persona_id','!=',$id)->count();
        $cant_total = $cantaccionpersona+ $cant_menos_id_select;


        $caja = Caja::where("estado","=","A")->get();
        $fecha_caja = count($caja) == 0? 0: Date::parse($caja[0]->fecha_horaapert)->format('Y-m-d');
        $listar = "NO";
        $ruta = $this->rutas;
        $cboPers = array(0=>'Seleccione...');
        $formData       = array('acciones.store');

        $persona = Persona::find($id);
        if (isset($listarParam)) {
            $listar = $listarParam;
        }
        $nom = '  DNI: '.$persona->dni.'   NOMBRES: '.'  '.$persona->apellidos.'  '.$persona->nombres;

        $boton          = 'Comprar Accion';
        return view($this->folderview.'.compraaccion')->with(compact('acciones','persona','cantaccionpersona','cant_menos_id_select','cant_total' ,'id' ,'entidad', 'boton', 'listar','cboConfiguraciones','cboConcepto','cboContribucion','ruta','nom','id_config','precio_accion','fecha_caja'));
    }

    public function guardarcompra(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }

        //evaluando los datos que vienen del view
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;

        $res = null;
        if(count($caja_id) != 0){//validamos si existe caja aperturada
            $reglas = array(
                'cantidad_accion'        => 'required|max:100',
                'fechai'        => 'required|max:100',
                'monto_recibido' =>'required',
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

                    $can_moment = DB::table('acciones')->where('deleted_at',null)->count();
                    $codigo = (count($can_moment) ==0)?0:$can_moment;
                    for($i=0; $i< $cantidad_accion; $i++){
                        $codigo++;
                        $acciones               = new Acciones();    
                        $acciones->estado        = 'C';
                        $acciones->tipo        = 'A';
                        $acciones->fechai        = $request->input('fechai').date(" H:i");
                        $acciones->fecha_transf  = $request->input('fechai').date(" H:i");
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
                        $acciones->persona_id        = $request->input('persona_id');
                        $acciones->configuraciones_id        = $request->input('configuraciones_id');
                        $acciones->caja_id = $idCaja;
                        $acciones->concepto_id        = $request->input('concepto_id');
                        $acciones->save();
                    }
                }

                $buy_last = Acciones::where('estado','C')->where('persona_id',$request->input('persona_id'))->limit($request->input('cantidad_accion'))->orderBy('fechai', 'DSC')->get();
                $descrp = "";
                foreach($buy_last as $value){
                    $descrp .= $value->codigo.',';
                }

                if($cantidad_accion !== ''){
                    $historial_accion               = new HistorialAccion();    
                    $historial_accion->cantidad        =  $request->input('cantidad_accion');
                    $historial_accion->estado        = 'C';
                    $historial_accion->fecha        = $request->input('fechai').date(" H:i");
                    $historial_accion->descripcion        = $descrp;
                    $historial_accion->persona_id        = $request->input('persona_id');
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
                $persona_nombre = DB::table('persona')->where('id', $request->input('persona_id'))->value('nombres');

                //comision voucher si esque desea imprimirlo
                $transaccion = new Transaccion();
                $transaccion->fecha = $request->input('fechai').date(" H:i");
                $transaccion->monto = $monto_ingreso;
                $transaccion->acciones_soles = $monto_ingreso;
                $transaccion->concepto_id = $request->input('concepto_id');
                $transaccion->descripcion = " compro ".$cantidad_accion." acciones";
                $transaccion->persona_id = $request->input('persona_id');
                $transaccion->inicial_tabla ="AC";
                $transaccion->usuario_id =Caja::getIdPersona();
                $transaccion->caja_id =$idCaja;
                $transaccion->save();

                $contribucion = $request->input('monto');
                $accion_last = Acciones::All()->last();
                $persona_last = Persona::find($accion_last->persona_id);

                if($contribucion != ''){
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $request->input('fechai').date(" H:i");
                    $transaccion->monto = $contribucion;
                    $transaccion->concepto_id = $request->input('contribucion_id');
                    $transaccion->descripcion = $persona_last->nombres;
                    $transaccion->usuario_id =Caja::getIdPersona();
                    $transaccion->inicial_tabla ="AC";
                    $transaccion->caja_id =$idCaja;
                    $transaccion->save();
                }

                $monto_recibido = intval($request->input('monto_recibido'));
                if($monto_recibido != 0){
                    $pagos = new Pagos();
                    $pagos->monto_recibido = $request->input('monto_recibido');
                    $pagos->monto_pago = $request->input('monto_pago');
                    $pagos->ini_tabla = 'AC';
                    $pagos->parte_entregado = 0;
                    $pagos->fecha = $request->input('fechai').date(" H:i");
                    $pagos->persona_id = $request->input('persona_id');
                    $pagos->caja_id =$idCaja;
                    $pagos->save();
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


    //venta de acciones
    public function cargarventa($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $cant_acciones = DB::table('acciones')->where('estado','C')->where('persona_id',$id)->where('deleted_at',null)->count();

        $listar = "NO";
        $ruta = $this->rutas;
        $persona = Persona::find($id);
        if (isset($listarParam)) {
            $listar = $listarParam;
        }
        $config = Configuraciones::All()->last();
        $precio_accion = $config->precio_accion;

        $cboContribucion  =array(11=>'Contribución de Ingreso');

        $caja = Caja::where("estado","=","A")->get();
        $fecha_caja = count($caja) == 0? 0: Date::parse($caja[0]->fecha_horaapert)->format('Y-m-d');

        $cboPers = array(0=>'Seleccione...');
        $nom =  'NOM: '.$persona->nombres.' '.$persona->apellidos.' ACCIONES: '.$cant_acciones;
        $cboConfiguraciones = Configuraciones::pluck('precio_accion', 'id')->all();
        $cboConcepto  =array(2=>'Venta de Acciones');
        $acciones        = Acciones::find($id);
        $entidad        = 'Acciones';

        $boton          = 'Vender Acciones';
        return view($this->folderview.'.venderaccion')->with(compact('acciones','persona', 'entidad', 'boton', 'listar','cboConfiguraciones','cboConcepto','cboContribucion','ruta','nom','cant_acciones','cboPers','precio_accion','fecha_caja'));
    }

    public function guardarventa(Request $request, $id)
    {
        $idpropietario= $request->input('idpropietario');
        $idcomprador= $request->input('selectnom');

        $existe = Libreria::verificarExistencia($id, 'persona');
        if ($existe !== true) {
            return $existe;
        }

        // $caja_id = Caja::where("estado","=","A")->value('id');
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = (count($caja) != 0)?$caja[0]->id:0;

        $res = null;
        if($caja_id != 0){//validamos si existe caja aperturada

            $reglas = array(
                'selectnom'   => 'required',
                'cantidad_accion' => 'required|max:100',
                'configuraciones_id'  => 'required|max:100'
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
            $vendidas = Acciones::where('estado','C')->where('persona_id',$request->input('idpropietario'))->limit($request->input('cantidad_accion'))->orderBy('fechai', 'ASC')->where('deleted_at',null)->get();

            // $idCaja = DB::table('caja')->where('estado', "A")->value('id');
            $error = DB::transaction(function() use($request, $vendidas, $descripcion_venta, $fechaf, $concepto_id, $caja_id, $caja ){
                $cantidad_accion= $request->input('cantidad_accion');

                foreach($vendidas as $value){
                    $acciones                 = Acciones::find($value->id); 
                    $acciones->estado        = 'V';
                    $acciones->descripcion        = $descripcion_venta;
                    $acciones->fechaf = $fechaf;
                    $acciones->concepto_id        = $concepto_id;
                    $acciones->save();

                    $new_acciones               = new Acciones();    
                    $new_acciones->estado        = 'C';
                    $new_acciones->tipo        = 'A';
                    $new_acciones->fechai        = $value->fechai;
                    $new_acciones->fecha_transf        = $caja[0]->fecha_horaapert;
                    $new_acciones->descripcion        = "comprado del socio: ".DB::table('persona')->where('id', $value->persona_id)->value('nombres');
                    $new_acciones->persona_id        = $request->input('selectnom');
                    $new_acciones->codigo               =$value->codigo;
                    $new_acciones->configuraciones_id        = $request->input('configuraciones_id');
                    $new_acciones->caja_id =$caja_id;
                    $new_acciones->concepto_id        =  1;
                    $new_acciones->save();
                }

                if($cantidad_accion !== ''){
                    $historial_accion               = new HistorialAccion();    
                    $historial_accion->cantidad        = -$request->input('cantidad_accion');
                    $historial_accion->estado        = 'V';
                    $historial_accion->fecha        = $request->input('fechai');
                    $historial_accion->descripcion        = $request->input('descripcion');
                    $historial_accion->persona_id        = $request->input('idpropietario');
                    $historial_accion->configuraciones_id        = $request->input('configuraciones_id');
                    $historial_accion->caja_id = $caja_id;
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
                    $historial_accion->caja_id = $caja_id;
                    $historial_accion->concepto_id        = $request->input('concepto_id');
                    $historial_accion->save();
                    
                }

                //registrar compra de de la persona que compra 
                // $caja_id = DB::table('caja')->where('estado', "A")->value('id');
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
                $transaccion->caja_id =$caja_id;
                $transaccion->save();

                $contribucion = $request->input('monto');

                if($contribucion != ''){
                    $transaccion = new Transaccion();
                    $transaccion->fecha = $request->input('fechai').date(" H:i");
                    $transaccion->monto = $contribucion;
                    $transaccion->concepto_id = $request->input('contribucion_id');
                    $transaccion->descripcion = $persona_comprador;
                    $transaccion->usuario_id =Caja::getIdPersona();
                    $transaccion->inicial_tabla ="AC";
                    $transaccion->caja_id =$caja_id;
                    $transaccion->save();
                }
                
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

        $cantidad =  Acciones::where('estado','C')->where('persona_id',$id)->where('fechai',$fecha)->where('deleted_at','=',null)->where('tipo','A')->count();

        $persona = DB::table('persona')->where('id', $id)->first();
        $monto_ahorro = DB::table('ahorros')->where('persona_id', $id)->where('estado','P')->value('capital');
        $CantAcciones = DB::table('acciones')->where('estado', 'C')->where('persona_id',$id)->where('deleted_at','=',null)->where('tipo','A')->count();
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
        $cantidad =  Acciones::where('estado','C')->where('persona_id',$id)->where('fechai',$fecha)->where('deleted_at','=',null)->where('tipo','A')->count();

        $persona = DB::table('persona')->where('id', $id)->first();
        $monto_ahorro = DB::table('ahorros')->where('persona_id', $id)->where('estado','P')->value('capital');
        $CantAcciones = DB::table('acciones')->where('estado', 'V')->where('persona_id',$id)->where('deleted_at','=',null)->where('tipo','A')->count();
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
        $CantAcciones = DB::table('acciones')->where('estado', 'C')->where('persona_id',$id)->where('deleted_at','=',null)->where('tipo','A')->count();
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
        $CantAccioneComprador = DB::table('acciones')->where('estado', 'C')->where('persona_id',$id_comprador)->where('deleted_at','=',null)->where('tipo','A')->count();
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

        $cant = DB::table('acciones')->where('estado', 'C')->where('deleted_at',null)->where('deleted_at','=',null)->where('tipo','A')->count();

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
            if(trim($tag->estado) == 'A'){
                if(trim($tag->tipo) == 'S'){
                    $formatted_tags[] = ['id' => $tag->id, 'text' => $tag->nombres." ".$tag->apellidos];
                }else{
                    //$formatted_tags[] = ['id'=> '', 'text'=>"seleccione socio"];
                }
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
            if($acciones->estado == 'C'){
                $fecha = $acciones->fechai;
                $configuraciones = Configuraciones::find($acciones->configuraciones_id);

                $history_acc = HistorialAccion::where('persona_id',$acciones->persona_id)->where('fecha',Date::parse( $acciones->fechai )->format('Y-m-d  H:i:00'))->get();
                foreach($history_acc as $valor){
                    //datos 
                    $history_cant = $valor->cantidad;
                    $history_estado = $valor->estado;
                    $history_fecha = $valor->fecha;
                    $history_persona_id = $valor->persona_id;
                    $history_config = $valor->configuraciones_id;
                    $history_caja = $valor->caja_id;
                    $history_concepto = $valor->concepto_id;

                    $accionid = explode(',',$valor->descripcion);
                    $cant = count($accionid);
                    for($i=0;$i<count($accionid); $i++){
                    if($accionid[$i] == $acciones->codigo){
                            $valor->delete();
                            unset($accionid[$i]);
                    } 
                    }
                    if(($cant-1) == count($accionid)){
                        $dat = "";
                        foreach ($accionid as $x) {
                            $dat .=$x.',';
                        }
                        $historial_accion               = new HistorialAccion();    
                        $historial_accion->cantidad        = ($history_cant-1);
                        $historial_accion->estado        = $history_estado;
                        $historial_accion->fecha        = $history_fecha;
                        $historial_accion->descripcion        = $dat;
                        $historial_accion->persona_id        = $history_persona_id;
                        $historial_accion->configuraciones_id        = $history_config;
                        $historial_accion->caja_id = $history_caja;
                        $historial_accion->concepto_id        = $history_concepto;
                        $historial_accion->save();
                    }
                }
                
                $concepto_id = $acciones->concepto_id;
                $persona_id = $acciones->persona_id;
                $transacciones = Transaccion::where('persona_id',$acciones->persona_id)->where('fecha',Date::parse( $acciones->fechai )->format('Y-m-d  H:i:00'))->where('inicial_tabla','AC')->get();
                
                if(count($transacciones) != 0){
                    $cantidad = ((($transacciones[0]->monto)/($configuraciones->precio_accion))-1);
                    $caja_id =$transacciones[0]->caja_id;
                    $transacciones[0]->delete();

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
                }
                $acciones->delete();
            }

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
