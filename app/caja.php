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


}
