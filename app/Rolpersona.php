<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rolpersona extends Model
{
    use SoftDeletes;
    protected $table = 'rolpersona';
    protected $dates = ['deleted_at'];
    
    public function persona(){
        return $this->belongsTo('App\Persona', 'persona_id');
    }
    
    public function empresa(){
        return $this->belongsTo('App\Empresa', 'empresa_id');
    }

    public function rol(){
        return $this->belongsTo('App\Rol', 'rol_id');
    }

}
