<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
class caja extends Model
{
    use SoftDeletes;
    protected $table = 'caja';
    protected $dates = ['deleted_at'];
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    public function persona(){
        return $this->belongsTo('App\Persona', 'persona_id');
    } 

    public static function getIdPersona()
    {
        $id = Auth::id();
        return $id;
    }

    public function scopelistar($query, $titulo)
    {
        return $query->where(function($subquery) use($titulo)
                    {
                        if (!is_null($titulo)) {
                            $subquery->where('titulo', 'ILIKE', '%'.$titulo.'%');
                        }
                    })
                    ->orderBy('fecha_horaapert', 'DSC');
    }
    
    //para evaluar el estado de la caja 
    public static function listCaja(){
        $results = DB::table('caja')->where('estado','=','A')->count();
        return $results;
    }

    //lista de ingresos por persona del mes actual
    public static function listIngresos($anio, $month)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.interes_ahorro) as deposito_ahorros"),
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro"),
                        DB::raw("SUM(transaccion.cuota_parte_capital) as pagos_de_capital"),
                        DB::raw("SUM(transaccion.cuota_interes) as intereces_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.acciones_soles) as acciones"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher"),
                        DB::raw("SUM(transaccion.rec_capital) as rec_capital")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    //->where('persona.estado','=','A')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id')
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }

    //lista de ingresos por persona asta el mes anterior
    public static function listIngresosastamesanterior($fecha)
    {
        $fechai = date('Y-m-d', strtotime($fecha));
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.interes_ahorro) as deposito_ahorros"),
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro"),
                        DB::raw("SUM(transaccion.cuota_parte_capital) as pagos_de_capital"),
                        DB::raw("SUM(transaccion.cuota_interes) as intereces_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.acciones_soles) as acciones"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher"),
                        DB::raw("SUM(transaccion.rec_capital) as rec_capital")
                    )
                    ->where('transaccion.fecha','<', $fechai)
                    ->where('concepto.tipo','=','I')
                    //->where('persona.estado','=','A')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }
    //lista de ingresos por persona asta el mes anterior
    public static function ingresos_mesanterior($anio)
    {
        // $fechai = date('Y-m-d', strtotime($fecha));
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.interes_ahorro) as deposito_ahorros"),
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro"),
                        DB::raw("SUM(transaccion.cuota_parte_capital) as pagos_de_capital"),
                        DB::raw("SUM(transaccion.cuota_interes) as intereces_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.acciones_soles) as acciones"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher"),
                        DB::raw("SUM(transaccion.rec_capital) as rec_capital")
                    )
                    ->where(DB::raw("extract(year from transaccion.fecha)"),'<=', $anio)
                    ->where('concepto.tipo','=','I')
                    //->where('persona.estado','=','A')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }

    //lista de ingresos por concepto del mes actual

    public static function listIngresos_por_concepto($month, $anio)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as transaccion_descrpcion'
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('concepto.id','!=',1)
                    ->where('concepto.id','!=',2)
                    ->where('concepto.id','!=',8)
                    ->where('concepto.id','!=',5)
                    ->where('concepto.id','!=',16)
                    ->where('concepto.id','!=',4)
                    ->where('concepto.id','!=',12)
                    ->where('concepto.id','!=',22)
                    ->where('concepto.id','!=',23)
                    ->where('transaccion.deleted_at',null);
        return $results;
    }

    //lista de ingresos por concepto aste el mes anterior 
    public static function listIngresos_por_concepto_asta_mes_anterior($fecha)
    {
        $fechai = date('Y-m-d', strtotime($fecha));
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as transaccion_descrpcion'
                    )
                    ->where('transaccion.fecha','<',$fechai)
                    ->where('concepto.tipo','=','I')
                    ->where('concepto.id','!=',1)
                    ->where('concepto.id','!=',2)
                    ->where('concepto.id','!=',8)
                    ->where('concepto.id','!=',5)
                    ->where('concepto.id','!=',16)
                    ->where('concepto.id','!=',4)
                    ->where('concepto.id','!=',12)
                    ->where('concepto.id','!=',22)
                    ->where('concepto.id','!=',23)
                    ->where('transaccion.deleted_at',null);
        return $results;
    }


    /**----------------------------------------------------- */
    //lista de egresos  del mes actual por persona
    public static function listEgresos($month, $anio)

    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro"),
                        DB::raw("SUM(transaccion.monto_credito) as monto_credito"),
                        DB::raw("SUM(transaccion.interes_ahorro) as interes_ahorro"),
                        DB::raw("SUM(transaccion.otros_egresos) as otros_egresos"),
                        DB::raw("SUM(transaccion.utilidad_distribuida) as utilidad_distribuida")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','E')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id')
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }

    //lista de egresos por persona asta el mes anterior 
    public static function listEgresos_asta_mes_anterior($fecha)

    {
        $fechai = date('Y-m-d', strtotime($fecha));
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro"),
                        DB::raw("SUM(transaccion.monto_credito) as monto_credito"),
                        DB::raw("SUM(transaccion.interes_ahorro) as interes_ahorro"),
                        DB::raw("SUM(transaccion.otros_egresos) as otros_egresos"),
                        DB::raw("SUM(transaccion.utilidad_distribuida) as utilidad_distribuida")
                    )
                    ->where('transaccion.fecha','<',$fechai)
                    ->where('concepto.tipo','=','E')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }

    //list de egresos del mes actual por concepto(gastos administrativos)
    public static function listEgresos_por_conceptoAdmin($anio, $month)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as comentario'
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.id','!=',6)
                    ->where('concepto.id','!=',3)
                    ->where('concepto.id','!=',17)
                    ->where('concepto.id','!=',10)
                    ->where('concepto.id', '!=', 37)
                    ->where('concepto.id','!=',15)
                    ->where('transaccion.tipo_egreso','=',1)
                    ->where('transaccion.deleted_at',null);
        return $results;
    }

    //list de egresos asta el es anterior por concepto
    public static function listEgresos_por_concepto_asta_mes_anteriorAdmin($fecha)
    {
        $fechai = date('Y-m-d', strtotime($fecha));
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as comentario'
                    )
                    ->where('transaccion.fecha','<' ,$fechai)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.id','!=',6)
                    ->where('concepto.id','!=',3)
                    ->where('concepto.id','!=',17)
                    ->where('concepto.id','!=',10)
                    ->where('concepto.id', '!=', 37)
                    ->where('transaccion.tipo_egreso','=',1)
                    ->where('transaccion.deleted_at',null);
        return $results;
    }


    //lista de egresos por concepto(Otros gastos de la empres financiera)

    public static function listEgresos_por_conceptoOthers($anio, $month)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as comentario'
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.id','!=',6)
                    ->where('concepto.id','!=',3)
                    ->where('concepto.id','!=',17)
                    ->where('concepto.id','!=',10)
                    ->where('concepto.id', '!=', 37)
                    ->where('transaccion.tipo_egreso','=',0)
                    ->where('transaccion.deleted_at',null);
        //echo "datos por concepto   ".$results->get();
        return $results;
    }

    //list de egresos asta el es anterior por concepto
    public static function listEgresos_por_concepto_asta_mes_anteriorOthers($fecha)
    {
        $fechai = date('Y-m-d', strtotime($fecha));
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as comentario'
                    )
                    ->where('transaccion.fecha','<' ,$fechai)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.id','!=',6)
                    ->where('concepto.id','!=',3)
                    ->where('concepto.id','!=',17)
                    ->where('concepto.id','!=',10)
                    ->where('concepto.id', '!=', 37)
                    ->where('transaccion.tipo_egreso','=',0)
                    ->where('transaccion.deleted_at',null);
        return $results;
    }


    //busqueda de cantidad de acciones por personas para actualizar las ganancias por mes
    public static function list_ganancia_acciones_persona()
    {
        $results = DB::table('acciones')
                    ->join('configuraciones', 'acciones.configuraciones_id', '=', 'configuraciones.id')
                    ->join('persona', 'acciones.persona_id', '=', 'persona.id')
                    ->select(
                        DB::raw("(COUNT(acciones.estado)*configuraciones.ganancia_accion) as ganancia_accion"),
                        'acciones.persona_id AS persona_id',
                        'acciones.concepto_id as concepto_id',
                        'persona.tipo as persona_tipo'
                    )
                    ->where('acciones.estado','=','C')
                    ->where('persona.tipo','=','S')
                    ->orWhere('persona.tipo', 'SC')
                    ->groupBy('persona_id','configuraciones.ganancia_accion','concepto_id','persona.tipo');
        return $results;
    }

    /**************************************************************+RESUMEN FINANCIERO POR MES******************************************************/
    //lista de cuotas de prestamos por persona
    /*
    SELECT 	persona.nombres AS persona_nombres,
				persona.apellidos AS persona_apellidos,
				SUM(transaccion.cuota_parte_capital) AS capital, 
				SUM(transaccion.cuota_interes) AS interes,
				SUM(transaccion.cuota_mora) AS mora,
				SUM(transaccion.comision_voucher) AS comision
FROM persona INNER JOIN transaccion ON (transaccion.persona_id = persona.id)
WHERE extract( month from transaccion.fecha) = 2
GROUP BY persona.id;
    */
    public static function listcoutasprestamo($anio, $month)
    {
        $results = DB::table('persona')
                    ->join('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.cuota_parte_capital) as pagos_de_capital"),
                        DB::raw("SUM(transaccion.cuota_interes) as intereces_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }

    //lista de acciones compradas en el mes en soles
    /*
    SELECT 	persona.nombres AS persona_nombres,
				persona.apellidos AS persona_apellidos,
				SUM(transaccion.acciones_soles) AS acciones
FROM persona INNER JOIN transaccion ON (transaccion.persona_id = persona.id)
WHERE extract( month from transaccion.fecha) = 1
GROUP BY persona.id;
    */
    public static function listacciones($anio, $month)
    {
        $results = DB::table('persona')
                    ->join('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.acciones_soles) as acciones")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('persona.estado','=','A')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }
    //lista de ahorros realizados en el mes seleccionado
    public static function listahorros($anio, $month)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.monto_ahorro) as deposito_ahorros")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }
    //lista de prestamos realizados en el mes seleccionado
    public static function listprestamos($month, $anio)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.monto_credito) as monto_credito")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','E')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }

    //otros ingresos
    public static function listotrosingresos($month, $anio)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as transaccion_descrpcion'
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('concepto.id','!=',1)
                    ->where('concepto.id','!=',2)
                    ->where('concepto.id','!=',8)
                    ->where('concepto.id','!=',5)
                    ->where('concepto.id','!=',16)
                    ->where('concepto.id','!=',4)
                    ->where('concepto.id','!=',12)
                    ->where('concepto.id','!=',23)
                    ->where('transaccion.deleted_at',null);
        return $results;
    }
    //otros egresos admin
    public static function listotrosegresosadmin($anio, $month)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as comentario'
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.id','!=',6)
                    ->where('concepto.id','!=',3)
                    ->where('concepto.id','!=',17)
                    ->where('concepto.id','!=',10)
                    ->where('concepto.id', '!=', 37)
                    ->where('concepto.id','!=',15)
                    ->where('transaccion.tipo_egreso','=',1)
                    ->where('transaccion.deleted_at',null);
        return $results;
    }

    public static function listotrosegresosothers($anio, $month)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
                        'transaccion.monto as transaccion_monto',
                        'transaccion.descripcion as comentario'
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.id','!=',6)
                    ->where('concepto.id','!=',3)
                    ->where('concepto.id','!=',17)
                    ->where('concepto.id','!=',10)
                    ->where('concepto.id', '!=', 37)
                    ->where('transaccion.tipo_egreso','=',0)
                    ->where('transaccion.deleted_at',null);
        //echo "datos por concepto   ".$results->get();
        return $results;
    }
    

    /**PARA DISTRIBUCUION DE UTILIDADES **************************************************** */
    public static function listIngresosActual($anio, $month)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        DB::raw("SUM(transaccion.interes_ahorro) as deposito_ahorros"),
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }
    
    public static function listIngresosTotal($anio, $month)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.interes_ahorro) as deposito_ahorros"),
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro"),
                        DB::raw("SUM(transaccion.cuota_parte_capital) as pagos_de_capital"),
                        DB::raw("SUM(transaccion.cuota_interes) as intereces_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.acciones_soles) as acciones"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'<=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'<=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
        return $results;
    }
    public static function listIngresos_por_concepto_hasta_mesactual($fecha)
    {
        $anio = date('Y', strtotime($fecha));
        $mes = date('m', strtotime($fecha));

        $fechai = date('Y-m-d', strtotime($fecha));
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'transaccion.monto as transaccion_monto'
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'<=',$mes)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'<=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('concepto.id','!=',1)
                    ->where('concepto.id','!=',2)
                    ->where('concepto.id','!=',8)
                    ->where('concepto.id','!=',5)
                    ->where('concepto.id','!=',16)
                    ->where('concepto.id','!=',4)
                    ->where('concepto.id','!=',12)
                    ->where('transaccion.deleted_at',null);
        return $results;
    }

    //estado de cuenta de los prestamos activos asta la+ fecha

    public static function listcoutas_pendientes($anio, $month)
    {
        $fecha_p = new DateTime($anio.'-'.$month.'-01');
        $fecha_p->modify('last day of this month');
        $day_last = $fecha_p->format('Y-m-d');

        $results = DB::table('persona')
                    ->join('credito', 'credito.persona_id', '=', 'persona.id')
                    ->join('cuota', 'credito.id', '=', 'cuota.credito_id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(cuota.parte_capital) as parte_capital")
                    )
                    ->where('cuota.estado','!=','1')
                    ->where('credito.estado','!=','1')
                    ->where('credito.deleted_at',null)
                    ->groupBy('persona.id')
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }


    public static function listacciones_asta_la_fecha($anio, $month)
    {
        $fecha_p = new DateTime($anio.'-'.$month.'-01');
        $fecha_p->modify('last day of this month');
        $day_last = $fecha_p->format('Y-m-d');

        $results = DB::table('persona')
                    ->join('acciones', 'acciones.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("COUNT(acciones.estado)*10 as acciones")
                    )
                    ->where('acciones.estado','C')
                    //->where('acciones.tipo','!=','I')
                    ->where('persona.estado','!=','I')
                    ->where('acciones.deleted_at',null)
                    ->groupBy('persona.id')
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }
    //lista de ahorros realizados en el mes seleccionado
    public static function listahorros_asta_la_fecha($anio, $month)
    {
        $fecha_p = new DateTime($anio.'-'.$month.'-01');
        $fecha_p->modify('last day of this month');
        $day_last = $fecha_p->format('Y-m-d');

        $results = DB::table('persona')
                    ->join('ahorros', 'ahorros.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        'ahorros.capital as deposito_ahorros'
                    )
                    ->where('ahorros.estado','P')
                    ->where('persona.estado','A')
                    ->where('ahorros.deleted_at',null);
        return $results;
    }



    /*
    SELECT 
				(COUNT(acciones.estado)*configuraciones.ganancia_accion) AS ganancia_accion,
				acciones.persona_id AS persona_id,
				acciones.concepto_id as concepto_id,
				persona.tipo as persona_tipo,
				acciones.caja_id as caja_id
    FROM acciones INNER JOIN configuraciones ON (acciones.configuraciones_id = configuraciones.id)
                                INNER JOIN persona ON (acciones.persona_id = persona.id)
    WHERE acciones.estado = 'C' AND (persona.tipo = 'S' OR persona.tipo ='SC')
    GROUP BY acciones.persona_id, configuraciones.ganancia_accion, acciones.concepto_id,persona.tipo, caja_id;

    consulta para ver la cantidad de acciones en soles en transaccion 
    de la persona requejo smit luis alberto:
    
    select 	persona.nombres as nombres,
        persona.apellidos as apellidos,
        transaccion.acciones_soles as acciones,
        transaccion.fecha as fecha_compra 
    from transaccion inner join  persona on  (transaccion.persona_id = persona.id)
    where transaccion.persona_id = 25 and transaccion.inicial_tabla = 'AC';

    select sum(acciones_soles) from transaccion where persona_id = 25 and inicial_tabla = 'AC';


    select estado, fechai, descripcion from acciones where persona_id = 25;

    */


