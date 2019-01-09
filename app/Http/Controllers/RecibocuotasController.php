<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Persona;
use App\Credito;
use App\Cuota;
use App\Librerias\Libreria;
use App\Http\Controllers\Controller;

class RecibocuotasController extends Controller
{

    protected $folderview = 'app.recibocuotas';
    protected $tituloAdmin = 'Cuotas a Pagar';
    protected $tituloPagoCuota = 'Pago de Cuota';
    protected $rutas = array('create' => 'recibocuotas.create', 
            'edit'   => 'recibocuotas.edit',
            'delete' => 'recibocuotas.eliminar',
            'search' => 'recibocuotas.buscar',
            'index'  => 'recibocuotas.index',
            'recibocuota'  => 'recibocuotas.recibocuota',
            'generarecibopagocuotaPDF' => 'creditos.generarecibopagocuotaPDF',
            'vistapagocuota' => 'creditos.vistapagocuota',
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
    public function index()

    {
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
        $anioactual = explode('-',date('Y-m-d'))[0];
        $mesactual = explode('-',date('Y-m-d'))[1];
        for($anyo=$anioactual; $anyo>=$anioInicio; $anyo --){
            $anios[$anyo] = $anyo;
        }

        $entidad = 'ReciboCuota';
        $title = $this->tituloAdmin;
        $tituloPagoCuota = $this->tituloPagoCuota;
        $ruta = $this->rutas;
        return view($this->folderview.'.admin')->with(compact('entidad', 'title', 'tituloPagoCuota', 'ruta', 'meses', 'anios','anioactual','mesactual'));
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
        $nombre = Libreria::getParam($request->input('nombres'));
        $resultado  = Cuota::listarCuotasAlafecha($anio,$mes, $nombre);
        $lista = $resultado->get();
        $cabecera = array();
        $cabecera[] = array('valor' => '#', 'numero' => '1');
        $cabecera[] = array('valor' => 'SOCIO/CLIENTE', 'numero' => '1');
        $cabecera[] = array('valor' => 'N° CUOTA', 'numero' => '1');
        $cabecera[] = array('valor' => 'MONTO S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'INTERES MORA S/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'TOTAL s/.', 'numero' => '1');
        $cabecera[] = array('valor' => 'ESTADO', 'numero' => '1');
        $cabecera[] = array('valor' => 'FECHA', 'numero' => '1');
        $cabecera[] = array('valor' => 'Operaciones', 'numero' => '2');
        
        $tituloPagoCuota = $this->tituloPagoCuota;
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
            return view($this->folderview.'.list')->with(compact('lista', 'paginacion', 'inicio', 'fin', 'entidad', 'cabecera', 'tituloPagoCuota', 'ruta'));
        }
        return view($this->folderview.'.list')->with(compact('lista', 'entidad'));
    }

   
}
