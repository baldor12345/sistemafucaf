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
    ->orwhere('credito.estado','=',$estado)
    ->orwhere('credito.fecha','>=',$fecha)
    ->orwhere('persona.nombres','LIKE', '%'.$nombreAcreditado.'%')
    ->orwhere('persona.apellidos','LIKE', '%'.$nombreAcreditado.'%')
    ->orderBy('credito.fecha', 'ASC');
        
        return $results;
       
    }
}
