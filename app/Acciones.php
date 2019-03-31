<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
class Acciones extends Model
{
    use SoftDeletes;
    protected $table = 'acciones';
    protected $dates = ['deleted_at'];

    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    /*
        CONSULTA SQL EN POSTGRES
        SELECT * FROM acciones 
        INNER JOIN persona ON (acciones.persona_id = persona.id)
        INNER JOIN configuraciones  ON (acciones.configuraciones_id = configuraciones.id)
        WHERE persona.id= -1
        OR persona.codigo=''
        OR persona.nombres LIKE '%Bald%'
    */
    public function persona()
    {
        return $this->belongsTo('App\Persona', 'persona_id');
    }
    public function caja()
    {
        return $this->belongsTo('App\Caja', 'caja_id');
    }

    public function configuraciones()
    {
        return $this->belongsTo('App\Configuraciones', 'configuraciones_id');
    }

    public static function cantAcciones($id){
        return  Acciones::where('dni','=',$id)->get();
    }

    public static  function listar($fecha, $nombre){     
        $fecha = date('Y-m-d', strtotime($fecha));
        $result1 = DB::table('persona')
        ->leftjoin('acciones','persona.id','=','acciones.persona_id')
        ->join('caja','caja.id','=','acciones.caja_id')
        ->select(
            'persona.id as persona_id'
        )
        ->whereDate('acciones.fechai','=',$fecha)
        ->where('persona.estado','A')
        ->where('caja.estado','A')
        ->where('persona.tipo','=','S ')->get();
        $ids = array();
       
        foreach ($result1 as $user) {
            $ids[] = $user->persona_id;
        }       
        $result = DB::table('persona')
                    ->select(
                        'persona.id as persona_id',
                        'persona.dni as persona_dni',
                        'persona.codigo as persona_codigo',
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos'
                    )
                    ->whereNotIn('persona.id', $ids)
                    ->where('persona.estado','A')
                    ->where('persona.tipo','=','S ')
                    ->where('persona.deleted_at','=',null)
                    ->where('persona.nombres','ILIKE','%'.$nombre.'%')
                    ->orderBy('persona.apellidos','ASC');
           
        return $result;
    }

    public static function listAcciones($persona_id){
        $results = DB::table('acciones')
                    ->select( 
                        'acciones.estado as accion_estado',
                        'acciones.fechai as accion_fecha',
                        'acciones.descripcion as accion_descripcion',
                        'acciones.persona_id as accion_persona_id',
                        'acciones.codigo as accion_codigo',
                        'acciones.id as accion_id'
                        )
                    ->where('acciones.persona_id', '=', $persona_id)
                    ->where('acciones.deleted_at',null)
                    ->orderBy('acciones.codigo','DSC');
        return $results;
        			
    }

    public static function cant_acciones_acumuladas($persona_id){
        return  DB::table('acciones')
                ->join('persona', 'acciones.persona_id', '=', 'persona.id')
                ->join('configuraciones', 'acciones.configuraciones_id', '=', 'configuraciones.id')
                ->select(
                        'persona.id as persona_id', 
                        'persona.nombres as persona_nombres',
                        'persona.tipo as persona_tipo',
                        'configuraciones.limite_acciones as limite_acciones',
                        DB::raw('count(acciones.estado) as cantidad_accion_acumulada')
                )
                ->where('acciones.persona_id', '!=', $persona_id)
                ->where('acciones.estado', '=', 'C')
                ->where('acciones.deleted_at',null)
                ->groupBy('configuraciones.limite_acciones','persona.tipo', 'persona.nombres','persona.id')->get();
    }

    //metodo listar cantidad de acciones por socio para el calculo de las normas 
    public static function list_acciones_persona()
    {
        $results = DB::table('acciones')
                    ->join('configuraciones', 'acciones.configuraciones_id', '=', 'configuraciones.id')
                    ->join('persona', 'acciones.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.nombres AS persona_nombres',
                        'persona.apellidos AS persona_apellidos',
                        DB::raw("COUNT(acciones.estado) as cantidad_accion"),
                        'configuraciones.limite_acciones AS limite_accion'
                    )
                    ->where('persona.tipo','=','S')
                    ->where('acciones.estado','C')
                    ->where('acciones.deleted_at',null)
                    ->groupBy('persona.nombres','configuraciones.limite_acciones','persona.apellidos')
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }

    public static function listresumen($fecha){
        $fecha = date('Y-m-d', strtotime($fecha));
        $year = date('Y', strtotime($fecha));
        $month = date('m', strtotime($fecha));
        $day = date('d', strtotime($fecha));
        $fechai;
        $result1 = DB::table('persona')
        ->leftjoin('acciones','persona.id','=','acciones.persona_id')
        ->select(
            'persona.id as persona_id'
        )
        ->whereDate('acciones.fechai','=',$fecha)
        ->where('persona.estado','A')
        ->where('persona.tipo','=','S ')->get();
        $ids = array();
       
        foreach ($result1 as $user) {
            $ids[] = $user->persona_id;
        }
       
        $result = DB::table('persona')
                    ->select(
                        'persona.id as persona_id',
                        'persona.codigo as persona_codigo',
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos'
                    )
                    ->whereNotIn('persona.id', $ids)
                    ->where('persona.estado','A')
                    ->where('persona.tipo','=','S ')
                   
                    ->orderBy('persona.apellidos','ASC');
        return $result;
    }
    
    /*
    SELECT 
				persona.nombres AS persona_nombres,
				persona.apellidos AS persona_apellidos,
				COUNT(acciones.estado) AS cantidad_accion,
				configuraciones.ganancia_accion AS acciones_ganancia
    FROM acciones INNER JOIN configuraciones ON (acciones.configuraciones_id = configuraciones.id)
                                INNER JOIN persona ON (acciones.persona_id = persona.id)
    WHERE acciones.estado = 'C' AND (persona.tipo = 'S' OR persona.tipo ='SC')
    GROUP BY configuraciones.ganancia_accion, persona.tipo, persona.nombres,persona.apellidos;
    */
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
            $binnacle->table      = 'acciones';
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
            $binnacle->table      = 'acciones';
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
            $binnacle->table      = 'acciones';
            $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });
    }

}
