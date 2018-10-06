<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\UserResetPasswordNotification;


use Illuminate\Database\Eloquent\Model;

class configuraciones extends Model
{
    use Notifiable;
    protected $table = 'configuraciones';
    protected $dates = ['deleted_at'];
    
    public function scopelistar($query, $codigo)
    {
        return $query->where(function($subquery) use($codigo)
                    {
                        if (!is_null($codigo)) {
                            $subquery->where('codigo', 'LIKE', '%'.$codigo.'%');
                        }
                    })
                    ->orderBy('codigo', 'ASC');
    }

}
