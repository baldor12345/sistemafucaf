<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\DistribucionUtilidades;
use App\Http\Controllers\Controller;
use App\Configuraciones;
use App\Caja;
use App\Credito;
use App\Transaccion;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\DB;

class DistUtilidadesController extends Controller
{
    protected $folderview      = 'app.distribucionutilidad';
    protected $tituloAdmin     = 'Distribución de Utilidades Anual';
    protected $tituloRegistrar = 'Distribución de utilidades';
    protected $rutas           = array('create' => 'distribucion_utilidades.create', 
            'edit'   => 'distribucion_utilidades.edit', 
            'search' => 'distribucion_utilidades.buscar',
            'reporte' => 'distribucion_utilidades.reporte',
            'index'  => 'distribucion_utilidades.index',
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
        $anioactual = date('Y');
        $anios = array();
        $anioi =2008;
        for($anyo=$anioactual; $anyo >=$anioi;  $anyo --){
            $anios[$anyo] = $anyo;
        }

        return view($this->folderview.'.admin')->with(compact('entidad','configuraciones', 'title', 'tituloRegistrar', 'ruta','anios','anioactual'));
    }

    public function buscar(Request $request)
    {
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = Configuraciones::all()->last();
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $resultado = DistribucionUtilidades::listar("");

        $lista = $resultado->get();
        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'TITULO', 'numero' => '1');
        $cabecera[] = array('valor' => 'UTILIDAD DISTRIBUIBLE', 'numero' => '1');
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '3');
        
        $ruta = $this->rutas;
        if (count($lista) > 0) {
            $clsLibreria = new Libreria();
            $paramPaginacion = $clsLibreria->generarPaginacion($lista, $pagina, $filas, $entidad);
            $paginacion = $paramPaginacion['cadenapaginacion'];
            $inicio = $paramPaginacion['inicio'];
            $fin = $paramPaginacion['fin'];
            $paginaactual = $paramPaginacion['nuevapagina'];
            $lista = $resultado->paginate($filas);
            $request->replace(array('page' => $paginaactual));
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta','idcaja','configuraciones'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

    public function create(Request $request)
    {
        $anio = $request->input('anio');
        $caja = Caja::where("estado","=","A")->get();
        $idcaja = count($caja) == 0? 0: $caja[0]->id;
        $configuraciones = Configuraciones::all()->last();
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $entidad = 'Distribucion';
        $ruta = $this->rutas;
      
        $sumUBAcumulado = DistribucionUtilidades::sumUBDacumulado($anio);
        //$anio = date('Y') - 1; 
        $intereses = ($sumUBAcumulado[0]==null)?0:$sumUBAcumulado[0];
        $otros = $sumUBAcumulado[1];
        $gastosDUActual = DistribucionUtilidades::gastosDUactual($anio);

        $int_pag_acum= $gastosDUActual[0];
        $otros_acumulados= $gastosDUActual[1];
        $gastadmacumulado = $gastosDUActual[2];
        
        //$dist_u_anterior = DB::table('transaccion')->where(DB::raw('extract( year from fechai)'),'=',($anio-1))->get();
        
       $dist_u_anterior = DistribucionUtilidades::where(DB::raw('extract( year from fechai)'),'=',($anio-1))->get();

        $du_anterior= (count($dist_u_anterior)>0)?$dist_u_anterior[0]->ub_duactual: 0;
        $gast_du_anterior=(count($dist_u_anterior)>0)?$dist_u_anterior[0]->gastos_duactual: 0;
        $utilidad_neta =round((($intereses + $otros - $du_anterior) - ($gastadmacumulado + $int_pag_acum + $otros_acumulados - $gast_du_anterior )));
        $utilidad_dist = round($utilidad_neta - 2*0.1*$utilidad_neta, 1);

        $acciones_mensual=  DistribucionUtilidades::list_total_acciones_mes($anio)->get();
        $acciones_mes  =0;
        $indice1 = 0;
        $j1=12;
        for($i=1; $i<=12; $i++){
            if((($indice1<count($acciones_mensual))?$acciones_mensual[$indice1]->mes:"") == $i){
                $acciones_mes += $acciones_mensual[$indice1]->cantidad_mes * $j1;
                $j1--;
                $indice1++;
            }
        }
    
        $anio_actual=$anio;
        $formData = array('distribucion_utilidades.store');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
 
        return view($this->folderview.'.mant')->with(compact('intereses','otros','configuraciones','idcaja', 'gastadmacumulado', 'formData', 'entidad','ruta', 'otros_acumulados', 'listar','du_anterior', 'int_pag_acum','utilidad_dist','acciones_mensual','anio','anio_actual','listasocios','gast_du_anterior','acciones_mes','utilidad_neta'));
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
            $anio = $request->input('anio');
            $listar = Libreria::getParam($request->input('listar'), 'NO');
            $error = DB::transaction(function() use($request, $caja_id, $anio){
              $distribucion = new DistribucionUtilidades();
              $distribucion->gast_admin_acum = $request->input('gast_ad_acum');
              $distribucion->int_pag_acum = $request->input('int_pag_acum');
              $distribucion->otros_acum = $request->input('otros_acum');
              //$distribucion->ub_duactual = $request->input('ub_duactual');
              $distribucion->intereses = $request->input('intereses');
              $distribucion->utilidad_distribuible = $request->input('utilidad_distr');
              $distribucion->otros = $request->input('otros');
              //$distribucion->gastos_duactual = $request->input('gast_duactual');
              $distribucion->fechai = date($anio.'-01-01');
              $distribucion->fechaf =  date($anio.'-12-31');
              $distribucion->save();

              $num_socios = $request->input('numerosocios');
              for($i=0;$i<$num_socios;$i++){
                  $transaccion = new Transaccion();
                  $transaccion->usuario_id = Credito::idUser();
                  $transaccion->persona_id = $request->input('persona_id'.$i);
                  $transaccion->caja_id = $caja_id;
                  $transaccion->fecha = date('Y-m-d H:i:s');
                  $transaccion->concepto_id = 37; // distribucion d eutilidad
                  $transaccion->monto = 0;
                  $transaccion->utilidad_distribuida = $request->input('monto'.$i);
                  $transaccion->save();
              }


            });

        }else{
            $error = "Caja no aperturada, asegurese de aperturar caja primero !";
        }
        return is_null($error) ? "OK" : $error;
    }
}
