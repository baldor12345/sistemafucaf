<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DistribucionUtilidades extends Model
{
    use SoftDeletes;
    protected $table = 'distribucion_utilidades';
    protected $dates = ['deleted_at'];

    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    /*
        CONSULTA SQL EN POSTGRES
        SELECT * FROM acciones 
        INNER JOIN persona ON (acciones.persona_id = persona.id)
        INNER JOIN configuraciones  ON (acciones.configuraciones_id = configuraciones.id)
        WHERE persona.id= -1
        OR persona.codigo=''
        OR persona.nombres LIKE '%Bald%'
    */
    public function scopelistar($query, $titulo)
    {
        return $query->where(function($subquery) use($titulo)
                    {
                        if (!is_null($titulo)) {
                            $subquery->where('titulo', 'LIKE', '%'.$titulo.'%');
                        }
                    })
                    ->orderBy('fechai', 'DSC');
    }

    public function persona()
    {
        return $this->belongsTo('App\Persona', 'persona_id');
    }
    


    /**CALCULO DE LOS INGRESOS */

    //lista de ingresos por persona del mes actual
    public static function sumUBDacumulado($anio)
    {
        $resultsI1 = DB::table('transaccion')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        DB::raw("SUM(transaccion.cuota_interes) as intereses_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher")
                    )
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->groupBy(DB::raw('extract( year from transaccion.fecha)'))->get();
        $intereses = $resultsI1[0]->intereses_recibidos;
        $sum_otros = $resultsI1[0]->intereses_recibidos+ $resultsI1[0]->comision_voucher;
        
        $results2 = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        DB::raw("SUM(transaccion.monto) as mas_otros"),
                    )
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('concepto.titulo','!=','Compra de acciones')
                    ->where('concepto.titulo','!=','Venta de acciones')
                    ->where('concepto.titulo','!=','Comision Voucher')
                    ->where('concepto.titulo','!=','Deposito de ahorros')
                    ->where('concepto.titulo','!=','Pago de cuotas');
                    ->groupBy('concepto.tipo')->get();
        $sum_otros += $resultsI1[0]->mas_otros;
        
        return array($intereses, $sum_otros);
    }

    //lista de ingresos por concepto del mes actual

    public static function listIngresos_por_concepto($anio)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
				        'transaccion.monto as transaccion_monto'
                    )
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('concepto.titulo','!=','Compra de acciones')
                    ->where('concepto.titulo','!=','Venta de acciones')
                    ->where('concepto.titulo','!=','Comision Voucher')
                    ->where('concepto.titulo','!=','Deposito de ahorros')
                    ->where('concepto.titulo','!=','Pago de cuotas');
        return $results;
    }




    /**CALCULO DE LOS EGRESOS PARA EL CALCULO DE GASTOS*/
     //lista de egresos  del mes actual por persona
    public static function listEgresos($anio)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro"),
                        DB::raw("SUM(transaccion.interes_ahorro) as interes_ahorro"),
                        DB::raw("SUM(transaccion.otros_egresos) as otros_egresos")
                    )
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','E')
                    ->groupBy('persona.id');
        return $results;
    }


    //lista para calcular los gastos administrativos acumulados del año 
    public static function listEgresos_por_concepto($anio)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
				        'transaccion.monto as transaccion_monto'
                    )
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.titulo','!=','Retiro de ahorros')
                    ->where('concepto.titulo','!=','Crédito')
                    ->where('concepto.titulo','!=','Ganancia por accion')
                    ->where('concepto.titulo','!=','Saldo Deudor');
        return $results;
    }

    /*
        SELECT SUM(cantidad) AS cantidad_mes 
        from historial_accion 
        where  extract( year from fecha) = '2019' 
        GROUP BY extract( month from fecha) 
        ORDER  BY  extract( month from fecha) ASC;
     */
    //lista todal de acciones por mes 
    public static function list_total_acciones_mes($anio)
    {
        $results = DB::table('historial_accion')
                    ->select(
                        DB::raw("SUM(cantidad) as cantidad_mes"),
                        DB::raw("extract( year from fecha) as mes")
                    )
                    ->where(DB::raw('extract( year from fecha)'),'=',$anio)
                    ->groupBy(DB::raw('extract( month from fecha)'))
                    ->orderBy(DB::raw('extract( month from fecha)'), 'ASC');
        return $results;
    }

    //lista total de acciones por persona por mes 
    public static function list_por_persona($persona_id, $anio)
    {
        $rsesults = DB::table('persona')
                    ->join('historial_accion', 'historial_accion.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.nombres as persona_nombres'.
                        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(cantidad) as cantidad_mes"),
                        DB::raw("extract( year from fecha) as mes")
                    )
                    ->where('persona.id','=',$persona_id)
                    ->where(DB::raw('extract( year from fecha)'),'=',$anio)
                    ->orderBy(DB::raw('extract( month from fecha)'), 'ASC');
        return $results;
    }


}
