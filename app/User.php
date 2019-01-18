<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\UserResetPasswordNotification;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'login', 'password', 'usertype_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopelistar($query, $login)
    {
        return $query->where(function($subquery) use($login)
                    {
                        if (!is_null($login)) {
                            $subquery->where('login', 'LIKE', '%'.$login.'%');
                        }
                    })
                    ->orderBy('login', 'ASC');
    }

    public function usertype()
    {
        return $this->belongsTo('App\Usertype', 'usertype_id');
    }

    public function persona()
    {
        return $this->belongsTo('App\Persona', 'persona_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new UserResetPasswordNotification($token));
    }

    public static function listBinnacle($desde, $hasta)
    {
        $results = DB::table('persona')
                    ->join('binnacle', 'persona.id', '=', 'binnacle.user_id')
                    ->select(
                        'persona.nombres AS persona_nombres',
                        'persona.apellidos AS persona_apellidos',
                        'persona.codigo as persona_codigo',
                        'binnacle.action as accion',
                        'binnacle.table as tabla',
                        'binnacle.date as fecha_hora',
                        'binnacle.detail as detalle'
                    )
                    ->where(DB::raw("to_char(binnacle.date,'yyyy-mm-dd')"),'>=',$desde)
                    ->where(DB::raw("to_char(binnacle.date,'yyyy-mm-dd')"),'<=',$hasta);
        return $results;
    }

    /*
    SELECT 	persona.nombres,
				persona.apellidos,
				persona.codigo,
				binnacle.action, 
				binnacle.table,
				binnacle.date,
				binnacle.detail
FROM persona INNER JOIN binnacle on (persona.id = binnacle.user_id)
    */
}
