<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Directivos extends Model
{
    use SoftDeletes;
    protected $table = 'directivos';
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
        $user = DB::table('usuario')->where('id', $id)->first();
        $persona_id=$user->persona_id;
        return $persona_id;
    }

    public function scopelistar($query, $titulo, $periodoi, $periodof)
    {
        $fecha1 = date('Y-m-d', strtotime($periodoi));
        $fecha2 = date('Y-m-d', strtotime($periodof));
        return $query->where(function($subquery) use($titulo)
                    {
                        if (!is_null($titulo)) {
                            $subquery->where('titulo', 'LIKE', '%'.$titulo.'%');
                        }
                    })
                    ->where('periodoi','>=',$fecha1)
                    ->where('periodoi','<=',$fecha2)
                    ->orderBy('periodoi', 'DSC');
    }

    public static function boot()
{
    parent::boot();

    static::created(function($marca)
    {

        $binnacle             = new Binnacle();
        $binnacle->action     = 'I';
        $binnacle->date      = date('Y-m-d H:i:s');
        $binnacle->ip         = Libreria::get_client_ip();
        $binnacle->user_id =  Auth::user()->id;
        $binnacle->table      = 'Directivos';
        $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
        $binnacle->recordid = $marca->id;
        $binnacle->save();
    });

    static::updated(function($marca)
    {
        $binnacle             = new Binnacle();
        $binnacle->action     = 'U';
        $binnacle->date      = date('Y-m-d H:i:s');
        $binnacle->ip         = Libreria::get_client_ip();
        $binnacle->user_id = Auth::user()->id;
        $binnacle->table      = 'Directivos';
        $binnacle->detail    =$marca->toJson(JSON_UNESCAPED_UNICODE);
        $binnacle->recordid = $marca->id;
        $binnacle->save();
    });
    static::deleted(function($marca)
    {
        $binnacle             = new Binnacle();
        $binnacle->action     = 'D';
        $binnacle->date      = date('Y-m-d H:i:s');
        $binnacle->ip         = Libreria::get_client_ip();
        $binnacle->user_id = Auth::user()->id;
        $binnacle->table      = 'Directivos';
        $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
        $binnacle->recordid = $marca->id;
        $binnacle->save();
    });
}
}
