<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    public function scopelistar($query,$nombreAcreditado, $fechai = '2009-01-01', $estado){
        $fechai = $fechai==null? '2009-01-01' :  $fechai;
        $results = DB::table('credito')
        ->leftJoin('persona', 'persona.id', '=', 'credito.persona_id')
        ->select(
            'persona.id as persona_id',
            'persona.nombres as nombres',
            'persona.apellidos as apellidos',
            'persona.tipo as tipo',
            'persona.codigo as codigo',
            'persona.dni as dni',
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
        ->where('persona.nombres','ILIKE', '%'.$nombreAcreditado.'%')
        // ->where('persona.apellidos','ILIKE', '%'.$nombreAcreditado.'%')
        ->where('credito.fechai','>=',$fechai)
        ->where('credito.estado','=',$estado)
        ->where('credito.deleted_at','=', null)
        ->orderBy('credito.fechai', 'DSC');
        return $results;
    }

    public function obtenercredito($idcredito){
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

    public function getpersonacredito($persona_id){
        $persona = Persona::where('id','=',$persona_id)->get();
        $numerocreditos = null;
        $numeroacciones = null;
        $num_moras = 0;
        if(count($persona) > 0){
             $creditos = Credito::where('persona_id', '=',$persona_id)->where('deleted_at','=',null)->get();
            $ids_creditos = array();
            $i=0;
            foreach ($creditos as $key => $value) {
                $ids_creditos[$i] = $value->id;
                $i++;
            }
            // echo("ids: ".$ids_creditos);
            $numerocreditos = Credito::where('estado', '=', '0')->where('persona_id','=',$persona_id)->where('deleted_at','=', null)->count();
            $numeroacciones = Acciones::where('estado','=','C')->where('persona_id','=',$persona_id)->where('deleted_at','=', null)->count();
             $num_moras = count(Cuota::where('interes_mora','>',0)->whereIn('credito_id',$ids_creditos)->get());
            
        }
        // echo($num_moras);
        $res = array($persona, $numerocreditos, $numeroacciones,$num_moras);
        return  $res;
    }

    public function lista_creditos_desde_hasta($fecha_inicio, $fecha_fin){
        
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
        ->where('credito.fechai','>=',$fecha_inicio)
        ->where('credito.fechai','<=',$fecha_fin)
        ->where('credito.deleted_at','=',null)
        ->orderby('credito.fechai', 'ASC')->get();
        return $results;
        
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($marca){

            $binnacle             = new Binnacle();
            $binnacle->action     = 'I';
            $binnacle->date      = date('Y-m-d H:i:s');
            $binnacle->ip         = Libreria::get_client_ip();
            $binnacle->user_id =  Auth::user()->id;
            $binnacle->table      = 'credito';
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
            $binnacle->table      = 'credito';
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
            $binnacle->table      = 'credito';
            $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });
    }
}
