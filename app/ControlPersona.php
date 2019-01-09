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

    public function scopelistar($query, $persona_id){

        return $query->where(function($subquery) use($persona_id)
		            {
		            	if (!is_null($persona_id)) {
		            		$subquery->where('persona_id', '=',$persona_id );
		            	}
		            })
        			->orderBy('persona_id', 'ASC');
    }

    public static function listSocioCliente(){
        return  DB::table('persona')->where('tipo', 'S')->orWhere('tipo','SC');
    }
    
}
