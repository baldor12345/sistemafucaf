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

    public function scopelistar($query,$nombreAcreditado, $fecha, $estado){
        $results = DB::table('credito')
    ->leftJoin('persona', 'persona.id', '=', 'credito.persona_id')
    ->where('credito.estado','=',$estado)
    ->where('credito.fecha','>=',$fecha)
    ->where('persona.nombres','LIKE', '%'.$nombreAcreditado.'%')
    ->where('persona.apellidos','LIKE', '%'.$nombreAcreditado.'%')
    ->orderBy('credito.fecha', 'ASC');
        echo var_dump(json_encode($results));
        return $results;
       
    }
}
