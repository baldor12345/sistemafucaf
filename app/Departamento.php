<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Departamento extends Model
{
    use SoftDeletes;
    protected $table = 'departamento';
    protected $dates = ['deleted_at'];

    /**
     * método para obtener las provincias hijas
     * @return [type] [description]
     */
    public function provincias()
	{
		return $this->hasMany('App\Provincia');
    }
    
}
