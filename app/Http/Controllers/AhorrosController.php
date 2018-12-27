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

class AhorrosController extends Controller
{
    protected $folderview      = 'app.ahorros';
    protected $tituloAdmin     = 'Ahorros';
    protected $tituloRegistrar = 'Registrar ahorro';
    protected $tituloModificar = 'Modificar ahorro';
    protected $tituloEliminar  = 'Eliminar ahorro';
    protected $titulo_verahorro  = 'Detalles de ahorro';
    protected $titulo_verdetalleahorro = 'Detalle de ahorros';
    protected $titulo_capitalizacion = 'Capitalizacion de ahorro';
    protected $titulo_retirar  = 'Retirar ahorro';
    protected $rutas           = array('create' => 'ahorros.create', 
            'edit'   => 'ahorros.edit', 
            'delete' => 'ahorros.eliminar',
            'search' => 'ahorros.buscar',
            'index'  => 'ahorros.index',
            'verahorro' => 'ahorros.verahorro',
            'retirar' =>'ahorros.retirar',
            'retiro' => 'ahorros.retiro',
            'buscarahorro' => 'ahorros.buscarahorro',
            'verdetalleahorro'=> 'ahorros.verdetalleahorro',
            'vercapitalizacion' => 'ahorros.vercapitalizacion',
            'listarcapitalizacion' => 'ahorros.listarcapitalizacion',
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

    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    /*
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
    }*/

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
        $cabecera[]       = array('valor' => 'COD. CLIENTE', 'numero' => '1');
        $cabecera[]       = array('valor' => 'NOMBRE CLIENTE', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DEPOSITO TOTAL S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_verahorro = $this->titulo_verahorro;
        $titulo_verdetalleahorro = $this->titulo_verdetalleahorro;
        $titulo_capitalizacion = $this->titulo_capitalizacion;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'titulo_verahorro','titulo_verdetalleahorro','titulo_capitalizacion'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function buscarahorro(Request $request)
    {
        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $estado = Libreria::getParam($request->input('estado'));
        $persona_id = Libreria::getParam($request->input('persona_id'));
        $entidad          = 'Ahorros';
        $entidad1 = "Detalleahorro";
        $resultado        = Ahorros::listardetalle($estado,$persona_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'FECHA DEP.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'MONTO DEP. S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'INTERES GANADO S/.', 'numero' => '1');
        $cabecera[]       = array('valor' => 'TOTAL S/.', 'numero' => '1');
        if($estado != 'P'){
            $cabecera[]       = array('valor' => 'FECHA RETIRO', 'numero' => '1');
        }
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_verahorro = $this->titulo_verahorro;
        $titulo_retirar = $this->titulo_retirar;
        $ruta             = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria     = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad1);
            $paginacion      = $paramPaginacion['cadenapaginacion'];
            $inicio          = $paramPaginacion['inicio'];
            $fin             = $paramPaginacion['fin'];
            $paginaactual    = $paramPaginacion['nuevapagina'];
            $lista           = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.listdetahorro')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad','entidad1', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta', 'titulo_verahorro','titulo_retirar','estado'));
        }
        return view($this->folderview.'.listdetahorro')->with(compact('lista', 'entidad'));
    }

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

    public function index()
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = configuraciones::all()->last();
        /*
        $capitales = Detalle_ahorro::listarhistorico(3,2018)->get();

        echo("<table>");
        echo("<thead><tr><th>Capital</th><th>Interes</th><th>Mes</th></tr></thead>");
        echo("<tbody>");
        echo($capitales);
        foreach ($capitales as $key => $value) {
            echo("<tr><td>".$value->capital_mensual."</td><td>".$value->interes_mensual."</td><td>".$value->mes."</td></tr>");
        }
        
        echo("</tbody>");
        echo("</table>");
        */

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
                $transaccion->descripcion = "";
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

