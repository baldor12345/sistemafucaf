<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    //esto es una prueba



    /**CALCULO DE LOS INGRESOS */

    //lista de ingresos por persona del mes actual
    public static function sumUBDacumulado($anio)
    {
        $fechai = date($anio.'-01-01');
        $fechaf = date(($anio+1).'-01-31');

        $resultsI1 = DB::table('transaccion')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        DB::raw("SUM(transaccion.cuota_interes) as intereses_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher")
                    )
                    //S>where('transaccion.fecha','>=',$fechai)
                    ->where('transaccion.fecha','<=',$fechaf)
                    ->where('transaccion.deleted_at','=',null)
                    ->where('concepto.tipo','=','I')
                    ->groupBy('concepto.tipo')->get();
                    
                    
        $intereses = (count($resultsI1)<1)? 0 :  $resultsI1[0]->intereses_recibidos;
        $sum_otros =  (count($resultsI1)<1)? 0 : $resultsI1[0]->comision_voucher;
        
        $results2 = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        DB::raw("SUM(transaccion.monto) as mas_otros")
                    )
                    //->where('transaccion.fecha','>=',$fechai)
                    ->where('transaccion.fecha','<=',$fechaf)
                    ->where('transaccion.deleted_at','=',null)
                    ->where('concepto.tipo','=','I')
                    ->where('concepto.titulo','!=','Compra de acciones')
                    ->where('concepto.titulo','!=','Venta de acciones')
                    ->where('concepto.titulo','!=','Comision Voucher')
                    ->where('concepto.titulo','!=','Deposito de ahorros')
                    ->where('concepto.titulo','!=','Pago de cuotas')
                    ->groupBy('concepto.tipo')->get();
        $sum_otros += (count($results2)<1)? 0:  $results2[0]->mas_otros;
        
        return array($intereses, $sum_otros);
    }


    /**CALCULO DE LOS EGRESOS PARA EL CALCULO DE GASTOS*/
     //lista de egresos  del mes actual por persona
    public static function gastosDUactual($anio)
    {
        $fechai = date($anio.'-01-01');
        $fechaf = date(($anio+1).'-01-31');
        $results0 = DB::table('transaccion')
        ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
        ->select(
            DB::raw("SUM(transaccion.interes_ahorro) as interes_ahorro")
       
        )
        //->where('transaccion.fecha','>=',$fechai)
        ->where('transaccion.fecha','<=',$fechaf)
        ->where('transaccion.deleted_at','=',null)
        ->where('concepto.tipo','=','E')
        ->groupBy('concepto.tipo')->get();
$i_pag_acum =(count($results0)<1)?0: $results0[0]->interes_ahorro;


        $results1 = DB::table('transaccion')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                     
                        DB::raw("SUM(transaccion.otros_egresos) as otros_egresos")
                    )
                    //->where('transaccion.fecha','>=',$fechai)
                    ->where('transaccion.fecha','<=',$fechaf)
                    ->where('transaccion.deleted_at','=',null)
                    ->where('concepto.tipo','=','E')
                    ->where('transaccion.tipo_egreso','=', 0)
                    ->groupBy('concepto.tipo')->get();
        
        $otros_acum  = 0;
        $otros_acum += (count($results1)<1)?0: $results1[0]->otros_egresos;
                        
        $results2 = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        DB::raw("SUM(transaccion.monto) as gastos_adm ")
                    )
                   // ->where('transaccion.fecha','>=',$fechai)
                    ->where('transaccion.fecha','<=',$fechaf)
                    ->where('transaccion.deleted_at','=',null)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.titulo','!=','Retiro de ahorros')
                    ->where('concepto.titulo','!=','Crédito')
                    ->where('concepto.titulo','!=','Ganancia por accion')
                    ->where('concepto.titulo','!=','Saldo Deudor')
                    ->where('concepto.titulo', '!=', 'Distribución de utilidad')
                    ->where('transaccion.tipo_egreso','=', 1)
                    ->groupBy('concepto.tipo')->get();
        $g_adm_acum = (count($results2)<1)?0: $results2[0]->gastos_adm;

        return array($i_pag_acum, $otros_acum, $g_adm_acum);
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
                        DB::raw('extract( month from fecha) as mes')
                    )
                    ->where('deleted_at','=', null)
                    ->where(DB::raw('extract( year from fecha)'),'=',$anio)
                    ->groupBy(DB::raw('extract( month from fecha)'))
                    ->orderBy(DB::raw('extract( month from fecha)'), 'ASC');
        return $results;
    }

    public static function num_acciones_anio_anterior($anio){
        $results = DB::table('historial_accion')
        ->select(
            DB::raw("SUM(cantidad) as cantidad_total")
        )
        ->where('deleted_at','=', null)
        ->where(DB::raw('extract( year from fecha)'),'<',$anio);
       
    return $results;
    }

    //lista total de acciones por persona por mes 
    public static function list_por_persona($persona_id, $anio)
    {
       
        $results = DB::table('historial_accion')
                    ->select(
                        DB::raw("SUM(cantidad) as cantidad_mes"),
                        DB::raw("extract( month from fecha) as mes")
                    )
                    ->where('persona_id','=',$persona_id)
                    ->where('deleted_at','=',null)
                    ->where(DB::raw('extract( year from fecha)'),'=',$anio)
                    ->groupBy(DB::raw('extract( month from fecha)'))
                    ->orderBy(DB::raw('extract( month from fecha)'), 'ASC');

        return $results;
    }

    public static function list_enero($persona_id, $anio_anterior){
        $results = DB::table('historial_accion')
        ->select(
            DB::raw("SUM(cantidad) as cantidad_total")
        )
        ->where('persona_id','=',$persona_id)
        ->where('deleted_at','=',null)
        ->where(DB::raw('extract( year from fecha)'),'<=',$anio_anterior)
        ->groupBy(DB::raw('persona_id'));

        return $results;
    }

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
            $binnacle->table      = 'distribucion_utilidades';
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
            $binnacle->table      = 'distribucion_utilidades';
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
            $binnacle->table      = 'distribucion_utilidades';
            $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });
    }
}
