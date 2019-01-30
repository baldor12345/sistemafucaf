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
class Cuota extends Model
{
    use SoftDeletes;
    protected $table = 'cuota';
    protected $dates = ['deleted_at'];
    
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */

    public function scopelistar($query,$idcredito){
        $results = DB::table('cuota')
        ->where('cuota.credito_id','=',$idcredito)
        ->where('cuota.deleted_at','=',null)
        ->orderBy('cuota.fecha_programada_pago', 'ASC');
        return $results;
    }

    public static function listarCuotasAlafecha($anio, $mes, $nombre){

        $fecha_p = new DateTime($anio.'-'.$mes.'-01');
        $fecha_p->modify('last day of this month');
        $fecha_p->format('Y-m-d');

        $results = DB::table('cuota')
        ->Join('credito','credito.id','=','cuota.credito_id')
        ->Join('persona','persona.id','=','credito.persona_id')
        ->select(
            'cuota.id as cuota_id',
            'cuota.parte_capital as parte_capital',
            'cuota.interes as interes',
            'cuota.interes_mora as interes_mora',
            'cuota.fecha_programada_pago as fecha_pagar',
            'cuota.fecha_iniciomora as fecha_iniciomora',
            'cuota.fecha_pago as fecha_pago',
            'cuota.tasa_interes_mora as tasa_interes_mora',
            'cuota.estado as estado',
            'cuota.numero_cuota as numero_cuota',
            'credito.id as credito_id',
            'credito.periodo as periodo',
            'persona.id as persona_id',
            'persona.nombres as nombres',
            'persona.apellidos as apellidos',
            'persona.tipo as tipo',
            DB::raw('extract( month from cuota.fecha_programada_pago) as mes'),
            DB::raw('extract( year from cuota.fecha_programada_pago) as anio')
        )
        ->where('persona.nombres','ILIKE', '%'.$nombre.'%')
        ->where('cuota.estado','!=', '1')
        ->where('cuota.deleted_at','=', null)
        ->where('cuota.fecha_programada_pago','<=',$fecha_p)
        ->orderBy('cuota.numero_cuota', 'ASC');
        return $results;
    }

    public static function listarCuotasAlafechaPersona($anio, $mes, $persona_id, $credito_id, $opcion){
     
        $fecha_p = new DateTime($anio.'-'.$mes.'-01');
        $fecha_p->modify('last day of this month');
        $fecha_p->format('Y-m-d');
        $results = null;
        if($opcion == '3'){
            $results = DB::table('cuota')
            ->Join('credito','credito.id','=','cuota.credito_id')
            ->Join('persona','persona.id','=','credito.persona_id')
            ->select(
                'cuota.id as cuota_id',
                'cuota.parte_capital as parte_capital',
                'cuota.interes as interes',
                'cuota.interes_mora as interes_mora',
                'cuota.fecha_programada_pago as fecha_pagar',
                'cuota.fecha_iniciomora as fecha_iniciomora',
                'cuota.fecha_pago as fecha_pago',
                'cuota.tasa_interes_mora as tasa_interes_mora',
                'cuota.estado as estado',
                'cuota.numero_cuota as numero_cuota',
                'credito.id as credito_id',
                'credito.periodo as periodo',
                'persona.id as persona_id',
                'persona.nombres as nombres',
                'persona.apellidos as apellidos',
                'persona.tipo as tipo',
                DB::raw('extract( month from cuota.fecha_programada_pago) as mes'),
                DB::raw('extract( year from cuota.fecha_programada_pago) as anio')
            )
            ->where('persona.id','=', $persona_id)
            ->where('credito.id','=', $credito_id)
            ->where('cuota.estado','!=', '1')
            ->where('cuota.deleted_at','=', null)
            ->orderBy('cuota.numero_cuota', 'ASC');
        }else{
            $results = DB::table('cuota')
            ->Join('credito','credito.id','=','cuota.credito_id')
            ->Join('persona','persona.id','=','credito.persona_id')
            ->select(
                'cuota.id as cuota_id',
                'cuota.parte_capital as parte_capital',
                'cuota.interes as interes',
                'cuota.interes_mora as interes_mora',
                'cuota.fecha_programada_pago as fecha_pagar',
                'cuota.fecha_iniciomora as fecha_iniciomora',
                'cuota.fecha_pago as fecha_pago',
                'cuota.tasa_interes_mora as tasa_interes_mora',
                'cuota.estado as estado',
                'cuota.numero_cuota as numero_cuota',
                'credito.id as credito_id',
                'credito.periodo as periodo',
                'persona.id as persona_id',
                'persona.nombres as nombres',
                'persona.apellidos as apellidos',
                'persona.tipo as tipo',
                DB::raw('extract( month from cuota.fecha_programada_pago) as mes'),
                DB::raw('extract( year from cuota.fecha_programada_pago) as anio')
            )
            ->where('persona.id','=', $persona_id)
            ->where('credito.id','=', $credito_id)
            ->where('cuota.estado','!=', '1')
            ->where('cuota.deleted_at','=', null)
            ->where('cuota.fecha_programada_pago','<=',$fecha_p)
            ->orderBy('cuota.numero_cuota', 'ASC');
        }
        
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
            $binnacle->table      = 'cuota';
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
            $binnacle->table      = 'cuota';
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
            $binnacle->table      = 'cuota';
            $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });
    }
}