    public function retirar($id, $listarLuego){
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $ahorro   = Ahorros::find($id);
        $entidad  = 'Ahorros';
        $entidad1 = "Detalleahorro";
        $ruta             = $this->rutas;


        $fecha_actual = date('Y-m-d'); 
        $fecha_dep = date("Y-m-d", strtotime($ahorro->fecha_deposito));
        $datosfac = explode("-", $fecha_actual);
        $datofdep = explode("-", $fecha_dep); 
        $fechadeposito = new DateTime (''.$datofdep[0].'-'.$datofdep[1].'-'.$datofdep[2]);
        $fechafinal =  new DateTime (''.$datosfac[0].'-'.$datosfac[1].'-'.$datosfac[2]);
        $diferencia = $fechadeposito-> diff($fechafinal);
        $cantmeses = ($diferencia->y * 12) + $diferencia->m;
        $interesganado =($cantmeses >= 1)? $ahorro->importe * pow((101/100),(int)$cantmeses):$ahorro->importe;
        $interesganado -= $ahorro->importe;
        $interesMostrar= round(  $interesganado , 2, PHP_ROUND_HALF_UP);
        $totalretiro = $interesMostrar + $ahorro->importe;

        //$formData = array('route' => array('ahorros.retiro', $id), 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        //$boton    = 'Retirar';
        return view($this->folderview.'.detretirar')->with(compact('ahorro','entidad','entidad1', 'listar','ruta','interesMostrar','totalretiro'));
    }
    public function retiro(Request $request){
        $id = $request->get('id_ahorro');
        $monto_retiro = $request->get('montototal');
        $existe = Libreria::verificarExistencia($id, 'ahorros');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id,$monto_retiro){
            $fechahora_actual = date("Y-m-d H:i:s");
            $ahorros = Ahorros::find($id);
            $ahorros->estado = 'R';
            $ahorros->fecha_retiro = $fechahora_actual;
            $ahorros->save();

            //Guardar en tabla transacciones **********
            $caja = Caja::where("estado","=","A")->get();
            $idconcepto = 6;
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

    public function verdetalleahorro($persona_id, Request $request){
        $existe = Libreria::verificarExistencia($persona_id, 'persona');
        if ($existe !== true) {
            return $existe;
        }
        $ruta             = $this->rutas;
        $persona = Persona::find($persona_id);
        $cboestado = array('P'=>'Depositos activos','R'=>'Depositos retirados');
        $entidad = "Ahorros";
        $entidad1 = "Detalleahorro";
        $boton          = 'Retirar';
        return view($this->folderview.'.detalles_ahorro')->with(compact('ruta','persona', 'entidad','entidad1', 'boton','cboestado'));
    }

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

    public function vercapitalizacion ($pers_id, Request $request){
        $ruta             = $this->rutas;
        $fecha_actual = date('Y-m-d'); 
        $datosfac = explode("-", $fecha_actual);
        $anioactual = $datosfac[0];
        $cboanio = array(''.$anioactual=>''.$anioactual,
        ''.($anioactual-1)=>''.($anioactual-1),
        ''.($anioactual-2)=>''.($anioactual-2),
        ''.($anioactual-3)=>''.($anioactual-3),
        ''.($anioactual-4)=>''.($anioactual-4),
        ''.($anioactual-5)=>''.($anioactual-5),);
        $titulo_capitalizacion = $this->titulo_capitalizacion;
        $entidad = "Detcapitalizacion";
        return view($this->folderview.'.det_capitalizacion')->with(compact('ruta','pers_id', 'entidad','cboanio','titulo_capitalizacion'));
    }
    public function listarcapitalizacion (Request $request){

        $pagina           = $request->input('page');
        $filas            = $request->input('filas');
        $persona_id = Libreria::getParam($request->input('pers_id'));
        $anio = Libreria::getParam($request->input('cboanio'));
        $entidad = "Detcapitalizacion";
        $resultado = Detalle_ahorro::listarhistorico($persona_id,$anio);
        echo($resultado->get());
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
            return view($this->folderview.'.listcapitalizacion')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta'));
        }
        return view($this->folderview.'.listcapitalizacion')->with(compact('lista', 'entidad'));
    }

 //metodo para generar voucher en pdf
 public function generareciboahorroPDF($id, $cant, $fecha, Request $request)
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
     PDF::SetTopMargin(20);
     PDF::SetLeftMargin(40);
     PDF::SetRightMargin(40);
     PDF::SetDisplayMode('fullpage');
     PDF::writeHTML($html_content, true, false, true, false, '');
     PDF::Output($titulo.'.pdf', 'D');
 }
}
?>