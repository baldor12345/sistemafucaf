<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ahorros extends Model
{
    use SoftDeletes;
    protected $table = 'ahorros';
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

    public function scopelistar($query, $nombre, $fecha )
    {
        $results = DB::table('ahorros')
    ->leftJoin('persona', 'persona.id', '=', 'ahorros.persona_id')
    ->select(
        'persona.id as persona_id',
        'ahorros.id as ahorros_id',
        'persona.nombres as nombres',
        'persona.apellidos as apellidos',
        'persona.tipo as tipo',
        'ahorros.inporte as importe',
        'ahorros.periodo as periodo',
        'ahorros.fecha_inicio as fecha_inicio',
        'ahorros.fecha_fin as fecha_fin',
        'ahorros.interes as interes'
    )
    ->where('persona.nombres','LIKE', '%'.$nombre.'%')
    ->where('ahorros.fecha_inicio','>=',$fecha)
    ->orderBy('ahorros.fecha_inicio', 'DSC');
        return $results;
    }


}
