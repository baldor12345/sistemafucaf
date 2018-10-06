<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Validator;
use App\Http\Requests;
use App\Menuoptioncategory;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CategoriaopcionmenuController extends Controller
{
    protected $folderview      = 'app.categoriaopcionmenu';
    protected $tituloAdmin     = 'Categoría opción menú';
    protected $tituloRegistrar = 'Registrar categoría';
    protected $tituloModificar = 'Modificar categoría';
    protected $tituloEliminar  = 'Eliminar categoría';
    protected $rutas           = array('create' => 'categoriaopcionmenu.create', 
            'edit'   => 'categoriaopcionmenu.edit', 
            'delete' => 'categoriaopcionmenu.eliminar',
            'search' => 'categoriaopcionmenu.buscar',
            'index'  => 'categoriaopcionmenu.index',
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
        $entidad          = 'Categoriaopcionmenu';
        $name             = Libreria::getParam($request->input('name'));
        $resultado        = Menuoptioncategory::listar($name);
        $lista            = $resultado->get();
        $cabecera         = array();
        $cabecera[]       = array('valor' => '#', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Nombre', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Orden', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Categoria', 'numero' => '1');
        $cabecera[]       = array('valor' => 'Posicion', 'numero' => '1');
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
        $entidad          = 'Categoriaopcionmenu';
        $title            = $this->tituloAdmin;
        $titulo_registrar = $this->tituloRegistrar;
        $ruta             = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'titulo_registrar', 'ruta'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $entidad             = 'Categoriaopcionmenu';
        $cboCategoria        = [''=>'Seleccione una categoría'] + Menuoptioncategory::pluck('name', 'id')->all();
        $categoriaopcionmenu = null;
        $cboPosition         = array('V'=>'Vertical','H' => 'Horizontal');
        $formData            = array('categoriaopcionmenu.store');
        $formData            = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Registrar'; 
        return view($this->folderview.'.mant')->with(compact('categoriaopcionmenu', 'formData', 'entidad', 'boton', 'cboCategoria', 'listar','cboPosition'));
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
                'name'                  => 'required|max:60',
                'menuoptioncategory_id' => 'nullable|integer|exists:menuoptioncategory,id,deleted_at,NULL',
                'order'                 => 'required|integer',
                'icon'                  => 'required'
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        }
        $error = DB::transaction(function() use($request){
            $categoriaopcionmenu                        = new Menuoptioncategory();
            $categoriaopcionmenu->name                  = $request->input('name');
            $categoriaopcionmenu->order                 = $request->input('order');
            $categoriaopcionmenu->icon                  = $request->input('icon');
            $categoriaopcionmenu->position                  = $request->input('position');
            $categoriaopcionmenu->menuoptioncategory_id = Libreria::obtenerParametro($request->input('menuoptioncategory_id'));
            $categoriaopcionmenu->save();
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
        $existe = Libreria::verificarExistencia($id, 'menuoptioncategory');
        if ($existe !== true) {
            return $existe;
        }
        $listar              = Libreria::getParam($request->input('listar'), 'NO');
        $categoriaopcionmenu = Menuoptioncategory::find($id);
        $entidad             = 'Categoriaopcionmenu';
        $cboCategoria        = [''=>'Seleccione una categoría'] + Menuoptioncategory::where('id', '<>', $id)->pluck('name', 'id')->all();
        $cboPosition         = array('V'=>'Vertical','H' => 'Horizontal');
        $formData            = array('categoriaopcionmenu.update', $id);
        $formData            = array('route' => $formData, 'method' => 'PUT', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton               = 'Modificar';
        return view($this->folderview.'.mant')->with(compact('categoriaopcionmenu', 'formData', 'entidad', 'boton', 'cboCategoria', 'listar','cboPosition'));
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
        $existe = Libreria::verificarExistencia($id, 'menuoptioncategory');
        if ($existe !== true) {
            return $existe;
        }
        $validacion = Validator::make($request->all(),
            array(
                'name'                  => 'required|max:60',
                'menuoptioncategory_id' => 'nullable|integer|exists:menuoptioncategory,id,deleted_at,NULL',
                'order'                 => 'required|integer',
                'icon'                  => 'required'
                )
            );
        if ($validacion->fails()) {
            return $validacion->messages()->toJson();
        } 
        $error = DB::transaction(function() use($request, $id){
            $categoriaopcionmenu                        = Menuoptioncategory::find($id);
            $categoriaopcionmenu->name                  = $request->input('name');
            $categoriaopcionmenu->order                 = $request->input('order');
            $categoriaopcionmenu->icon                  = $request->input('icon');
            $categoriaopcionmenu->position                  = $request->input('position');
            $categoriaopcionmenu->menuoptioncategory_id = Libreria::obtenerParametro($request->input('menuoptioncategory_id')); 
            $categoriaopcionmenu->save();
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
        $existe = Libreria::verificarExistencia($id, 'menuoptioncategory');
        if ($existe !== true) {
            return $existe;
        }
        $error = DB::transaction(function() use($id){
            $categoriaopcionmenu = Menuoptioncategory::find($id);
            $categoriaopcionmenu->delete();
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
        $existe = Libreria::verificarExistencia($id, 'menuoptioncategory');
        if ($existe !== true) {
            return $existe;
        }
        $listar = "NO";
        if (!is_null(Libreria::obtenerParametro($listarLuego))) {
            $listar = $listarLuego;
        }
        $modelo   = Menuoptioncategory::find($id);
        $mensaje = '<p class="text-inverse">¿Esta seguro de eliminar el registro "'.$modelo->name.'"?</p>';
        $entidad  = 'Categoriaopcionmenu';
        $formData = array('route' => array('categoriaopcionmenu.destroy', $id), 'method' => 'DELETE', 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        $boton    = 'Eliminar';
        return view('app.confirmarEliminar')->with(compact('modelo', 'formData', 'entidad', 'boton', 'listar','mensaje'));
    }
}
