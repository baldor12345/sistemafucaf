<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ControlPersona extends Model
{
    use SoftDeletes;
    protected $table = 'control_socio';
    protected $dates = ['deleted_at'];
    
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    public function persona()
	{
		return $this->hasMany('App\Persona');
    }
    public function concepto()
	{
		return $this->hasMany('App\Concepto');
    }
    public function caja()
	{
		return $this->hasMany('App\Caja');
    }

    public function scopelistar($query, $codigo, $nombre, $dni, $tipo){

        return $query->where(function($subquery) use($codigo)
		            {
		            	if (!is_null($codigo)) {
		            		$subquery->where('codigo', 'LIKE', '%'.$codigo.'%');
		            	}
		            })->where(function($subquery) use($nombre)
		            {
		            	if (!is_null($nombre)) {
		            		$subquery->whereRaw("nombres || ' ' || apellidos LIKE ?", '%'.$nombre.'%');
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

    
}
