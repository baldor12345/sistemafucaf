<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detalle_ahorro extends Model
{
    use SoftDeletes;
    protected $table = 'detalle_ahorro';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'capital',
        'interes',
        'fecha_capitalizacion',
        'ahorros_id'
    ];

    public function scopelistar($query)
    {
        $results = DB::table('detalle_ahorro')
        ->orderBy('fecha_capitalizacion', 'ASC');
        return $results;
    }

    //listar por ahorro_id desde una fecha indicada hasta la actualidad 
    public function listardesde($fechai, $ahorro_id)
    {
        $results = DB::table('detalle_ahorro')
        ->where('fecha_capitalizacion','>=',''.$fechai)
        ->where('ahorros_id','=',$ahorro_id)
        ->orderBy('fecha_capitalizacion', 'ASC');
        return $results;
    }

    /* --CONSULTA SUMA DE CAPITAL E INTERES MENSUAL

SELECT 	PERSONA.ID, 
		SUM(DETALLE_AHORRO.CAPITAL) AS CAPITAL_MENSUAL, 
		SUM(DETALLE_AHORRO.INTERES) AS INTERES_MENSUAL
FROM PERSONA INNER JOIN AHORROS ON (PERSONA.ID = AHORROS.PERSONA_ID)
			LEFT JOIN DETALLE_AHORRO ON (AHORROS.ID = DETALLE_AHORRO.AHORROS_ID)
WHERE DETALLE_AHORRO.FECHA_CAPITALIZACION BETWEEN '2018-12-01' AND '2018-12-30'
GROUP BY (PERSONA.ID); 
DB::raw("DATE_FORMAT(detalle_ahorro.fecha_capitalizacion,'%M %Y') as meses")
DB::raw('extract( year from detalle_ahorro.fecha_capitalizacion) as anio')
*/

// Historico de capital + interes mensuales.. por año
    public static function listarhistorico($cliente_id, $anio)
    {
        $inicioanio = date("Y-m-d", strtotime($anio.'-01-01'));
        $finalanio = date("Y-m-d", strtotime($anio.'-12-31'));
        echo("inicio: ".$cliente_id);
        $results = DB::table('persona')
        ->Join('ahorros', 'persona.id', '=', 'ahorros.persona_id')
        ->leftJoin('detalle_ahorro', 'ahorros.id','=','detalle_ahorro.ahorros_id')
        ->select(
            DB::raw('sum(detalle_ahorro.capital) as capital_mensual'),
            DB::raw('sum(detalle_ahorro.interes) as interes_mensual'),
            DB::raw('extract( month from detalle_ahorro.fecha_capitalizacion) as mes')
        )
        ->where('persona.id','=',''.$cliente_id)
        ->whereBetween('detalle_ahorro.fecha_capitalizacion', [$inicioanio, $finalanio])
        ->groupBy('mes')
        ->orderBy('mes', 'ASC');
        return $results;
    }

    
}