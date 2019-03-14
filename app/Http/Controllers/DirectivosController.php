<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Persona;
use App\Directivos;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Jenssegers\Date\Date;
use PDF;
use DateTime;

class DirectivosController extends Controller
{
    protected $folderview      = 'app.directivos';
    protected $tituloAdmin     = 'Lista de Directivos por Periodo';
    protected $tituloRegistrar = 'Registrar Nuevos Directivos';
    protected $tituloModificar = 'Modificar Directivos';
    protected $tituloEliminar  = 'Eliminar gasto';
    protected $titulo_detalle = 'Directivos del periodo: ';
    protected $rutas           = array('create' => 'directivos.create', 
            'edit'   => 'directivos.edit', 
            'delete' => 'directivos.eliminar',
            'search' => 'directivos.buscar',
            'index'  => 'directivos.index',
            'listdirectivos' =>'directivos.listdirectivos',
            'listpersonas'  =>'directivos.listpersonas'
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
        $entidad          = 'Directivos';
        $fechai             = Libreria::getParam($request->input('fechai'));
        $fechaf             = Libreria::getParam($request->input('fechaf'));
        $titulo             ="";
        $resultado        = Directivos::listar($titulo, $fechai, $fechaf);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Titulo', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Periodo Inicio', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Periodo Fin', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Estado', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Operaciones', 'numero' => '3');

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
        
        $titulo_modificar = $this->tituloModificar;
        $titulo_eliminar  = $this->tituloEliminar;
        $titulo_detalle   = $this->titulo_detalle;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'titulo_detalle','ruta','month'));
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
        $directivos_last = Directivos::All()->last();
        $day = date("d/m/Y");
        if(count($directivos_last) != 0){
            $periodo_fin = Date::parse($directivos_last->periodof )->format('d/m/Y');
        }else{
            $periodo_fin =null;
        }
        

        $entidad          = 'Directivos';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta','periodo_fin','day'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $directivos_last = Directivos::All()->last();
        $day = date("d/m/Y");
        if(count($directivos_last) != 0){
            $periodo_fin = Date::parse($directivos_last->periodof )->format('d/m/Y');
        }else{
            $periodo_fin = null;
        }
        
        $listar         = Libreria::getParam($request->input('listar'), 'NO');
        $entidad        = 'Directivos';
        $directivos        = null;
        $cboPresidente = array(0=>'Seleccione presidente...');
        $cboSecretario = array(0=>'Seleccione secretario...');
        $cboTesorero = array(0=>'Seleccione tesorero...');
        $cboVocal = array(0=>'Seleccione vocal...');
        $cboEstado        = array('A'=>'Activo','I'=>'Inactivo');
        $ruta = $this->rutas;
        $formData       = array('directivos.store');
        $formData       = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('directivos', 'formData', 'entidad', 'boton', 'listar','cboPresidente','cboSecretario','cboVocal','cboTesorero','ruta','cboEstado','directivos_last','day','periodo_fin'));
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
            'presidente_id'         => 'required',
            'periodoi'        => 'required',
            'periodof'      => 'required'
            );

        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $dir_moment = DB::table('directivos')->count();
            $codigo = (count($dir_moment) ==0)?0:$dir_moment;
            $directivos               = new Directivos();
            $codigo++;
            if(strlen($codigo) == 1){
                $directivos->titulo = "Periodo 000".$codigo;
            }
            if(strlen($codigo) == 2){
                $directivos->titulo = "Periodo 00".$codigo;
            }
            if(strlen($codigo) == 3){
                $directivos->titulo = "Periodo 0".$codigo;
            }
            if(strlen($codigo) == 4){
                $directivos->titulo = "Periodo ".$codigo;
            }
            $directivos->periodoi = $request->input('periodoi');
            $directivos->periodof = $request->input('periodof');
            $directivos->estado = $request->input('estado');
            $directivos->descripcion = $request->input('descripcion');
            $directivos->presidente_id = $request->input('presidente_id');
            $directivos->tesorero_id = $request->input('tesorero_id');
            $directivos->secretario_id = $request->input('secretario_id');
            $directivos->vocal_id = $request->input('vocal_id');
            $directivos->save();
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
        $existe = Libreria::verificarExistencia($id, 'directivos');
        if ($existe !== true) {
            return $existe;
        }
        $listar         = Libreria::getParam($request->input('listar'), 'NO');

        $cboPresidente = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboTesorero = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboSecretario = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboVocal = array('' => 'Seleccione') + Persona::pluck('nombres', 'id')->all();
        $cboEstado        = array('A'=>'Activo','I'=>'Inactivo');
        
        $directivos        = Directivos::find($id);
        $entidad        = 'directivos';
        $ruta = $this->rutas;
        $formData       = array('directivos.update', $id);
        $formData       = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton          = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('directivos', 'formData', 'entidad', 'boton', 'listar','cboPresidente','cboTesorero','cboSecretario','cboVocal','cboEstado','ruta'));
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
        $existe = Libreria::verificarExistencia($id, 'directivos');
        if ($existe !== true) {
            return $existe;
        }
        $reglas = array(
            'presidente_id'       => 'required',
            'tesorero_id'    => 'required'
        );
        $validacion = Validator::make($request->all(),$reglas);
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $directivos = Directivos::find($id);
            $directivos->periodoi = $request->input('periodoi');
            $directivos->periodof = $request->input('periodof');
            $directivos->estado = $request->input('estado');
            $directivos->descripcion = $request->input('descripcion');
            $directivos->presidente_id = $request->input('presidente_id');
            $directivos->tesorero_id = $request->input('tesorero_id');
            $directivos->secretario_id = $request->input('secretario_id');
            $directivos->vocal_id = $request->input('vocal_id');
            $directivos->save();

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
        $existe = Libreria::verificarExistencia($id, 'directivos');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $resultado = Directivos::obtenerid($id);
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
        $existe = Libreria::verificarExistencia($id, 'directivos');
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

    public function listpersonas(Request $request){
        $term = trim($request->q);
        if (empty($term)) {
            return \Response::json([]);
        }
        $tags = Persona::where("dni",'ILIKE', '%'.$term.'%')->orwhere("nombres",'ILIKE', '%'.$term.'%')->orwhere("apellidos",'ILIKE', '%'.$term.'%')->limit(5)->get();
        $formatted_tags = [];
        foreach ($tags as $tag) {
            if(trim($tag->tipo) == 'S'){
                $formatted_tags[] = ['id' => $tag->id, 'text' => $tag->nombres." ".$tag->apellidos];
            }else{
                //$formatted_tags[] = ['id'=> '', 'text'=>"seleccione socio"];
            }
        }

        return \Response::json($formatted_tags);
    }

    public function listdirectivos($persona_id, Request $request)
    {
        
        $directivos = DB::table('directivos')->where('id', $persona_id)->first();
        $estado = $directivos->estado;

        $entidad = "Directivos";
        $ruta             = $this->rutas;
        $inicio           = 0;
        //datos
        //presidente
        $presidente = Persona::where('id', $directivos->presidente_id)->first();

        //secretario
        $secretario = Persona::where('id', $directivos->secretario_id)->first();

        //tesorero
        $tesorero = Persona::where('id', $directivos->tesorero_id)->first();
        //vocal
        $vocal = Persona::where('id', $directivos->vocal_id)->first();
    
        return view($this->folderview.'.detalle')->with(compact('lista', 'entidad', 'persona_id', 'ruta','directivos','presidente','secretario','tesorero','vocal','estado'));
    }

    public function directivosPDF($id, Request $request)
    {    
        $directivos = DB::table('directivos')->where('id', $id)->first();
        //datos
        //presidente
        $presidente = Persona::where('id', $directivos->presidente_id)->first();

        //secretario
        $secretario = Persona::where('id', $directivos->secretario_id)->first();

        //tesorero
        $tesorero = Persona::where('id', $directivos->tesorero_id)->first();
        //vocal
        $vocal = Persona::where('id', $directivos->vocal_id)->first();

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


        $titulo = 'directivos_'.$directivos->titulo;
        $view = \View::make('app.directivos.directivosPDF')->with(compact('directivos','presidente','secretario','tesorero', 'vocal','month'));
        $html_content = $view->render();      
 
        PDF::SetTitle($titulo);
        PDF::AddPage('P', 'A4', 'es');
        PDF::SetTopMargin(0);
        //PDF::SetLeftMargin(40);
        //PDF::SetRightMargin(110);
        PDF::SetDisplayMode('fullpage');
        PDF::writeHTML($html_content, true, false, true, false, '');
        PDF::Output($titulo.'.pdf', 'I');
    }
}
