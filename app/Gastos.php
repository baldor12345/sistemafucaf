<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gastos extends Model
{
    use SoftDeletes;
    protected $table = 'gastos';
    protected $dates = ['deleted_at'];
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    /*public function persona(){
        return $this->belongsTo('App\Persona', 'persona_id');
    } */

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

    public function scopelistar($query, $fecha )
    {
        $results = DB::table('gastos')
    
    ->where('gastos.fecha','>=',$fecha)
    ->orderBy('gastos.fecha', 'DSC');
        return $results;
    }


}
