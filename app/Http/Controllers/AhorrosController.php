<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Http\Requests;
use App\Ahorros;
use App\Concepto;
use App\Caja;
use App\Persona;
use App\Transaccion;
use App\Credito;
use App\configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AhorrosController extends Controller
{
    protected $folderview      = 'app.ahorros';
    protected $tituloAdmin     = 'Ahorros';
    protected $tituloRegistrar = 'Registrar ahorro';
    protected $tituloModificar = 'Modificar ahorro';
    protected $tituloEliminar  = 'Eliminar ahorro';
    protected $titulo_verahorro  = 'Detalles de ahorro';
    protected $titulo_retirar  = 'Retirar ahorro';
    protected $rutas           = array('create' => 'ahorros.create', 
            'edit'   => 'ahorros.edit', 
            'delete' => 'ahorros.eliminar',
            'search' => 'ahorros.buscar',
            'index'  => 'ahorros.index',
            'verahorro' => 'ahorros.verahorro',
            'retirar' =>'ahorros.retirar',
            'retiro' => 'ahorros.retiro'
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
        $entidad          = 'Ahorros';
        $fecha             = Libreria::getParam($request->input('fecha'));
        $nombres             = Libreria::getParam($request->input('nombres'));
        $resultado        = Ahorros::listar($nombres, $fecha);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Cliente', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Monto S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de deposito', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fecha de retiro', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_verahorro = $this->titulo_verahorro;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'titulo_verahorro'));
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
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();

        $entidad          = 'Ahorros';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad','idcaja','configuraciones', 'title', 'titulo_registrar', 'ruta'));
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

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Ahorros';
        $ahorros        =  null;
        $dni = null;
        $idopcion = null;
        $ruta             = $this->rutas;
        $resultado      = Concepto::listar('I');
        $cboConcepto  =array(5=>'Deposito de ahorros');// Concepto::pluck('titulo', 'id')->all();
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
    public function store(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $msjeah = null;
        if(count($caja) >0){
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

            $error = DB::transaction(function() use($request){
                $ahorros = new Ahorros();

                $ahorros->importe = $request->input('importe');
                $ahorros->fecha_deposito = $request->input('fecha_deposito');
            // $fecha_fin = date("Y-m-d",strtotime($ahorros->fecha_inicio."+ ".$ahorros->periodo." month"));
                $ahorros->interes = $request->input('interes');
                $ahorros->persona_id = $request->input('persona_id');
                $ahorros->descripcion = $request->input('descripcion');
                $ahorros->estado = 'P';
                $ahorros->save();
                //Guardar en tabla transacciones **********
                $caja = Caja::where("estado","=","A")->get();
                $fechahora_actual = date("Y-m-d H:i:s");
                $idconcepto = $request->input('concepto');

                $transaccion = new Transaccion();
                $transaccion->fecha = $ahorros->fecha_deposito;
                $transaccion->monto = $ahorros->importe;
                $transaccion->id_tabla = $ahorros->id;
                $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccion->concepto_id = $idconcepto;
                $transaccion->descripcion = $ahorros->descripcion;
                $transaccion->persona_id = $ahorros->persona_id;
                $transaccion->usuario_id = Credito::idUser();
                $transaccion->caja_id =  $caja[0]->id;
                $transaccion->save();
            });
            $msjeah = $error;
        }else{
            $msjeah = "Caja no aperturada, aperture primero. !";
        }
            return is_null($msjeah) ? "OK" : $msjeah;
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
                $ahorros->descripcion = $request->input('descripcion');
                $ahorros->save();
                /// REGISTRO EN CAJA
                $idconcepto = $request->input('concepto');

                $list = Transaccion::getTransaccion($id,'AH');
                $transaccion = Transaccion::find($list[0]->id);
                $transaccion->monto = $ahorros->importe;
                $transaccion->id_tabla = $ahorros->id;
                $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
                $transaccion->concepto_id = $idconcepto;
                $transaccion->descripcion = $ahorros->descripcion;
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
    public function retirar($id, $listarLuego){
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
        $formData = array('route' => array('ahorros.retiro', $id), 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Retirar';
        return view('app.confirmarRetirar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));

    }
    public function retiro(Request $request){
        $id = $request->get('id_ahorro');
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $ahorros = Ahorros::find($id);
            $ahorros->estado = 'R';
            $ahorros->save();

            //Guardar en tabla transacciones **********
            $caja = Caja::where("estado","=","A")->get();
            $fechahora_actual = date("Y-m-d H:i:s");
            $idconcepto = 6;
            $interes_mes = $ahorros->interes;
            $monto_inicial = $ahorros->monto;
            //$periodo = $ahorros->periodo;
           // $monto_retiro = pow((100+$interes_mes)/100,$periodo)*$monto_inicial;

            $transaccion = new Transaccion();
            $transaccion->fecha = $fechahora_actual;
            $transaccion->monto = $monto_retiro;
            $transaccion->id_tabla = $ahorros->id;
            $transaccion->inicial_tabla = 'AH';//AH = INICIAL DE TABLA AHORROS
            $transaccion->concepto_id = $idconcepto;
            $transaccion->descripcion = "Retiro de S/. ".$monto_retiro." de ahorros";
            $transaccion->persona_id = $ahorros->persona_id;
            $transaccion->usuario_id = Ahorros::idUser();
            $transaccion->caja_id =  $caja[0]->id;
            $transaccion->save();

        });
        return is_null($error) ? "OK" : $error;
    }
    public function rouNumber($numero, $decimales) { 
        $factor = pow(10, $decimales); 
        return (round($numero*$factor)/$factor);
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
