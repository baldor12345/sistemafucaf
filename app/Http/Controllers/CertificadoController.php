<?php

namespace App\Http\Controllers;
use Validator;
use App\Http\Requests;
use App\Persona;
use App\Caja;
use App\Certificado;
use App\Ahorros;
use App\Transaccion;
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use PDF;
use DateTime;

use Illuminate\Http\Request;

class CertificadoController extends Controller
{
    protected $folderview      = 'app.certificado';
    protected $tituloAdmin     = 'Certificados';
    protected $tituloRegistrar = 'Generar Nueva Lista';
    protected $tituloModificar = 'Modificar gasto';
    protected $tituloEliminar  = 'Eliminar gasto';
    protected $rutas           = array('create' => 'certificado.create', 
            'edit'   => 'certificado.edit', 
            'delete' => 'certificado.eliminar',
            'search' => 'certificado.buscar',
            'index'  => 'certificado.index',
            'cargarpagarcontribucion'   => 'certificado.cargarpagarcontribucion',
            'guardarpagarcontribucion'  => 'certificado.guardarpagarcontribucion',
            'reportecertificadoPDF'  => 'certificado.reportecertificadoPDF'
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
        $entidad          = 'Certificado';
        $fechai             = Libreria::getParam($request->input('fechai'));
        $fechaf             = Libreria::getParam($request->input('fechaf'));
        $resultado        = Certificado::listar($fechai,$fechaf,'P');
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombres', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Capital', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'N° Acciones', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Codigo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Semestre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Año', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Periodo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Certificado', 'numero' => '1');

        $cboMonth = array(1=>'Enero',2=>'Febrero',3=>'Marzo',4=>'Abril',5=>'Mayo',6=>'Junio',7=>'Julio',8=>'Agosto',9=>'Septiembre',10=>'Octubre',11=>'Noviembre',12=>'Diciembre');
        
