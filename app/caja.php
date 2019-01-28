<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $persona_id = null;
        // Obtiene el objeto del Usuario Autenticado
        $user = Auth::user();
        // Obtiene el ID del Usuario Autenticado
        $id = Auth::id();
        $user = DB::table('user')->where('id', $id)->first();
        $persona_id=$user->persona_id;
        return $persona_id;
    }

    public function scopelistar($query, $titulo)
    {
        return $query->where(function($subquery) use($titulo)
                    {
                        if (!is_null($titulo)) {
                            $subquery->where('titulo', 'LIKE', '%'.$titulo.'%');
                        }
                    })
                    ->orderBy('fecha_horaApert', 'DSC');
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
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher")
                    )
                    ->where(DB::raw('extract( month from transaccion.fecha)'),'=',$month)
                    ->where(DB::raw('extract( year from transaccion.fecha)'),'=',$anio)
                    ->where('concepto.tipo','=','I')
                    ->where('transaccion.deleted_at',null)
                    ->groupBy('persona.id');
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
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher")
                    )
                    ->where('transaccion.fecha','<', $fechai)
                    ->where('concepto.tipo','=','I')
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
                    ->groupBy('persona.id');
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
