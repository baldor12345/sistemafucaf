<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\DistribucionUtilidades;
use App\Http\Controllers\Controller;

class DistUtilidadesController extends Controller
{


    protected $folderview      = 'app.distribucionutilidad';
    protected $tituloAdmin     = 'Distribución de Utilidades Anual';
    protected $tituloRegistrar = 'Distribución de utilidades';
  
    protected $rutas           = array('create' => 'distribucionutilidades.create', 
            'edit'   => 'distribucionutilidades.edit', 
            'search' => 'distribucionutilidades.buscar',
            'reporte' => 'distribucionutilidades.reporte',
            'index'  => 'distribucionutilidades.index',
        );
    /**
     * Método para generar combo provincia
     * @param  [type] $departamento_id [description]
     * @return [type]                  [description]
     */
    public function index()
    {
        $configuraciones = Configuraciones::all()->last();
        $entidad = 'Distribucion';
        $title = $this->tituloAdmin;
        $tituloRegistrar = $this->tituloRegistrar;
        $ruta = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad','configuraciones', 'title', 'tituloRegistrar', 'ruta'));
    }

    public function buscar(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = Configuraciones::all()->last();
        $pagina = $request->input('page');
        $filas = $request->input('filas');
       
        $lista = $resultado->get();
        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'COD. CLIENTE', 'numero' => '1');
        $cabecera[] = array('valor' => 'NOMBRE CLIENTE', 'numero' => '1');
        $cabecera[] = array('valor' => 'CAPITAL AHORRO S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '3');
        
        $ruta             = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion = $paramPaginacion['cadenapaginacion'];
            $inicio = $paramPaginacion['inicio'];
            $fin = $paramPaginacion['fin'];
            $paginaactual = $paramPaginacion['nuevapagina'];
            $lista = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta', 'titulo_vistaretiro','titulo_vistadetalleahorro','titulo_vistahistoricoahorro','idcaja','configuraciones'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function create(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = Configuraciones::all()->last();
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'Distribucion';
        $ruta = $this->rutas;
        $sumUBAcumulado = DistribucionUtilidades::sumUBDacumulado($request->input('anios'));
        $anio = $request->input('anios');
        $intereses = $sumUBAcumulado[0];
        $otros = $sumUBAcumulado[1];
        $gastosDUActual = gastosDUactual($anio);

        $int_pag_acum= $gastosDUActual[0];
        $otros_acumulados=$gastosDUActual[1];
        $gastadmacumulado = $gastosDUActual[2];
        
        $du_anterior=0;
        $gast_du_anterior=0;
        $utilidad_neta =(($intereses + $otros - $du_anterior) - ($gastadmacumulado + $int_pag_acum + $otros_acumulados - $gast_du_anterior ));
        $utilidad_dist = $utilidad_neta - 2*0.1*$utilidad_neta;

        $acciones_mensual=list_total_acciones_mes($anio);
       
        $anio_actual=0;
        $listasocios=0;
        $formData = array('distribucion.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
 
        return view($this->folderview.'.mant')->with(compact('intereses','otros','configuraciones','idcaja', 'gastadmacumulado', 'formData', 'entidad','ruta', 'otros_acumulados', 'listar','du_anterior', 'int_pag_acum','utilidad_dist','acciones_mensual','anio','anio_actual','listasocios'));
    }
   /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //Metodo para registrar deposito
    public function store(Request $request)
    {
        $caja_id = Caja::where("estado","=","A")->value('id');
        $caja_id = ($caja_id != "")?$caja_id:0;
        $error = null;
        if($caja_id >0){
            $listar = Libreria::getParam($request->input('listar'), 'NO');
            
            $validacion = Validator::make($request->all(),$reglas);
            if ($validacion->fails()) {
                return $validacion->messages()->toJson();
            }

            $error = DB::transaction(function() use($request, $caja_id){
              
                
            });

        }else{
            $error = "Caja no aperturada, asegurese de aperturar caja primero !";
        }
        return is_null($error) ? "OK" : $error;
    }


}
