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

    public function sopelistar($query, $fecha, $concepto_id){
        $idCaja = DB::table('caja')->where('estado', "A")->value('id');
        //echo "id de la caja: ".$idCaja;

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
    }

    public static function scopelistar($query, $fecha, $concepto_id, $caja_id){
        $query = DB::table('transaccion')
            ->join('persona', 'transaccion.persona_id', '=', 'persona.id')
            ->join('concepto', 'transaccion.concepto_id', '=', 'concepto.id')
            ->select( 
                    'persona.id as persona_id',
                    'persona.codigo AS persona_codigo',
                    'persona.dni as persona_dni',
                    'persona.nombres as persona_nombres',
                    'persona.apellidos as persona_apellidos',
                    'concepto.titulo AS concepto_titulo',
                    'transaccion.fecha as transaccion_fecha',
                    'transaccion.monto as transaccion_monto',
                    'transaccion.descripcion as transaccion_descripcion'
                    )
            ->where('transaccion.caja_id', '=', $caja_id);
        return $query;
    }


    public static function obtenerid($idgasto){
        $results = DB::table('transaccion') ->where('gastos_id','=',$idgasto);
        return $results->get();

     }  
     
     public static function getTransaccion($id_tabla, $inicial_tabla){
        $results = DB::table('transaccion') ->where('id_tabla','=',$id_tabla)->where('inicial_tabla','=',$inicial_tabla);
        $lista =$results->get();
        $transaccion = $lista[0];
        return $transaccion;
     }
     
    

    public static function getsaldo($id){
        $results = DB::table('transaccion')
            ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
            ->select( 
                    'concepto.titulo as concepto_titulo',
                    'concepto.tipo as concepto_tipo',
                    DB::raw('sum(transaccion.monto) as monto')
                    )->where('transaccion.caja_id','=',$id)
                    ->groupBy('concepto.titulo',
                                'concepto.tipo');
        return $results;
    }    

}
