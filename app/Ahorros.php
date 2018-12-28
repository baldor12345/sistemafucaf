<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ahorros extends Model
{
    use SoftDeletes;
    protected $table = 'ahorros';
    protected $dates = ['deleted_at'];
    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    /*public function persona(){
        return $this->belongsTo('App\Persona', 'persona_id');
    } */
    public static function idUser()
    {
        // Obtiene el ID del Usuario Autenticado
        $id = Auth::id();
        return $id;
    }
    public static function getIdPersona()
    {
        $persona_id = null;
        // Obtiene el objeto del Usuario Autenticado
        $user = Auth::user();
        // Obtiene el ID del Usuario Autenticado
        $id = Auth::id();
        $user = DB::table('user')->where('id', $id)->first();
        $persona_id=$user->persona_id;
        return $persona_id;
    }

  /* 
    public static function listardetalle($estado, $persona_id)
    {
        $results = DB::table('ahorros')
        ->select(
            'ahorros.id as ahorros_id',
            'ahorros.importe as importe',
            'ahorros.fecha_deposito as fecha_deposito',
            'ahorros.fecha_retiro as fecha_retiro',
            'ahorros.interes as interes',
            'ahorros.estado as estado'
        )
        ->where('ahorros.persona_id','=', $persona_id)
        ->where('ahorros.estado','=', ''.$estado)
        ->where('ahorros.deleted_at','=',null)
        ->orderBy('ahorros.fecha_deposito', 'DSC');
        return $results;
    }
 
    public function scopelistar($query, $nombre, $fecha)
    {
        $results = DB::table('ahorros')
        ->leftJoin('persona', 'persona.id', '=', 'ahorros.persona_id')
        ->select(
            'persona.id as persona_id',544
            'persona.nombres as nombres',
            'persona.apellidos as apellidos',
            'persona.tipo as tipo',
            'persona.codigo as codigo',
            DB::raw('sum(ahorros.importe) as montototal')
        )
        ->where('persona.nombres','LIKE', '%'.$nombre.'%')
        ->where('ahorros.deleted_at','=',null)
        ->where('ahorros.estado','=','P')
        ->groupBy('persona.id')
        ->orderBy('persona.nombres', 'ASC');
        return $results;
    }*/

//Metdo para listar todos los ahorros activos actualizados  
    public function scopelistar($query, $nombre)
    {
        $results = DB::table('ahorros')
        ->leftJoin('persona', 'persona.id', '=', 'ahorros.persona_id')
        ->select(
            'persona.id as persona_id',
            'persona.nombres as nombres',
            'persona.apellidos as apellidos',
            'persona.tipo as tipo',
            'persona.codigo as codigo',
            'ahorros.capital as capital',
            'ahorros.fechai as fechai'
        )
        ->where('persona.nombres','LIKE', '%'.$nombre.'%')
        ->where('ahorros.fechaf','=',null)
        ->orderBy('persona.nombres', 'ASC');
        return $results;
    }

    //Metodo para obtener el ahorro por persona 
    public static function getahorropersona($id_persona){
        return Ahorros::where('persona_id','=',$id_persona)->where('fechaf','=',null)->get();
    }

    //Metodo para listar el historico de su capital de ahorros y sus ganancias mensuales por año
    public static function listarhistorico($id_persona, $anio)
    {
        $results = DB::table('ahorros')
        ->select(
            'ahorros.id as ahorros_id',
            'ahorros.interes as interes',
            'ahorros.capital as capital',
            'ahorros.fechai as fechai',
            'ahorros.fechaf as fechaf',
            'ahorros.persona_id as persona_id',
            DB::raw('extract( month from ahorros.fechaf) as mes')
        )
        ->where('ahorros.persona_id','=', $id_persona)
        ->where('ahorros.fechaf','=',null)
        ->where(DB::raw('extract( year from ahorros.fechaf)'),'=',$anio)
        ->orderBy(DB::raw('extract( month from ahorros.fechaf)'), 'ASC');
        return $results;
    }

    // Metodo para listar retiros o depositos de ahorros,  por persona 
    public static function listaretirodeposito( $id_persona, $fechainicio, $tipo)
    {
        $results = DB::table('transaccion')
        ->Join('concepto', 'transaccion.concepto_id', '=', 'concepto.id')
        ->select(
            'transaccion.id_tabla as id_ahorro',
            'transaccion.monto as monto',
            'transaccion.fecha as fecha'
        )
        ->where('transaccion.inicial_tabla','=', 'AH')
        ->where('concepto.tipo','=', ''.$tipo)
        ->where('transaccion.persona_id','=', $id_persona)
        ->where('transaccion.fecha','>=', $fechainicio)
        ->orderBy('transaccion.fecha', 'ASC');
        return $results;
    }
}
