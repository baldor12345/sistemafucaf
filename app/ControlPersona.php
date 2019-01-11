<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public function persona(){
        return $this->belongsTo('App\Persona', 'persona_id');
    }

    public function concepto(){
        return $this->belongsTo('App\Concepto', 'concepto_id');
    }

    public function caja(){
        return $this->belongsTo('App\Caja', 'caja_id');
    }

    public function scopelistar($query, $fecha){

        return $query->where(function($subquery) use($fecha)
		            {
		            	if (!is_null($fecha)) {
		            		$subquery->where('fecha', '=',$fecha );
		            	}
		            })
        			->orderBy('persona_id', 'ASC');
    }



    public static function listSocioCliente(){
        return  DB::table('persona')->where('tipo', 'S')->orWhere('tipo','SC');
    }
    
}
