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

    public function scopelistar($query, $fechai,$fechaf,$tipo){
        $fecha1 = date('Y-m-d', strtotime($fechai));
        $fecha2 = date('Y-m-d', strtotime($fechaf));
        return $query->where(function($subquery) use($tipo)
		            {
		            	if (!is_null($tipo)) {
		            		$subquery->where('asistencia', '=',$tipo );
		            	}
                    })
                    ->where('fecha','>=',$fecha1)
                    ->where('fecha','<=',$fecha2)
                    ->where('estado','=','N')
                    ->where('asistencia','!=','J')
        			->orderBy('persona_id', 'ASC');
    }



    public static function listSocioCliente(){
        return  DB::table('persona')->where('tipo', 'S');
    }

    //lista de ingresos por persona del mes actual
    public static function listAsistenciaF($fechai, $fechaf)
    {
        $fechai = date('Y-m-01', strtotime($fechai));
        $fechaf = date('Y-m-01', strtotime($fechaf));

        $month1 = date('m', strtotime($fechai));
        $year1 = date('Y', strtotime($fechai));

        $month2 = date('m', strtotime($fechaf));
        $year2 = date('Y', strtotime($fechaf));

        $results = DB::table('persona')
                    ->join('control_socio', 'control_socio.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.codigo as persona_codigo',
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        DB::raw("count(control_socio.asistencia) as control_faltas")
                    )
                    ->where('control_socio.estado','N')
                    ->where('control_socio.asistencia','F')
                    ->where(DB::raw('extract( month from control_socio.fecha )'),'>=',$month1)
                    ->where(DB::raw('extract( year from control_socio.fecha )'),'>=',$year1)
                    ->where(DB::raw('extract( month from control_socio.fecha )'),'<=',$month2)
                    ->where(DB::raw('extract( year from control_socio.fecha )'),'<=',$year2)
                    ->groupBy('persona.id')
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }

    public static function listAsistenciaT($fechai, $fechaf)
    {
        $fechai = date('Y-m-01', strtotime($fechai));
        $fechaf = date('Y-m-01', strtotime($fechaf));

        $month1 = date('m', strtotime($fechai));
        $year1 = date('Y', strtotime($fechai));

        $month2 = date('m', strtotime($fechaf));
        $year2 = date('Y', strtotime($fechaf));

        $results = DB::table('persona')
                    ->join('control_socio', 'control_socio.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.codigo as persona_codigo',
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        DB::raw("count(control_socio.asistencia) as control_tardanzas")
                    )
                    ->where('control_socio.estado','N')
                    ->where('control_socio.asistencia','T')
                    ->where(DB::raw('extract( month from control_socio.fecha )'),'>=',$month1)
                    ->where(DB::raw('extract( year from control_socio.fecha )'),'>=',$year1)
                    ->where(DB::raw('extract( month from control_socio.fecha )'),'<=',$month2)
                    ->where(DB::raw('extract( year from control_socio.fecha )'),'<=',$year2)
                    ->groupBy('persona.id')
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }

    public static function listJustificadas($fechai, $fechaf)
    {
        $fechai = date('Y-m-01', strtotime($fechai));
        $fechaf = date('Y-m-01', strtotime($fechaf));

        $month1 = date('m', strtotime($fechai));
        $year1 = date('Y', strtotime($fechai));

        $month2 = date('m', strtotime($fechaf));
        $year2 = date('Y', strtotime($fechaf));

        $results = DB::table('persona')
                    ->join('control_socio', 'control_socio.persona_id', '=', 'persona.id')
                    ->select(
                        'persona.codigo as persona_codigo',
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        'control_socio.fecha as fecha',
                        'control_socio.descripcion as descripcion'
                    )
                    ->where(DB::raw('extract( month from control_socio.fecha )'),'>=',$month1)
                    ->where(DB::raw('extract( year from control_socio.fecha )'),'>=',$year1)
                    ->where(DB::raw('extract( month from control_socio.fecha )'),'<=',$month2)
                    ->where(DB::raw('extract( year from control_socio.fecha )'),'<=',$year2)
                    ->where('control_socio.descripcion','!=',null)
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }


    public static function listSocios(){
        $results =DB::table('persona')
                    ->select('id','codigo','nombres','apellidos')
                    ->where('tipo','S ')
                    ->where('estado','A')
                    ->where('id','!=',3)
                    ->where('id','!=',4)
                    ->where('id','!=',5)
                    ->where('id','!=',6)
                    ->orderBy('codigo', 'ASC')
                    ->orderBy('tipo','DSC');
        return $results;
    }

}
