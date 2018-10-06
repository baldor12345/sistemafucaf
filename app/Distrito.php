<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distrito extends Model
{
	use SoftDeletes;
    protected $table = 'distrito';
    protected $dates = ['deleted_at'];

    public function provincia() 
    {
        return $this->belongsTo('App\Provincia','provincia_id');
    }

    public static function distritos($id){
        return  Distrito::where('provincia_id','=',$id)->get();
    }
    
}
