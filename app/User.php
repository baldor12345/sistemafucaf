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
use DateTime;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    protected $table = 'usuario';

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
                    ->orderBy('estado', 'ASC');
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

    public static function listBinnacle($month, $anio)
    {   
        //$month = date('m', strtotime($month));
        //$anio = date('Y', strtotime($anio));
        $results = DB::table('persona')
                    ->join('usuario', 'persona.id', '=', 'usuario.persona_id')
                    ->join('binnacle', 'usuario.id', '=', 'binnacle.user_id')
                    ->select(
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        'persona.codigo as persona_codigo',
                        'binnacle.action as accion',
                        'binnacle.table as tabla',
                        'binnacle.date as fecha_hora',
                        'binnacle.detail as detalle'
                    )
                    ->where(DB::raw('extract( year from binnacle.date)'),'=',$anio)
                    ->where(DB::raw('extract( month from binnacle.date)'),'=',$month);
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
