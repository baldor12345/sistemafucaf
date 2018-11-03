<?php

namespace App\Http\Controllers;
use Validator;
use App\Http\Requests;
use App\Concepto;
use App\Transaccion;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TransaccionController extends Controller
{
    protected $folderview      = 'app.transaccion';
    protected $tituloAdmin     = 'Transacciones realizadas';
    protected $tituloRegistrar = 'Registrar transaccion';
    protected $tituloModificar = 'Modificar transaccion';
    protected $tituloEliminar  = 'Eliminar transaccion';
    protected $rutas           = array('create' => 'transaccion.create', 
            'edit'     => 'transaccion.edit', 
            'delete'   => 'transaccion.eliminar',
            'search'   => 'transaccion.buscar',
            'index'    => 'transaccion.index',
            'permisos' => 'transaccion.obtenerpermisos',
            'operaciones' => 'transaccion.obteneroperaciones',
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
        $entidad          = 'Transaccion';
        $fecha             = Libreria::getParam($request->input('fecha'));
        $concepto_id             = Libreria::getParam($request->input('concepto_id'));
        $resultado        = Transaccion::listar($fecha, $concepto_id);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'FECHA', 'numero' => '1');
        $cabecera[]       = array('valor' => 'MONTO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'CONCEPTO', 'numero' => '1');
        $cabecera[]       = array('valor' => 'DESCRIPCION', 'numero' => '1');
        
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'titulo_modificar', 'titulo_eliminar', 'ruta'));
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
        $ingresos =0;
        $egresos=0;
        $diferencia =0;
        $entidad          = 'Transaccion';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $cboConcepto = array('' => 'Todo') + Concepto::pluck('titulo', 'id')->all();

        $saldo = Transaccion::getsaldo()->get();
        for($i=0; $i<count($saldo); $i++){
            if(($saldo[$i]->concepto_tipo)=="I"){
                $ingresos  += $saldo[$i]->monto; 
            }else if(($saldo[$i]->concepto_tipo)=="E"){
                $egresos += $saldo[$i]->monto;
            }
        }
        $diferencia= $ingresos-$egresos;

        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad','saldo' ,'cboConcepto','title', 'titulo_registrar', 'ruta','ingresos','egresos','diferencia'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $entidad      = 'Transaccion';
        $cboConcepto = [''=>'Seleccione Concepto'] + Concepto::pluck('titulo', 'id')->all();
        $transaccion  = null;
        $formData     = array('transaccion.store');
        $formData     = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton        = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('transaccion', 'formData', 'entidad', 'boton', 'cboConcepto', 'listar'));
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
        $validacion = Validator::make($request->all(),
            array(
                'name' => 'required|max:60'
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $tipousuario       = new Usertype();
            $tipousuario->name = $request->input('name');
            $tipousuario->save();
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
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar       = Libreria::getParam($request->input('listar'), 'NO');
        $tipousuario  = Usertype::find($id);
        $entidad      = 'Tipousuario';
        $cboCategoria = [''=>'Seleccione una categoría'] + Usertype::where('id', '<>', $id)->pluck('name', 'id')->all();
        $formData     = array('tipousuario.update', $id);
        $formData     = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton        = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('tipousuario', 'formData', 'entidad', 'boton', 'cboCategoria', 'listar'));
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
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'name' => 'required|max:60'
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $tipousuario       = Usertype::find($id);
            $tipousuario->name = $request->input('name');
            $tipousuario->save();

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
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $tipousuario = Usertype::find($id);
            $tipousuario->delete();
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
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Usertype::find($id);
        $entidad  = 'Tipousuario';
        $formData = array('route' => array('tipousuario.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar'));
    }

    /**
     * método para cargar view para asignación de permisos
     * @param  [type] $listarParam [description]
     * @param  [type] $id          [description]
     * @return [type]              [description]
     */
    public function obtenerpermisos($listarParam, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $entidad = 'Tipousuario';
        if (isset($listarParam)) {
            $listar = $listarParam;
        }
        $tipousuario = Usertype::find($id);
        return view($this->folderview.'.permisos')->with(compact('tipousuario', 'listar', 'entidad'));
    }

    /**
     * método para guardar asignación de permisos
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function guardarpermisos(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $estados       = $request->input('estado');
        $idopcionmenus = $request->input('idopcionmenu');
        $cantAux       = count($estados);
        $respuesta     = true;
        $error         = DB::transaction(function() use ($id, $idopcionmenus, $estados, $cantAux)
        {
            Permission::where('usertype_id', '=', $id)->delete();
            for ($i=0; $i < $cantAux; $i++) {
                $exito = true;
                if($estados[$i] === 'H'){
                    $permiso = new Permission();
                    $permiso->usertype_id = $id;
                    $permiso->menuoption_id = $idopcionmenus[$i];
                    $permiso->save();
                }
            }
        });
        return is_null($error) ? "OK" : $error;  
    }

    public function obteneroperaciones($listarParam, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        $entidad = 'Tipousuario';
        if (isset($listarParam)) {
            $listar = $listarParam;
        }
        $tipousuario = Usertype::find($id);
        return view($this->folderview.'.operaciones')->with(compact('tipousuario', 'listar', 'entidad'));
    }

    public function guardaroperaciones(Request $request, $id)
    {
        $existe = Libreria::verificarExistencia($id, 'usertype');
        if ($existe !== true) {
            return $existe;
        }
        $listar        = Libreria::getParam($request->input('listar'), 'NO');
        $estados       = $request->input('estado');
        $idoperacionmenus = $request->input('idoperacionmenu');
        $cantAux       = count($estados);
        $respuesta     = true;
        $error         = DB::transaction(function() use ($id, $idoperacionmenus, $estados, $cantAux)
        {
            PermisoOperacion::where('usertype_id', '=', $id)->delete();
            for ($i=0; $i < $cantAux; $i++) {
                $exito = true;
                if($estados[$i] === 'H'){
                    $permiso = new PermisoOperacion();
                    $permiso->usertype_id = $id;
                    $permiso->operacionmenu_id = $idoperacionmenus[$i];
                    $permiso->save();
                }
            }
        });
        return is_null($error) ? "OK" : $error;  
    }
    
    
    public function pdf(Request $request){
       
        $tipousuario = Usertype::where('name','like','%'.$request->input('descripcion').'%')->first();
        $pdf = new TCPDF();
        $pdf::SetTitle('Tipousuario');
        $pdf::AddPage();
        //$pdf::Image("http://localhost/juanpablo/dist/img/logo.jpg", 65, 7, 75, 15);
        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(50,10,"TIPO USUARIO ID",1,0,'C');
        $pdf::SetFont('helvetica','',15);
        $pdf::Cell(50,6,"".$tipousuario->id,1,0,'L');
        $pdf::Ln();
        $pdf::SetFont('helvetica','B',15);
        $pdf::Cell(50,10,"NOMBRE",0,0,'C');
        $pdf::SetFont('helvetica','',15);
        $pdf::Cell(50,6,"".$tipousuario->name,0,0,'L');
        $pdf::Ln();
        $pdf::Output('Tipousuario.pdf');
    }
    
    public function excel(Request $request){
       
        $tipousuario = Usertype::where('name','like','%'.$request->input('descripcion').'%')->first();
        
    }
}
