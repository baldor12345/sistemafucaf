<?php

namespace App;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;
    protected $table = 'persona';
    protected $dates = ['deleted_at'];
    
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */

    public function scopelistar($query, $codigo, $nombre, $dni, $tipo){

        return $query->where(function($subquery) use($codigo)
		            {
		            	if (!is_null($codigo)) {
		            		$subquery->where('codigo', 'ILIKE', '%'.$codigo.'%');
		            	}
		            })->where(function($subquery) use($nombre)
		            {
		            	if (!is_null($nombre)) {
		            		$subquery->where('nombres', 'ILIKE', '%'.$nombre.'%');
		            	}
                    })->where(function($subquery) use($dni)
                    {
                        if (!is_null($dni)) {
		            		$subquery->where('dni', 'ILIKE', '%'.$dni.'%');
		            	}
                    })->where(function($subquery) use($tipo)
                    {
                        if (!is_null($tipo)) {
		            		$subquery->where('tipo', 'ILIKE', '%'.$tipo.'%');
		            	}
                    })
                    ->orderBy('tipo', 'DSC')
                    ->orderBy('apellidos', 'ASC');
    }

    public static function personas($dni){
        return  Persona::where('dni','=',$dni)->get();
    }

    //lista para el control de asistencia
    public static function listSocioCliente(){
        return  Persona::where('tipo','=','SC')->orWhere('tipo','=','S')->get();
    }

    public static function estadoCuenta(){
        
    }

    //metodo para estado de cuenta
    public static function creditos_por_persona($persona_id)
    {
        $results = DB::table('persona')
                    ->join('credito', 'credito.persona_id', '=', 'persona.id')
                    ->join('cuota', 'cuota.credito_id', '=', 'credito.id')
                    ->select(
                        'credito.valor_credito as valor_credito',
                        'credito.periodo as periodo_credito',
                        'credito.tasa_interes as credito_interes',
                        'credito.fechai as fechai', 
					    'credito.fechaf as fechaf', 
					    'credito.pers_aval_id as persona_aval_id', 
                        DB::raw("COUNT(cuota.estado) as pedientes")
                    )
                    ->where('cuota.estado','=','0')
                    ->where('credito.persona_id','=',$persona_id)
                    ->groupBy('credito.valor_credito','credito.periodo','credito.tasa_interes', 'credito.fechai', 'credito.fechaf','credito.persona_id', 
					            'credito.pers_aval_id');
        return $results;
    }

    public static function moras_acumuladas_persona($persona_id){
        $results = DB::table('persona')
                    ->join('credito', 'credito.persona_id','=','persona.id')
                    ->join('cuota', 'cuota.credito_id','=','credito.id')
                    ->select(
                        DB::raw('COUNT(cuota.interes_mora) as cant_mora')
                    )
                    ->where('cuota.interes_mora','!=','0')
                    ->where('credito.persona_id','=',$persona_id)
                    ->groupBy('persona.id');
        return $results;
    }


    /*
     	SELECT SUM(capital) AS cantidad_ahorro FROM ahorros WHERE persona_id =1;
	
	SELECT SUM(cantidad) AS cantidad_accion FROM historial_accion WHERE persona_id =1;
	
	SELECT 	persona.nombres,
					persona.apellidos,
					credito.valor_credito, 
					credito.periodo, 
					credito.tasa_interes, 
					credito.fechai, 
					credito.fechaf, 
					credito.persona_id, 
					credito.pers_aval_id, 
					count(cuota.estado) as pendientes 
FROM 
			persona INNER JOIN credito on (credito.persona_id = persona.id)
							INNER JOIN cuota on (cuota.credito_id = credito.id)
where cuota.estado = '0' and persona_id = 1
GROUP BY persona.id, credito.valor_credito,credito.periodo,credito.tasa_interes, credito.fechai, credito.fechaf,credito.persona_id, 
					credito.pers_aval_id;
     */
}
