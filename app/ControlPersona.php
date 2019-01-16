<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function scopelistar($query, $fecha,$tipo){

        return $query->where(function($subquery) use($fecha)
		            {
		            	if (!is_null($fecha)) {
		            		$subquery->where('fecha', '=',$fecha );
		            	}
		            })->where(function($subquery) use($tipo)
                    {
                        if (!is_null($tipo)) {
		            		$subquery->where('asistencia','=',$tipo);
		            	}
                    })
        			->orderBy('persona_id', 'ASC');
    }



    public static function listSocioCliente(){
        return  DB::table('persona')->where('tipo', 'S')->orWhere('tipo','SC');
    }

    //lista de ingresos por persona del mes actual
    public static function listAsistencia($fecha)
    {
        $fecha = date('Y-m-d',strtotime($fecha));
        $results = DB::table('persona')
                    ->join('control_socio', 'control_socio.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.codigo as persona_codigo',
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        DB::raw("count(control_socio.asistencia) as control_tardanzas"),
                        DB::raw("count(control_socio.asistencia) as control_faltas")
                    )
                    ->where('control_socio.estado','N')
                    ->where('control_socio.fecha','<=',$fecha)
                    ->groupBy('persona.id');
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
            $binnacle->table      = 'control_socio';
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
            $binnacle->table      = 'control_socio';
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
            $binnacle->table      = 'control_socio';
            $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });
    }
    
}
