<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credito extends Model
{
    use SoftDeletes;
    protected $table = 'credito';
    protected $dates = ['deleted_at'];
    
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */

    public function scopelistar($query,$nombreAcreditado, $fecha, $estado){
        $results = DB::table('credito')
    ->leftJoin('persona', 'persona.id', '=', 'credito.persona_id')
    ->select(
        'persona.id as persona_id',
        'credito.id as credito_id',
        'persona.nombres as nombres',
        'persona.apellidos as apellidos',
        'persona.tipo as tipo',
        'credito.valor_credito as valor_credito',
        'credito.cantidad_cuotas as cuotas',
        'credito.cantidad_meses as meses',
        'credito.descripcion as descripcion',
        'credito.comision as comision',
        'credito.fecha as fecha',
        'credito.estado as estado',
        'credito.multa as multa'
    )
    ->orwhere('persona.nombres','LIKE', '%'.$nombreAcreditado.'%')
    ->orwhere('persona.apellidos','LIKE', '%'.$nombreAcreditado.'%')
    ->where('credito.fecha','>=',$fecha)
    ->where('credito.estado','=',$estado)
    
    ->orderBy('credito.fecha', 'DSC');
        
        return $results;
       
    }

    public static function obtenercredito($idcredito){
        $results = DB::table('credito')
    ->leftJoin('persona', 'persona.id', '=', 'credito.persona_id')
    ->select(
        'persona.id as persona_id',
        'credito.id as credito_id',
        'persona.nombres as nombres',
        'persona.apellidos as apellidos',
        'persona.tipo as tipo',
        'credito.valor_credito as valor_credito',
        'credito.cantidad_cuotas as cantidad_cuotas',
        'credito.cantidad_meses as cantidad_meses',
        'credito.descripcion as descripcion',
        'credito.comision as comision',
        'credito.fecha as fecha',
        'credito.estado as estado',
        'credito.multa as multa'
    )
    ->where('credito.id','=',$idcredito);
   
        return $results->get();
    }
}
