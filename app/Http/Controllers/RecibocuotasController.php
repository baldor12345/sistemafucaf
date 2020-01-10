<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Persona;
use App\Credito;
use App\Cuota;
use App\Caja;
use App\Configuraciones;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use DateTime;
class RecibocuotasController extends Controller
{

    protected $folderview = 'app.recibocuotas';
    protected $tituloAdmin = 'Cuotas a Pagar';
    protected $tituloPagoCuota = 'Pago de Cuota';
    protected $rutas = array('create' => 'recibocuotas.create', 
            'edit' => 'recibocuotas.edit',
            'delete' => 'recibocuotas.eliminar',
            'search' => 'recibocuotas.buscar',
            'index' => 'recibocuotas.index',
            'recibocuota' => 'recibocuotas.recibocuota',
            'generarecibopagocuotaPDF' => 'creditos.generarecibopagocuotaPDF',
            'vistapagocuota' => 'creditos.vistapagocuota',
            'aplicarmora' => 'recibocuotas.aplicarmora',
            'vistaaplicarmora' => 'recibocuotas.vistaaplicarmora',
            'vistasimulador' => 'recibocuotas.vistasimulador',
            'listsimulador' => 'recibocuotas.listsimulador',
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        $meses = array(
        '01'=>'Enero',
        '02'=>'Febrero',
        '03'=>'Marzo',
        '04'=>'Abril',
        '05'=>'Mayo',
        '06'=>'Junio',
        '07'=>'Julio',
        '08'=>'Agosto',
        '09'=>'Septiembre',
        '10'=>'Octubre',
        '11'=>'Noviembre',
        '12'=>'Diciembre');

        $anios = array();
        $anioInicio = 2007;
        $caja = Caja::where("estado","=","A")->get();
        $caja_id = count($caja) == 0? 0: $caja[0]->id;
        // $fecha_actual = count($caja) == 0?  date('Y-m-d'): date('Y-m-d',strtotime($caja[0]->fecha_horaapert));
        $fecha_actual = count($caja)>0?date('Y-m-d',strtotime($caja[0]->fecha_horaapert)): date('Y-m-d');
        
        $anioactual = date('Y',strtotime($fecha_actual));
        $mesactual = date('m',strtotime($fecha_actual));
        
        for($anyo=$anioactual; $anyo>=$anioInicio; $anyo --){
            $anios[$anyo] = $anyo;
        }
        $entidad = 'ReciboCuota';
        $title = $this->tituloAdmin;
        $tituloPagoCuota = $this->tituloPagoCuota;
        $ruta = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'tituloPagoCuota', 'ruta', 'meses', 'anios','anioactual','mesactual', 'fecha_actual'));
    }

    /**
     * Mostrar el resultado de búsquedas
     * 
     * @return Response 
     */
    public function buscar(Request $request)
    {
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $entidad = 'ReciboCuota';
        $anio = Libreria::getParam($request->input('anio'));
        $mes = Libreria::getParam($request->input('mes'));
        $fecha_actual = Libreria::getParam($request->input('fecha_recibocuotas'));
        
        $nombre = Libreria::getParam($request->input('nombres'));
        $caja = Caja::where('estado','=','A')->where('deleted_at','=', null)->get();
        $lista = null;
        $resultado= null;
        if(count($caja)> 0){
            $resultado  = Cuota::listarCuotasAlafecha($anio,$mes, $nombre);
            $lista = $resultado->get();
        }else{
            $lista = array();
        }
        
        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'SOCIO/CLIENTE', 'numero' => '1');
        $cabecera[] = array('valor' => 'N° CUOTA', 'numero' => '1');
        $cabecera[] = array('valor' => 'MONTO S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'INTERES MORA S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'TOTAL s/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'ESTADO', 'numero' => '1');
        $cabecera[] = array('valor' => 'FECHA', 'numero' => '1');
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '3');
        
        $tituloPagoCuota = $this->tituloPagoCuota;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'tituloPagoCuota', 'ruta', 'fecha_actual'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad','fecha_actual'));
    }
    public function vistaaplicarmora(Request $request, $id_cuota){
        $cuota = Cuota::find($id_cuota);
        $credito = Credito::find($cuota->credito_id);
        $persona = Persona::find($credito->persona_id);
        $configuraciones = Configuraciones::all()->last();
        $fecha_mora = $request->get('fecha_iniciomora');
        $entidad = 'ReciboCuota';
        $listar = Libreria::getParam($request->input('listar'), 'NO');
        $ruta = $this->rutas;
        $formData = array('recibocuotas.aplicarmora');
        $formData = array('route' => $formData, 'class' => 'form-horizontal', 'id' => 'formMantenimiento'.$entidad, 'autocomplete' => 'off');
        
        return view($this->folderview.'.mant')->with(compact('cuota', 'formData', 'entidad', 'listar', 'configuraciones','ruta', 'persona', 'credito','fecha_mora'));
    }
    public function aplicarmora2(Request $request){
        $id_cuota = $request->input('id_cuota');
        $monto_mora = $request->input('monto_cuota');
       
        $error = DB::transaction(function() use($monto_mora, $id_cuota){
            $cuota = Cuota::find($id_cuota);
            $cuota->interes_mora = rouNumber($monto_mora, 7);
            $cuota->estado = 'm';
            $cuota->save();
        });
        return is_null($error) ? "OK" : $error;
    }

    public function aplicarmora(Request $request){
        $id_cuota = $request->input('id_cuota');
        
        $error = DB::transaction(function() use($request, $id_cuota){
            $cuota = Cuota::find($id_cuota);
            // $cuota->fecha_iniciomora =  $request->get('fechamora')." ".date('H:i:s');
            $cuota->fecha_iniciomora =  $cuota->fecha_programada_pago;
            $cuota->estado = 'm';
            $cuota->tasa_interes_mora = $request->get('porcentaje_mora');
            $cuota->save();
        });
        return is_null($error) ? "OK" : $error;
    }
    public function rouNumber($numero, $decimales) { 
        $factor = pow(10, $decimales); 
        return (round($numero*$factor)/$factor);
    }

    public function vistasimulador(){
        $caja = Caja::all()->last();
        $fecha_simulacion = date('Y-m',strtotime($caja->fecha_horaapert))."-01";
        $fecha_simulacion = date("Y-m",strtotime($fecha_simulacion."+ 1 month")); 

        $entidad = 'Simulador';
        $ruta = $this->rutas;
        return view($this->folderview.'.vistasimulador')->with(compact('entidad','ruta','fecha_simulacion'));
    }

    public function listsimulador(Request $request){
        
        $pagina = $request->input('page');
        $filas = $request->input('filas');
        $entidad = 'Simulador';
        // $fecha_simulador = $request->input('fecha_simulador');

        $nombre = $request->get('nombres');
        $fecha_simulador = $request->input('fecha_simulador').'-01';
        $fecha_actual = $fecha_simulador;
        $anio = date('Y', strtotime( $fecha_simulador ));
        $mes = date('m', strtotime( $fecha_simulador ));
       
        $resultado  = Cuota::listarCuotasAlafecha($anio,$mes, $nombre);
        $lista = $resultado->get();

        $Total = 0;
        $interes_total = 0;
        $interes_mora_total = 0;
        $capital_total = 0;
        foreach ($lista as $key => $value) {
            $interes_ganado =0;
            if($value->fecha_iniciomora != null){
                $fecha_fin =null;
                if($value->fecha_pago == null){
                    $fecha_fin= date("Y-m-d", strtotime($fecha_actual));
                }else{
                    $fecha_fin= date("Y-m-d", strtotime($value->fecha_pago));
                }
                $anio_menor= date("Y", strtotime($value->fecha_iniciomora));
                $mes_menor= date("m", strtotime($value->fecha_iniciomora));
                $anio_mayor= date("Y", strtotime($fecha_fin));
                $mes_mayor= date("m", strtotime($fecha_fin));
                $num_meses = 0;

                if($anio_mayor == $anio_menor){
                    $num_meses = $mes_mayor - $mes_menor;
                }else if($anio_mayor > $anio_menor){
                    $diferencia_anios = $anio_mayor - $anio_menor;
                    $num_meses = 12 - $mes_menor + (12 * ($diferencia_anios - 1)) + $mes_mayor;
                }
                
                if($num_meses>0){
                    $interes_ganado = $num_meses*($value->tasa_interes_mora/100) * ($value->parte_capital + $value->saldo_restante);
                }
            }
            $Total +=  round($value->parte_capital +  $value->interes + $interes_ganado, 1);
            $interes_total +=  round( $value->interes, 1);
            $capital_total +=  round( $value->parte_capital, 1);
            $interes_mora_total +=  round( $interes_ganado, 1);
        }

        $Total = $interes_total + $interes_mora_total + $capital_total;

        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'SOCIO/CLIENTE', 'numero' => '1');
        $cabecera[] = array('valor' => 'N° CUOTA', 'numero' => '1');
        $cabecera[] = array('valor' => 'MONTO S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'INTERES MORA S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'TOTAL s/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'ESTADO', 'numero' => '1');
        $cabecera[] = array('valor' => 'FECHA', 'numero' => '1');
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
            return view($this->folderview.'.listsimulador')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'ruta','fecha_actual','Total','interes_total', 'interes_mora_total','capital_total'));
        }
        return view($this->folderview.'.listsimulador')->with(compact('lista', 'entidad'));
    }

}
