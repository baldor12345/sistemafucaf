<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class Pagos extends Model
{
    use SoftDeletes;
    protected $table = 'pagos';
    protected $dates = ['deleted_at'];

    //metodo para resumen de vueltos
    public static function getresumen_acciones($id){
        $results = DB::table('persona')
            ->join('pagos', 'pagos.persona_id', '=', 'persona.id')
            ->select( 
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        'pagos.monto_recibido as monto_recibido',
                        'pagos.monto_pago as monto_pago'
                    )
                    ->where('pagos.caja_id','=',$id)
                    ->where('pagos.ini_tabla','=','AC')
                    ->where('pagos.deleted_at',null)
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }

    public static function getresumen_pago_cuotas($id){
        $results = DB::table('persona')
            ->join('pagos', 'pagos.persona_id', '=', 'persona.id')
            ->select( 
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        'pagos.monto_recibido as monto_recibido',
                        'pagos.monto_pago as monto_pago'
                    )
                    ->where('pagos.caja_id','=',$id)
                    ->where('pagos.ini_tabla','=','CU')
                    ->where('pagos.deleted_at',null)
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }

    public static function getresumen_ahorros($id){
        $results = DB::table('persona')
            ->join('pagos', 'pagos.persona_id', '=', 'persona.id')
            ->select( 
                        'persona.nombres as persona_nombres',
                        'persona.apellidos as persona_apellidos',
                        'pagos.monto_recibido as monto_recibido',
                        'pagos.monto_pago as monto_pago'
                    )
                    ->where('pagos.caja_id','=',$id)
                    ->where('pagos.ini_tabla','=','AH')
                    ->where('pagos.deleted_at',null)
                    ->orderBy('persona.apellidos','ASC');
        return $results;
    }

}
