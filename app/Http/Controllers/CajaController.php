<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Persona;
use App\Caja;
use App\Transaccion;
use App\Concepto;
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;

class CajaController extends Controller
{
    protected $folderview      = 'app.caja';
    protected $tituloAdmin     = 'Caja';
    protected $tituloRegistrar = 'Apertura Caja';
    protected $tituloModificar = 'Modificar Caja';
    protected $titulo_nuevomovimiento = 'Nuevo Movimiento';
    protected $tituloCerrarCaja = 'Cerrar Caja';
    protected $titulo_transaccion = 'Transacciones Realizadas';
    protected $tituloNuevaTransaccion = 'Nueva Transaccion';
    protected $tituloEliminar  = 'Eliminar persona';
    protected $rutas           = array('create' => 'caja.create', 
            'edit'   => 'caja.edit', 
            'delete' => 'caja.eliminar',
            'search' => 'caja.buscar',
            'index'  => 'caja.index',
            'cargarCaja'   => 'caja.cargarCaja',
            'nuevatransaccion'   => 'caja.nuevatransaccion',
            'detalle'   => 'caja.detalle',
            'nuevomovimiento'   => 'caja.nuevomovimiento',
            'cargarselect' => 'encuesta.cargarselect'
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
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $entidad          = 'Caja';
        $titulo             = Libreria::getParam($request->input('titulo'));
        $resultado        = Caja::listar($titulo);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha-hora Apertura', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha-hora Cierre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto I.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto C.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto D.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '4');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_cerrarCaja = $this->tituloCerrarCaja;
        $titulo_transaccion = $this->titulo_transaccion;
        $titulo_nuevomovimiento = $this->titulo_nuevomovimiento;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar','titulo_cerrarCaja','titulo_nuevomovimiento','titulo_transaccion' ,'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $entidad          = 'Caja';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $titulo_nuevomovimiento = $this->titulo_nuevomovimiento;
        $ruta             = $this->rutas;
        $listCaja = Caja::listCaja();
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar','titulo_nuevomovimiento', 'ruta','listCaja'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $datosCaja = Caja::orderby('created_at','DESC')->take(1)->get();
        $ingresos = $datosCaja[0]->monto_cierre;

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Caja';
        $caja        = null;
        $formData       = array('caja.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('caja', 'formData', 'entidad', 'boton', 'listar','ingresos'));
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
            'fecha_horaApert'        => 'required|max:100',
            'hora_apertura'      => 'required|max:100',
            'monto_iniciado'    => 'required|max:100',
            'titulo'    => 'required|max:200'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $caja               = new Caja();
            $caja->titulo        = $request->input('titulo');
            $caja->descripcion        = $request->input('descripcion');
            $caja->fecha_horaApert        = $request->input('fecha_horaApert').":".$request->input('hora_apertura');
            $caja->monto_iniciado        = $request->input('monto_iniciado');
            $caja->estado        = 'A';//abierto
            $caja->persona_id        = Caja::getIdPersona();
            $caja->save();
        });
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
        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $datosCaja = Caja::orderby('created_at','DESC')->take(1)->get();
        $ingresos = $datosCaja[0]->monto_cierre;

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $caja        = Caja::find($id);
        $entidad        = 'Caja';

