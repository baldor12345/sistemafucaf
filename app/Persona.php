<?php

namespace App;

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
		            		$subquery->where('codigo', 'LIKE', '%'.$codigo.'%');
		            	}
		            })->where(function($subquery) use($nombre)
		            {
		            	if (!is_null($nombre)) {
		            		$subquery->where('nombres', 'LIKE', '%'.$nombre.'%');
		            	}
                    })->where(function($subquery) use($dni)
                    {
                        if (!is_null($dni)) {
		            		$subquery->where('dni', 'LIKE', '%'.$dni.'%');
		            	}
                    })->where(function($subquery) use($tipo)
                    {
                        if (!is_null($tipo)) {
		            		$subquery->where('tipo', 'LIKE', '%'.$tipo.'%');
		            	}
                    })
        			->orderBy('nombres', 'ASC');
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
}
