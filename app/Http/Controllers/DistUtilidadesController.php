<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\DistribucionUtilidades;
use App\Http\Controllers\Controller;

class DistUtilidadesController extends Controller
{


    protected $folderview      = 'app.distribucionutilidad';
    protected $tituloAdmin     = 'Distribución de Utilidades Anual';
    protected $tituloRegistrar = 'Compra de Acciones';
  
    protected $rutas           = array('create' => 'distribucionutilidades.create', 
            'edit'   => 'distribucionutilidades.edit', 
            'search' => 'distribucionutilidades.buscar',
            'reporte' => 'distribucionutilidades.reporte',
            'index'  => 'distribucionutilidades.index',
        );
    /**
     * Método para generar combo provincia
     * @param  [type] $departamento_id [description]
     * @return [type]                  [description]
     */
    
    public function cboprovincia($departamento_id = null)
    {
        
    }

    public function buscar(Request $request){
        
    }


}
