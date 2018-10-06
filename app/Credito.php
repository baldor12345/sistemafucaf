<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credito extends Model
{
    use SoftDeletes;
    protected $table = 'credito';
    protected $dates = ['deleted_at'];
    
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
/*
    public function scopelistar($query, $fecha){

        return $query->where(function($subquery) use($fecha)
		            {
		            	if (!is_null($codigo)) {
		            		$subquery->where('fecha', 'LIKE', '%'.$fecha.'%');
		            	}
		            })
        			->orderBy('nombres', 'ASC');
    }*/

    public function scopelistar($query, $fecha, $estado){


        $sql = "SELECT CREDITO.ID, CREDITO.VALOR_CREDITO, CREDITO.FECHA, CREDITO.ESTADO, CREDITO.CANTIDAD_CUOTAS, PE.NOMBRES AS NOMBRES, PE.APELLIDOS AS APELLIDOS FROM CREDITO 
         LEFT JOIN PERSONA AS PE ON PE.ID = CREDITO.PERSONA_ID 
         WHERE CREDITO.FECHA >= ? AND CREDITO.ESTADO = ?";

echo var_dump($fecha);
echo var_dump($estado);

        $results = DB::select($sql,array($fecha, $estado));
        
        
/*
        $sql = "SELECT COMPETENCIA.ID, COMPETENCIA.NOMBRE FROM COMPETENCIA 
        LEFT JOIN COMPETENCIA_ALUMNO ON COMPETENCIA_ALUMNO.COMPETENCIA_ID = COMPETENCIA.ID
        WHERE COMPETENCIA.ESCUELA_ID = ? AND COMPETENCIA.ID NOT IN (SELECT COMPETENCIA_ID FROM COMPETENCIA_ALUMNO WHERE ALUMNO_ID = ?)";
        $results = DB::select($sql,array($escuela_id,$alumno_id));
        */
        //echo var_dump(json_encode($results));
        return $results;
       
    }
}
