<?php

namespace App;

use Illuminate\Support\Facades\Auth;
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
    public static function idUser()
    {
        // Obtiene el ID del Usuario Autenticado
        $id = Auth::id();
        return $id;
    }

    public function scopelistar($query,$nombreAcreditado, $fechai, $estado){
        $results = DB::table('credito')
    ->leftJoin('persona', 'persona.id', '=', 'credito.persona_id')
    ->select(
        'persona.id as persona_id',
        'persona.nombres as nombres',
        'persona.apellidos as apellidos',
        'persona.tipo as tipo',
        'credito.id as credito_id',
        'credito.valor_credito as valor_credito',
        'credito.periodo as periodo',
        'credito.descripcion as descripcion',
        'credito.tasa_interes as tasa_interes',
        'credito.tasa_multa as tasa_multa',
        'credito.fechai as fechai',
        'credito.fechaf as fechaf',
        'credito.estado as estado'
    )
    ->orwhere('persona.nombres','LIKE', '%'.$nombreAcreditado.'%')
    ->orwhere('persona.apellidos','LIKE', '%'.$nombreAcreditado.'%')
    ->where('credito.fechai','>=',$fechai)
    ->where('credito.estado','=',$estado)
    
    ->orderBy('credito.fechai', 'DSC');
        
        return $results;
       
    }

    public static function obtenercredito($idcredito){
        $results = DB::table('credito')
    ->leftJoin('persona as per', 'per.id', '=', 'credito.persona_id')
    ->leftJoin('persona as per_aval', 'per_aval.id', '=', 'credito.pers_aval_id')
    ->select(
        'per_aval.id as aval_id',
        'per_aval.nombres as nombre_aval',
        'per_aval.apellidos as apellidos_aval',

        'per.id as persona_id',
        'per.nombres as nombres',
        'per.apellidos as apellidos',
        'per.tipo as tipo',

        'credito.id as credito_id',
        'credito.valor_credito as valor_credito',
        'credito.periodo as periodo',
        'credito.descripcion as descripcion',
        'credito.tasa_interes as tasa_interes',
        'credito.tasa_multa as tasa_multa',
        'credito.fechai as fechai',
        'credito.fechaf as fechaf',
        'credito.estado as estado'
    )
    ->where('credito.id','=',$idcredito);
   
        return $results->get();
    }
}
