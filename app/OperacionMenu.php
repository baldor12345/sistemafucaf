<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperacionMenu extends Model
{
    use SoftDeletes;
    protected $table = 'operacion_menu';
}
