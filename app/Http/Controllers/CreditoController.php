<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Credito;
use App\Persona;
use App\Detalle_cuotas;
use App\Transaccion;
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
        $fecha             = Libreria::getParam($request->input('fecha'));
        $estado             = Libreria::getParam($request->input('estado'));
        $nombreAcreditado             = Libreria::getParam($request->input('nombreAcr'));
        $nombreAcreditado = strtoupper($nombreAcreditado);
        $resultado        = Credito::listar($nombreAcreditado,$fecha, $estado);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto s/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cuotas', 'numero' => '1');
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
        $entidad          = 'Credito';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboEstado        = array(0=>'Pendientes', 1 => 'Cancelados');
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta', 'cboEstado' ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
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

        $error = DB::transaction(function() use($request){
            echo "resultado: ".$request->input('cantidad_meses');
            $credito       = new Credito();
            $credito->valor_credito = $request->input('valor_credito');
            $credito->cantidad_cuotas = $request->input('cantidad_cuotas');
            $credito->cantidad_meses = $request->input('cantidad_meses');
            $credito->comision = $request->input('comision');
            $credito->multa = 20;
            //$fecha =Libreria::getParam($request->input('fecha'));
            $credito->fecha = $request->input('fechacred');
            $credito->descripcion = $request->input('descripcion');
            $credito->estado = '0';
            $credito->persona_id = $request->input('idcl'); //$request->input('idpersona');
            $credito->pers_aval_id = $request->input('idavl');
            $credito->save();
           
        });
        return is_null($error) ? "OK" : $error;
    }

    public function rouNumber($numero, $decimales) { 
        $factor = pow(10, $decimales); 
        return (round($numero*$factor)/$factor);
    }

    public function detallecredito(Request $request, $idcredito){
        $resultado = Credito::obtenercredito($idcredito);
       $credito = $resultado[0];
        $entidad      = 'Credito';
       
        $fechacaducidad = Date::parse($credito->fecha)->format('Y/m/d');
        
        $fechacaducidad = date("Y-m-d",strtotime($fechacaducidad."+ ".$credito->cantidad_meses." month"));
        $lista = Detalle_cuotas::listar($idcredito)->get();
        return view($this->folderview.'.detallecredito')->with(compact('credito', 'entidad', 'lista','fechacaducidad','titulo_detalle'));
    }


    public function guardarcredito(Request $request){
        $error = DB::transaction(function() use($request){
            $credito       = new Credito();
            $credito->valor_credito = $request->get('valor_credito');
            $credito->cantidad_cuotas = $request->get('cantidad_cuotas');
            $credito->cantidad_meses = $request->get('cantidad_meses');
            $credito->comision = $request->get('comision');
            $credito->multa = 20;
            $credito->fecha = $request->get('fechacred');
            $credito->estado = '0';
            $credito->descripcion = $request->get('descripcion');
            $credito->persona_id = $request->get('idcl'); //$request->input('idpersona');
            $credito->pers_aval_id = $request->get('idavl');
            $credito->save();

            $montocredito =  $credito->valor_credito;
            $montorestante =  $credito->valor_credito;

            $numcuotas = $credito->cantidad_cuotas;
            $nummeses = $credito->cantidad_meses;
            $interes = ($credito->comision)/100;

            $cuota =  ($interes * $montocredito) / (1 - (pow(1/(1+$interes), $numcuotas)));
            $cuota =  $this->rouNumber($cuota, 2);
            $fecha_actual = date("Y-m-d");
            $i = 0;
            $interesAcumulado = 0.00;
            for($i=0;$i<$numcuotas; $i++){
                //sumo 1 mes
                $fecha_actual = date("Y-m-d",strtotime($fecha_actual."+ 1 month")); 
                $montInteres = $this->rouNumber($interes *$montorestante , 2); 
                $interesAcumulado +=  $montInteres; 

                $montCapital =$this->rouNumber(($cuota - $montInteres) , 2); 
                $montorestante = $this->rouNumber(($montorestante - $montCapital) , 2);

                $detalle_cuotas       = new Detalle_cuotas();
                $detalle_cuotas->capital = $montCapital;
                $detalle_cuotas->interes = $montInteres;
                $detalle_cuotas->fecha_pago = $fecha_actual;
                $detalle_cuotas->situacion = '0';//0=PENDIENTE; 1 = PAGADO; 2 = MOROSO
                $detalle_cuotas->credito_id = $credito->id;
                $detalle_cuotas->save();

            }
            $fechahora_actual = date("Y-m-d H:i:s");
            $transaccion = new Transaccion();
            $transaccion->credito_id = $credito->id;
            $transaccion->fecha = $fechahora_actual;
            $transaccion->save();
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
