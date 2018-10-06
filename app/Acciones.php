<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    public function configuraciones()
    {
        return $this->belongsTo('App\Configuraciones', 'configuraciones_id');
    }

    public static function cantAcciones($id){
        return  Acciones::where('dni','=',$id)->get();
    }

    public function scopelistar($codigo, $nombres, $dni){


        $results = DB::table('acciones')
            ->join('persona', 'acciones.persona_id', '=', 'persona.id')
            ->join('configuraciones', 'acciones.configuraciones_id', '=', 'configuraciones.id')
            ->select( 
                    'persona.id as persona_id',
                    'persona.codigo AS persona_codigo',
                    'persona.dni as persona_dni',
                    'persona.nombres as persona_nombres',
                    'persona.apellidos as persona_apellidos',
                    'configuraciones.codigo AS configuraciones_codigo',
                    'acciones.persona_id',
                    'acciones.estado',
                    'configuraciones.precio_accion AS precio_accion',
                    DB::raw('count(acciones.estado) as cantidad_accion_comprada')
                    )
                    ->where('acciones.estado', '=', 'C')
                    ->groupBy('persona.id','persona.codigo','persona.dni','persona.nombres',
                                'persona.apellidos','configuraciones.codigo','acciones.persona_id',
                                'acciones.estado','configuraciones.precio_accion');
        return $results;
        			
    }

    public function scopelistAcciones($persona_id){
        $results = DB::table('acciones')
            ->join('persona', 'acciones.persona_id', '=', 'persona.id')
            ->join('configuraciones', 'acciones.configuraciones_id', '=', 'configuraciones.id')
            ->select( 
                    'persona.id as persona_id',
                    'persona.codigo AS persona_codigo',
                    'persona.dni as persona_dni',
                    'persona.nombres as persona_nombres',
                    'persona.apellidos as persona_apellidos',
                    'configuraciones.codigo AS configuraciones_codigo',
                    'acciones.persona_id',
                    'acciones.estado',
                    'configuraciones.precio_accion AS precio_accion',
                    DB::raw('count(acciones.estado) as cantidad_accion_comprada')
                    )
                    ->where('acciones.estado', '=', 'C')
                    ->orWhere('acciones.estado', '=', 'V')
                    ->groupBy('persona.id','persona.codigo','persona.dni','persona.nombres',
                                'persona.apellidos','configuraciones.codigo','acciones.persona_id',
                                'acciones.estado','configuraciones.precio_accion');
        return $results;
        			
    }

    public static function cant_acciones_acumuladas($persona_id){
        
        return  DB::table('acciones')
                    ->join('persona', 'acciones.persona_id', '=', 'persona.id')
                    ->join('configuraciones', 'acciones.configuraciones_id', '=', 'configuraciones.id')
                    ->select( 
                            'persona.nombres as persona_nombres',
                            'persona.dni as persona_dni',
                            'configuraciones.limite_acciones as limite_acciones',
                            DB::raw('count(acciones.estado) as cantidad_accion_acumulada')
                            )
                            ->where('persona.dni', '!=', $persona_id)
                            ->where('acciones.estado', '=', 'C')
                            ->groupBy('configuraciones.limite_acciones','persona.dni','persona.nombres')->get();
    }

    /*
    public function scopeCantidaAcciones($persona_id){
        $results = DB::table('acciones')
            ->join('configuraciones', 'acciones.configuraciones_id', '=', 'configuraciones.id')
            ->select( 
                    'configuraciones.limite_acciones as limite_acciones',
                    DB::raw('count(acciones.estado) as cantidad_accion_acumulada')
                    )
                    ->where('acciones.persona_id', '!=', $persona_id)
                    ->groupBy('configuraciones.limite_acciones');
        return $results;
        			
    }
    */

}
