<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        ->orderBy('cuota.fecha_programada_pago', 'ASC');
        return $results;
    }
}
