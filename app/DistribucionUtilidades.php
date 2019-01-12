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
    public function persona()
    {
        return $this->belongsTo('App\Persona', 'persona_id');
    }


    /**CALCULO DE LOS INGRESOS */

    //lista de ingresos por persona del mes actual
    public static function listIngresos($anio)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        DB::raw("SUM(transaccion.cuota_interes) as intereces_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher")
                    )
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->groupBy('persona.id');
        return $results;
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

    

}
