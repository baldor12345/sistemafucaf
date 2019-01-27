<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
   
    public static function idUser()
    {
        // Obtiene el ID del Usuario Autenticado
        $id = Auth::id();
        return $id;
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

//Metdo para listar todos los ahorros activos actualizados  
    public function scopelistar($query, $nombre)
    {
        $results = DB::table('ahorros')
        ->leftJoin('persona', 'persona.id', '=', 'ahorros.persona_id')
        ->select(
            'persona.id as persona_id',
            'persona.nombres as nombres',
            'persona.apellidos as apellidos',
            'persona.tipo as tipo',
            'persona.codigo as codigo',
            'persona.dni as dni',
            'ahorros.capital as capital',
            'ahorros.fechai as fechai'
        )
        ->where('persona.nombres','LIKE', '%'.$nombre.'%')
        ->where('ahorros.estado','=','P')
        ->where('ahorros.deleted_at','=',null)
        ->orderBy('persona.nombres', 'ASC');
        return $results;
    }

    //Metodo para obtener el ahorro por persona 
    public static function getahorropersona($id_persona){
        return Ahorros::where('persona_id','=',$id_persona)->where('estado','=','P')->get();
    }

    //Metodo para listar el historico de su capital de ahorros y sus ganancias mensuales por año
    public static function listarhistorico($id_persona, $anio)
    {
        $results = DB::table('ahorros')
        ->select(
            'ahorros.id as ahorros_id',
            'ahorros.interes as interes',
            'ahorros.capital as capital',
            'ahorros.fechai as fechai',
            'ahorros.fechaf as fechaf',
            'ahorros.persona_id as persona_id',
            DB::raw('extract( month from ahorros.fechai) as mes')
        )
        ->where('ahorros.persona_id','=', $id_persona)
        ->where(DB::raw('extract( year from ahorros.fechai)'),'=',$anio)
        ->orderBy(DB::raw('extract( month from ahorros.fechai)'), 'ASC');
        return $results;
    }

    // Metodo para listar retiros o depositos de ahorros,  por persona 
    public static function listaretirodeposito( $id_persona, $fechainicio, $tipo)
    {
        $results = DB::table('transaccion')
        ->Join('concepto', 'transaccion.concepto_id', '=', 'concepto.id')
        ->select(
            'transaccion.id_tabla as id_ahorro',
            'transaccion.monto as monto',
            'transaccion.id as transaccion_id',
            'transaccion.fecha as fecha'
        )
        ->where('transaccion.inicial_tabla','=', 'AH')
        ->where('concepto.tipo','=', ''.$tipo)
        ->where('concepto.id','!=', '8')
        ->where('transaccion.deleted_at','=', null)
        ->where('transaccion.persona_id','=', $id_persona)
        ->where('transaccion.fecha','>=', $fechainicio)
        ->orderBy('transaccion.fecha', 'ASC');
        return $results;
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
            $binnacle->table      = 'ahorros';
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
            $binnacle->table      = 'ahorros';
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
            $binnacle->table      = 'ahorros';
            $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });
    }
}
