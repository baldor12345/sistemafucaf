<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Detalle_cuotas extends Model
{
    use SoftDeletes;
    protected $table = 'detalle_cuotas';
    protected $dates = ['deleted_at'];
    
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */

    public function scopelistar($query,$idcredito){
        $results = DB::table('detalle_cuotas')
        ->where('detalle_cuotas.credito_id','=',$idcredito)
        ->orderBy('detalle_cuotas.fecha_pago', 'ASC');
        return $results;
    }
}