        $caja = Caja::where("estado","=","A")->get();
        $idCaja = count($caja) == 0 ?0: $caja[0]->id;
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_pagarmulta = "Generar Reporte";
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta','cboMonth','idCaja','titulo_pagarmulta'));
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
        $caja_id = Caja::where("estado","=","A")->value('id');
        $idCaja = (count($caja_id) == 0)?0:$caja_id;
        $entidad          = 'Certificado';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','idCaja'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //anio
        $anioactual = date('Y');
        $cboAnios = array();
        $anioi =2008;
        for($anyo=$anioactual; $anyo >=$anioi;  $anyo --){
            $cboAnios[$anyo] = $anyo;
        }
        $cboMonth = array(1=>'Enero',
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
        $certificado_last = Certificado::All()->last();
        $day = date("Y-m-d");
        $month_now  = date("m");
        $fechaf = (count($certificado_last) != 0) ? Date::parse($certificado_last->fechaf )->format('Y-m-d') : null;
        $date_last = (count($certificado_last) != 0) ? Date::parse($certificado_last->fechaf )->format('Y-m-01') : null;
        
        $date_last = date("Y-m-d",strtotime($date_last."+ 1 month"));
        
        $month_first = intval(date("m",strtotime($date_last)));
        $month_last = intval($month_first)+5;

        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Certificado';
        $certificado        = null;
        $formData       = array('certificado.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Generar Certificados'; 
        return view($this->folderview.'.mant')->with(compact('certificado', 'formData','month_first','month_last', 'entidad', 'boton', 'listar','cboAnios','cboMonth','fechaf','day','certificado_last','month_now','date_last'));
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
        $reglas = array();

        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }

        $month1 = $request->input('month1');
        $month2 = $request->input('month2');
        $anio = $request->input('anio');

        $results = Certificado::listAccionesCertificado($month1, $month2, $anio);
        $lista = $results->get();

        $fechai =  $anio.'-'.$month1.'-05'; 
        $fechaf =  $anio.'-'.$month2.'-'.date('d');


        $error = DB::transaction(function() use($request, $lista, $fechai, $fechaf, $month2 ){
            $capital =0;
            $inicio =0;
            $fin =0;
            $num_acciones =0;
            $semestre = "";
            $estado ="P";
            foreach($lista as $value){
                $certificado               = new Certificado();
                $certificado->capital = ($value->cantidad_accion*$value->accion_precio);
                
                $ultimo_registro = Certificado::all()->last();
                $valor = (count($ultimo_registro) == 0)?0:$ultimo_registro->fin;
                $cod = (count($ultimo_registro) == 0)?1:($ultimo_registro->id+1);

                $certificado->inicio = ($valor==0)?1:($valor+1);
                $certificado->fin = ($valor+($value->cantidad_accion));
                $certificado->num_acciones = $value->cantidad_accion;
                if(strlen($cod) == 1){
                    $certificado->codigo = "000000".$cod;
                }
                if(strlen($cod) == 2){
                    $certificado->codigo = "00000".$cod;
                }
                if(strlen($cod) == 3){
                    $certificado->codigo = "0000".$cod;
                }
                if(strlen($cod) == 4){
                    $certificado->codigo = "000".$cod;
                }
                $certificado->semestre = $month2;
                $certificado->estado = 'P';
                $certificado->fechai = $fechai;
                $certificado->fechaf = $fechaf;
                $certificado->persona_id = $value->persona_id;
                $certificado->save();   
            }

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
        $existe = Libreria::verificarExistencia($id, 'gastos');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');

        $cboConcepto = array('18'=>'Contribucion de Certificado');
        
        $gastos        = Gastos::find($id);
        $entidad        = 'Gastos';
        $formData       = array('gastos.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off','cboConcepto');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('gastos', 'formData', 'entidad', 'boton', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'gastos');
        if ($existe !== true) {
            return $existe;
        }
      /*  $reglas = array(
            'codigo'       => 'required|max:20|unique:persona,codigo,'.$id.',id,deleted_at,NULL',
            'dni'       => 'required|max:20|unique:persona,dni,'.$id.',id,deleted_at,NULL',
            'nombres'    => 'required|max:100',
            'apellidos'    => 'required|max:100',
            'fecha_nacimiento'    => 'required|max:100',
            'sexo'    => 'required|max:100',
            'ocupacion'    => 'required|max:100',
            'tipo'    => 'required|max:5',
            'direccion'    => 'required|max:100',
            'ingreso_personal'    => 'required|max:20',
            'ingreso_familiar'    => 'required|max:20',
            'telefono_fijo'    => 'required|max:15',
            );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } */
        $error = DB::transaction(function() use($request, $id){
            $gastos = Gastos::find($id);

            $agstos->monto = $request->input('monto');
            $gastos->concepto = $request->input('concepto');
            $gastos->fecha = $request->input('fechag');
            $gastos->save();

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
        $existe = Libreria::verificarExistencia($id, 'gastos');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $resultado = Transaccion::obtenerid($id);
            $transacc = $resultado[0];
            $transaccion = Transaccion::find($transacc->id);
            $transaccion->delete();

            $gastos = Gastos::find($id);
            $gastos->delete();
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
        $existe = Libreria::verificarExistencia($id, 'gastos');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Gastos::find($id);
        $entidad  = 'Gastos';
        $formData = array('route' => array('gastos.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }


    public function cargarpagarcontribucion($id, $listarLuego){

        $caja_id = Caja::where("estado","=","A")->value('id');
        $cboCertificado = array('18'=>'Contribucion de Certificado');
        $entidad  = 'Certificado';

        $caja = Caja::where("estado","=","A")->get();
        $fecha_caja = count($caja) == 0? 0: Date::parse($caja[0]->fecha_horaApert)->format('Y-m-d');

        $ruta = $this->rutas;
        $titulo_pagarmulta = "Generar Certificado";
        return view($this->folderview.'.pagarcontribucion')->with(compact('entidad', 'ruta', 'titulo_pagarmulta','cboMulta','caja_id','id','cboCertificado','fecha_caja'));
    }

    public function guardarpagarcontribucion(Request $request)
    {
        $concepto_id = Libreria::getParam($request->input('concepto_id'));
        $monto = Libreria::getParam($request->input('monto'));
        $fecha_pago = Libreria::getParam($request->input('fecha_pago'));
        $caja_id = Libreria::getParam($request->input('caja_id'));
        $certificado_id = Libreria::getParam($request->input('certificado_id'));

        $cert = Certificado::find($certificado_id);
        $persona = Persona::find($cert->persona_id);

        $existe = Libreria::verificarExistencia($certificado_id, 'certificado');
        if ($existe !== true) {
            return $existe;
        }
        $res = null;
        $error = DB::transaction(function() use($request, $concepto_id, $monto, $fecha_pago, $caja_id, $certificado_id, $persona){

            $certificado            = Certificado::find($certificado_id);
            $certificado->estado = 'C';
            $certificado->save();

            $resultado = (new Ahorros())->getahorropersona(6);

            $ahorro_id=null;
            if(count($resultado) >0){
                $ahorro_actual = $resultado[0];
                $ahorro_actual->capital = $ahorro_actual->capital + $monto;
                $ahorro_actual->estado = 'P';
                $ahorro_actual->save();
                $ahorro_id = $ahorro_actual->id;
            }else{
                $ahorro = new Ahorros();
                $ahorro->capital = $monto;
                $ahorro->interes = 0;
                $ahorro->estado = 'P';
                $ahorro->fechai = $fecha_pago;
                $ahorro->persona_id = 6;
                $ahorro->save();
                $ahorro_id = $ahorro->id;
            }

            $transaccion = new Transaccion();
            $transaccion->fecha = $fecha_pago;
            $transaccion->monto =  $monto;
            $transaccion->monto_ahorro=  $monto;
            $transaccion->id_tabla = $ahorro_id;
            $transaccion->inicial_tabla = 'CE';//AH = INICIAL DE TABLA AHORROS
            $transaccion->concepto_id = 5;// concepto deposito de ahorros
            $transaccion->persona_id = 6;
            $transaccion->descripcion =  "Se ahorró S/.".$monto." de Contribucion por certificado de propiedad de acciones del socio: ".$persona->apellidos." ".$persona->nombres;
            $transaccion->usuario_id = Caja::getIdPersona();
            $transaccion->caja_id =  $caja_id;
            $transaccion->save();

        });
        
        $res = is_null($res) ? "OK" : $res;
        $respuesta = array($res, $certificado_id);
        return $respuesta;
    }

    public function reportecertificadoPDF($id){  

        $certificado = Certificado::find($id);
        $persona = Persona::find($certificado->persona_id);

        $month = array(1=>'Enero',
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

        $titulo = "Certificado_".$persona->nombres;
        $view = \View::make('app.certificado.reportecertificadoPDF')->with(compact('lista', 'id', 'fecha','certificado','persona','month'));
        $html_content = $view->render();

        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        PDF::SetLeftMargin(25);
        PDF::SetRightMargin(25);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }
}
