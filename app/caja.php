<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
                    ->orderBy('titulo', 'DSC');
    }
    
    //para evaluar el estado de la caja 
    public static function listCaja(){
        $results = DB::table('caja')->where('estado','=','A')->count();
        return $results;
    }

    public static function listIngresos($fechai, $fechaf)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.interes_ahorro) as deposito_ahorros"),
                        DB::raw("SUM(transaccion.cuota_parte_capital) as pagos_de_capital"),
                        DB::raw("SUM(transaccion.cuota_interes) as intereces_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.acciones_soles) as acciones")
                    )
                    ->whereBetween('transaccion.fecha', [$fechai, $fechaf])
                    ->groupBy('persona.id');
        return $results;
    }

    public static function listEgresos($fechai, $fechaf)
    {
        $results = DB::table('persona')
                    ->leftJoin('transaccion', 'transaccion.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.nombres as persona_nombres',
				        'persona.apellidos as persona_apellidos',
                        DB::raw("SUM(transaccion.monto_ahorro) as monto_ahorro"),
                        DB::raw("SUM(transaccion.monto_credito) as monto_credito"),
                        DB::raw("SUM(transaccion.interes_ahorro) as interes_ahorro")
                    )
                    ->whereBetween('transaccion.fecha', [$fechai, $fechaf])
                    ->groupBy('persona.id');
        return $results;
    }

    public static function listEgresos_por_concepto($fechai, $fechaf)
    {
        $results = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        'concepto.titulo as concepto_titulo',
				        'transaccion.monto as transaccion_monto'
                    )
                    ->whereBetween('transaccion.fecha', [$fechai, $fechaf])
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.titulo','!=','Retiro de ahorros')
                    ->where('concepto.titulo','!=','Crédito');
        return $results;
    }

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
}