        $formData       = array('caja.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('caja', 'formData', 'entidad', 'boton', 'listar','ingresos'));
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
        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'fecha_horaApert'        => 'required|max:100',
            'monto_iniciado'    => 'required|max:100',
            'titulo'    => 'required|max:200'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $caja                 = Caja::find($id);
            $caja->titulo        = $request->input('titulo');
            $caja->descripcion        = $request->input('descripcion');
            $caja->fecha_horaApert        = $request->input('fecha_horaApert').":".$request->input('hora_apertura');
            $caja->monto_iniciado        = $request->input('monto_iniciado');
            $caja->estado        = 'A';//abierto
            $caja->persona_id        = Caja::getIdPersona();
            $caja->save();
            
        });
        return is_null($error) ? "OK" : $error;
    }

    public function cargarCaja($id, Request $request)
    {
        $result = DB::table('caja')->where('id', $id)->first();
        //calculos
        $ingresos =$result->monto_iniciado;
        $egresos=0;
        $diferencia =0;
        $saldo = Transaccion::getsaldo($id)->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }
        $diferencia= $ingresos-$egresos;

        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = "NO";
        $entidad        = 'Caja';
        $caja = Caja::find($id);
        
        if (isset($listarParam)) {
            $listar = $listarParam;
        }
       
        return view($this->folderview.'.cierrecaja')->with(compact('caja','listar','entidad', 'boton','diferencia'));
    }

    public function cerrarcaja(Request $request, $id)
    {        
        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'diferencia_monto'    => 'required|max:200'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $caja                 = Caja::find($id);
            $caja->descripcion        = $request->get('descripcion');
            $caja->fecha_horaCierre        = $request->input('fecha_horaApert').":".$request->input('hora_cierre');
            $caja->monto_cierre        = $request->get('monto_cierre');
            $caja->diferencia_monto        = $request->get('diferencia_monto');
            $caja->estado        = 'C';//cierre
            $caja->save();
            
        });
        return is_null($error) ? "OK" : $error;
    }

    //CONTROL DETALLE DE LA CAJA

    public function detalle($id, Request $request)
    {
        $result = DB::table('caja')->where('id', $id)->first();
        //calculos
        $ingresos =$result->monto_iniciado;
        $egresos=0;
        $diferencia =0;
        $saldo = Transaccion::getsaldo($id)->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }
        $diferencia= $ingresos-$egresos;
        $cboConcepto = array('' => 'Todo') + Concepto::pluck('titulo', 'id')->all();

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $fecha             = Libreria::getParam('');
        $concepto_id             = Libreria::getParam(-1);
        $resultado        = Transaccion::listar($fecha, $concepto_id , $id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'FECHA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'MONTO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'CONCEPTO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DESCRIPCION', 'numero' => '1');

        $tituloNuevaTransaccion = $this->tituloNuevaTransaccion;
        $ruta             = $this->rutas;
        $inicio           = 0;
        $entidad ='Transaccion';
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, 1, 7, $entidad);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate(7);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.transaccion')->with(compact('lista', 'paginacion', 'entidad', 'cabecera', 'ruta', 'inicio', 'id','saldo','ingresos','egresos','diferencia','cboConcepto','tituloNuevaTransaccion'));
        }
        return view($this->folderview.'.transaccion')->with(compact('lista', 'entidad', 'id', 'ruta','saldo','ingresos','egresos','diferencia','cboConcepto','tituloNuevaTransaccion'));
    }

    //PARA REGISTRAR NUEVO MOVIMIENTO PARA GASTOS, AHORROS DESDE LA CAJA
    public function nuevomovimiento($id, Request $request)
    {
        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $caja = Caja::find($id);

        if (isset($listarParam)) {
            $listar = $listarParam;
        }

        $entidad        = 'Transaccion';
        $cboTipo        = [''=>'Seleccione']+ array('I'=>'Ingreso','E'=>'Egreso');
        $cboConceptos        = [''=>'Seleccione'];
        $boton          = 'Registrar Movimiento';
        return view($this->folderview.'.nuevomovimiento')->with(compact('caja', 'entidad', 'id','boton', 'listar','cboTipo','cboConceptos'));
    }



    public function registrarmovimiento(Request $request, $id)
    {

        $existe = Libreria::verificarExistencia($id, 'caja');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'dni'        => 'required|max:100'
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
       }

        $listar        = Libreria::getParam($request->input('listar'), 'NO');

        return is_null($error) ? "OK" : $error;
    }

    //para seleccionar concepto
    public function cargarselect($idselect, Request $request)
    {
        echo $idselect;
        $entidad = $request->get('entidad');
        $t = '';
        $tt = '';

        if($request->get('t') == ''){
            $t = '_';
            $tt = '2';
        }

        $retorno = '<select class="form-control input-sm" id="' . $t . $entidad . '_id" name="';
        $cbo = Concepto::select('id', 'titulo')
            ->where('tipo', '=', $idselect)
            ->get();
        $retorno .= '><option value="" selected="selected">Seleccione</option>';

        foreach ($cbo as $row) {
            $retorno .= '<option value="' . $row['id'] .  '">' . $row['titulo'] . '</option>';
        }
        $retorno .= '</select></div>';

        echo $retorno;
    }

}
