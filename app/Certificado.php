<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Certificado extends Model
{
    use SoftDeletes;
    protected $table = 'certificado';
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

    public function scopelistar($query, $fechai,$fechaf,$tipo){
        $fecha1 = date('Y-m-d', strtotime($fechai));
        $fecha2 = date('Y-m-d', strtotime($fechaf));
        return $query->where(function($subquery) use($tipo)
		            {
		            	if (!is_null($tipo)) {
		            		$subquery->where('estado', '=',$tipo );
		            	}
                    })
                    ->where('fechai','>=',$fecha1)
                    ->where('fechai','<=',$fecha2)
                    ->where('estado','=','P')
        			->orderBy('inicio', 'ASC');
    }

    public static function listAccionesCertificado($month1, $month2, $anio)
    {
        $results = DB::table('persona')
                    ->leftJoin('acciones', 'acciones.persona_id', '=', 'persona.id')
                    ->join('configuraciones', 'acciones.configuraciones_id','=','configuraciones.id')
                    ->select(
                        'persona.id as persona_id',
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        'configuraciones.precio_accion as accion_precio',
                        DB::raw('count(acciones.estado) as cantidad_accion')
                    )
                    ->where(DB::raw('extract( month from acciones.fechai)'),'>=',$month1)
                    ->where(DB::raw('extract( month from acciones.fechai)'),'<=',$month2)
                    ->where(DB::raw('extract( year from acciones.fechai)'),'=',$anio)
                    ->where('acciones.estado','=','C')
                    ->groupBy('persona.id','configuraciones.precio_accion');
        return $results;
    }

}
