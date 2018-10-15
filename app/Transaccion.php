<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaccion extends Model
{
    use SoftDeletes;
    protected $table = 'transaccion';
    protected $dates = ['deleted_at'];
    
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */

    public function scopelistar($query,$acciones_id, $detalle_cuotas_id, $ahorros_id, $gastos_id, $credito_id, $caja_id, $fecha){
        $results = DB::table('transaccion')
        ->where('transaccion.acciones_id','=',$acciones_id)
        ->where('transaccion.detalle_cuotas_id','=',$detalle_cuotas_id)
        ->where('transaccion.ahorros_id','=', $ahorros_id)
        ->where('transaccion.gastos_id','=',$gastos_id)
        ->where('transaccion.credito_id','=',$credito_id)
        ->where('transaccion.caja_id','=',$caja_id)
        ->where('transaccion.fecha','=',$fecha)
        ->orderBy('transaccion.fecha', 'ASC');
        
        return $results;
       
    }
}
