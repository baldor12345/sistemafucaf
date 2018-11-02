<?php

namespace App;

use App\Caja;
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

<<<<<<< HEAD
    public function user()
    {
        return $this->belongsTo('App\User', 'usuario_id');
    }

    public function persona()
    {
        return $this->belongsTo('App\Persona', 'persona_id');
    }

    public function concepto()
    {
        return $this->belongsTo('App\Concepto', 'concepto_id');
    }
    public function caja()
    {
        return $this->belongsTo('App\Caja', 'caja_id');
    }



    public function scopelistar($query, $fecha, $concepto_id){
        $idCaja = DB::table('caja')->where('estado', "A")->value('id');
        echo "id de la caja: ".$idCaja;

        return $query->where(function($subquery) use($fecha)
		            {
		            	if (!is_null($fecha)) {
		            		$subquery->where('fecha', '=', $fecha);
		            	}
		            })->where(function($subquery) use($concepto_id)
                    {
                        if (!is_null($concepto_id)) {
		            		$subquery->where('concepto_id', '=', $concepto_id);
		            	}
                    })->where(function($subquery) use($idCaja)
                    {
                        if (!is_null($idCaja)) {
		            		$subquery->where('caja_id', '=', $idCaja);
		            	}
                    })
        			->orderBy('concepto_id', 'ASC');      
=======
     public static function obtenerid($idgasto){
        $results = DB::table('transaccion') ->where('gastos_id','=',$idgasto);
        return $results->get();
     }
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
>>>>>>> 4fd76e96936d42f67eee2ed8d1979e9b26f4f73a
        
    }

}