/*
SELECT 	persona.nombres as persona_nombres,
				persona.apellidos as persona_apellidos,
				sum(transaccion.monto_ahorro) as monto_ahorro,
				sum(transaccion.monto_credito) as monto_credito,
				sum(transaccion.interes_ahorro) as interes_ahorro
FROM persona LEFT JOIN  transaccion ON (persona.id = transaccion.persona_id)
WHERE transaccion.fecha BETWEEN '2018-12-01' AND '2018-12-30'
GROUP BY (persona.id);

SELECT 	concepto.titulo as concepto_titulo,
				transaccion.monto as transaccion_monto
FROM concepto LEFT JOIN transaccion ON (concepto.id = transaccion.concepto_id)
WHERE concepto.tipo = 'E' AND concepto.titulo !='Retiro de ahorros' and concepto.titulo !='Crédito';
*/
public static function boot()
{
    parent::boot();

    static::created(function($marca)
    {

        $binnacle             = new Binnacle();
        $binnacle->action     = 'I';
        $binnacle->date      = date('Y-m-d H:i:s');
        $binnacle->ip         = Libreria::get_client_ip();
        $binnacle->user_id =  Auth::user()->id;
        $binnacle->table      = 'caja';
        $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
        $binnacle->recordid = $marca->id;
        $binnacle->save();
    });

    static::updated(function($marca)
    {
        $binnacle             = new Binnacle();
        $binnacle->action     = 'U';
        $binnacle->date      = date('Y-m-d H:i:s');
        $binnacle->ip         = Libreria::get_client_ip();
        $binnacle->user_id = Auth::user()->id;
        $binnacle->table      = 'caja';
        $binnacle->detail    =$marca->toJson(JSON_UNESCAPED_UNICODE);
        $binnacle->recordid = $marca->id;
        $binnacle->save();
    });
    static::deleted(function($marca)
    {
        $binnacle             = new Binnacle();
        $binnacle->action     = 'D';
        $binnacle->date      = date('Y-m-d H:i:s');
        $binnacle->ip         = Libreria::get_client_ip();
        $binnacle->user_id = Auth::user()->id;
        $binnacle->table      = 'caja';
        $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
        $binnacle->recordid = $marca->id;
        $binnacle->save();
    });
}

}
