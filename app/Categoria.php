<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;

class Categoria extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $table='categoria';

    protected $primaryKey='id';


    /**
     * Método para listar
     * @param  model $query modelo
     * @param  string $name  nombre
     * @return sql        sql
     */
    public function scopelistar($query, $descripcion)
    {
        return $query->where(function($subquery) use($descripcion)
		            {
		            	if (!is_null($descripcion)) {
		            		$subquery->where('name', 'LIKE', '%'.$descripcion.'%');
		            	}
		            })
        			->orderBy('name', 'ASC');
    }

    protected $fillable =[
        'name'
    ];

    protected $guarded=[

    ];

    public static function boot()
    {
        parent::boot();

        static::created(function($categoria)
        {

            $binnacle             = new Binnacle();
            $binnacle->action     = 'I';
            $binnacle->date      = date('Y-m-d H:i:s');
            $binnacle->ip         = Libreria::get_client_ip();
            $binnacle->user_id =  Auth::user()->id;
            $binnacle->table      = 'categoria';
            $binnacle->detail    = $categoria->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $categoria->id;
            $binnacle->save();
        });

        static::updated(function($categoria)
        {
            $binnacle             = new Binnacle();
            $binnacle->action     = 'U';
            $binnacle->date      = date('Y-m-d H:i:s');
            $binnacle->ip         = Libreria::get_client_ip();
            $binnacle->user_id = Auth::user()->id;
            $binnacle->table      = 'categoria';
            $binnacle->detail    =$categoria->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $categoria->id;
            $binnacle->save();
        });
        static::deleted(function($categoria)
        {
            $binnacle             = new Binnacle();
            $binnacle->action     = 'D';
            $binnacle->date      = date('Y-m-d H:i:s');
            $binnacle->ip         = Libreria::get_client_ip();
            $binnacle->user_id = Auth::user()->id;
            $binnacle->table      = 'categoria';
            $binnacle->detail    = $categoria->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $categoria->id;
            $binnacle->save();
        });
    }
}
