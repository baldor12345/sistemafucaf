<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Concepto extends Model
{
    use SoftDeletes;
    protected $table = 'concepto';
    protected $dates = ['deleted_at'];

    public function scopelistar($query, $tipo)
    {
        return $query->where(function($subquery) use($tipo)
            {
                if (!is_null($tipo)) {
                    $subquery->where('tipo', '=', $tipo);
                }
            })
            ->where('titulo','!=','Venta de acciones')
            ->where('id','!=',16)
            ->where('id','!=',17)
            ->where('id','!=',10)
            ->where('id','!=',21)
            ->orderBy('titulo', 'ASC');
    }
    
}
