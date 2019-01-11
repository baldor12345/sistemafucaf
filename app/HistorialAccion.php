<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistorialAccion extends Model
{
    use SoftDeletes;
    protected $table = 'historial_accion';
    protected $dates = ['deleted_at'];

    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    /*
        CONSULTA SQL EN POSTGRES
        SELECT * FROM acciones 
        INNER JOIN persona ON (acciones.persona_id = persona.id)
        INNER JOIN configuraciones  ON (acciones.configuraciones_id = configuraciones.id)
        WHERE persona.id= -1
        OR persona.codigo=''
        OR persona.nombres LIKE '%Bald%'
    */
    public function persona()
    {
        return $this->belongsTo('App\Persona', 'persona_id');
    }
    public function caja()
    {
        return $this->belongsTo('App\Caja', 'caja_id');
    }

    public function configuraciones()
    {
        return $this->belongsTo('App\Configuraciones', 'configuraciones_id');
    }

    public static function cantAcciones($id){
        return  Acciones::where('dni','=',$id)->get();
    }

}
