<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use App\Librerias\Libreria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DistribucionUtilidades extends Model
{
    use SoftDeletes;
    protected $table = 'distribucion_utilidades';
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
    public function scopelistar($query, $titulo)
    {
        return $query->where(
            function($subquery) use($titulo)
            {
                if (!is_null($titulo)) {
                    $subquery->where('titulo', 'LIKE', '%'.$titulo.'%');
                }
            })
            ->orderBy('fechai', 'DSC');
    }

    public function persona()
    {
        return $this->belongsTo('App\Persona', 'persona_id');
    }
    //esto es una prueba

    /**CALCULO DE LOS INGRESOS */

    //lista de ingresos por persona del mes actual
    public static function sumUBDacumulado($anio)
    {
        $fechai = date($anio.'-01-01');
        $fechaf = date(($anio+1).'-01-01');

        $resultsI1 = DB::table('transaccion')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                        DB::raw("SUM(transaccion.cuota_interes) as intereses_recibidos"),
                        DB::raw("SUM(transaccion.cuota_mora) as cuota_mora"),
                        DB::raw("SUM(transaccion.comision_voucher) as comision_voucher")
                    )
                    //S>where('transaccion.fecha','>=',$fechai)
                    ->where('transaccion.fecha','<',$fechaf)
                    ->where('transaccion.deleted_at','=',null)
                    ->where('concepto.tipo','=','I')
                    ->groupBy('concepto.tipo')->get();
                    
                    
        $intereses = (count($resultsI1)<1)? 0 :  $resultsI1[0]->intereses_recibidos;
        $sum_otros =  (count($resultsI1)<1)? 0 : $resultsI1[0]->comision_voucher;
        
        $results2 = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        DB::raw("SUM(transaccion.monto) as mas_otros")
                    )
                    //->where('transaccion.fecha','>=',$fechai)
                    ->where('transaccion.fecha','<',$fechaf)
                    ->where('transaccion.deleted_at','=',null)
                    ->where('concepto.tipo','=','I')
                    ->where('concepto.titulo','!=','Compra de acciones')
                    ->where('concepto.titulo','!=','Venta de acciones')
                    ->where('concepto.titulo','!=','Comision Voucher')
                    ->where('concepto.titulo','!=','Deposito de ahorros')
                    ->where('concepto.titulo','!=','Pago de cuotas')
                    ->groupBy('concepto.tipo')->get();
        $sum_otros += (count($results2)<1)? 0:  $results2[0]->mas_otros;
        
        return array($intereses, $sum_otros);
    }


    /**CALCULO DE LOS EGRESOS PARA EL CALCULO DE GASTOS*/
     //lista de egresos  del mes actual por persona
    public static function gastosDUactual($anio)
    {
        $fechai = date($anio.'-01-01');
        $fechaf = date(($anio+1).'-01-01');
        $results0 = DB::table('transaccion')
        ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
        ->select(
            DB::raw("SUM(transaccion.interes_ahorro) as interes_ahorro")
       
        )
        //->where('transaccion.fecha','>=',$fechai)
        ->where('transaccion.fecha','<',$fechaf)
        ->where('transaccion.deleted_at','=',null)
        ->where('concepto.tipo','=','E')
        ->groupBy('concepto.tipo')->get();
        $i_pag_acum =(count($results0)<1)?0: $results0[0]->interes_ahorro;


        $results1 = DB::table('transaccion')
                    ->join('concepto', 'concepto.id', '=', 'transaccion.concepto_id')
                    ->select(
                     
                        DB::raw("SUM(transaccion.monto) as otros_egresos")
                    )
                    //->where('transaccion.fecha','>=',$fechai)
                    ->where('transaccion.fecha','<',$fechaf)
                    ->where('transaccion.deleted_at','=',null)
                    ->where('concepto.tipo','=','E')
                    ->where('transaccion.tipo_egreso','=', 0)
                    ->groupBy('concepto.tipo')->get();
        
        $otros_acum  = 0;
        $otros_acum += (count($results1)<1)?0: $results1[0]->otros_egresos;
                        
        $results2 = DB::table('concepto')
                    ->leftJoin('transaccion', 'transaccion.concepto_id', '=', 'concepto.id')
                    ->select(
                        DB::raw("SUM(transaccion.monto) as gastos_adm ")
                    )
                   // ->where('transaccion.fecha','>=',$fechai)
                    ->where('transaccion.fecha','<',$fechaf)
                    ->where('transaccion.deleted_at','=',null)
                    ->where('concepto.tipo','=','E')
                    ->where('concepto.titulo','!=','Retiro de ahorros')
                    ->where('concepto.titulo','!=','Crédito')
                    ->where('concepto.titulo','!=','Ganancia por accion')
                    ->where('concepto.titulo','!=','Saldo Deudor')
                    ->where('concepto.titulo', '!=', 'Distribución de utilidad')
                    ->where('transaccion.tipo_egreso','=', 1)
                    ->groupBy('concepto.tipo')->get();
        $g_adm_acum = (count($results2)<1)?0: $results2[0]->gastos_adm;

        return array($i_pag_acum, $otros_acum, $g_adm_acum);
    }


    /*
        SELECT SUM(cantidad) AS cantidad_mes 
        from historial_accion 
        where  extract( year from fecha) = '2019' 
        GROUP BY extract( month from fecha) 
        ORDER  BY  extract( month from fecha) ASC;
     */

    //PARA CREAR LA DISTRIBUCION Y VER LA DISTRIBUCION
    //lista todal de acciones por mes 
    public static function list_total_acciones_mes($anio){

        $results = DB::table('acciones')
                    ->select(
                        DB::raw("COUNT(DISTINCT codigo) as cantidad_mes"),
                        DB::raw('extract( month from fechai) as mes')
                    )
                    ->where(DB::raw('extract( year from fechai)'),'=',$anio)
                    ->where('deleted_at','=',null)
                    ->where('tipo','=','A')
                    ->orwhere(
                        function($subquery) use($anio){
                            if (!is_null($anio)) {
                                $subquery->where('deleted_at','=',null)
                                ->where('tipo','=','I')
                                ->where(DB::raw('extract( year from fechai)'),'=',$anio)
                                ->where(DB::raw('extract( year from fechaf)'),'>',$anio);
                            }
                        }
                    )
                    ->groupBy(DB::raw('extract( month from fechai)'))
                    ->orderBy(DB::raw('extract( month from fechai)'), 'ASC');
        return $results;

    }


 //PARA CREAR LA DISTRIBUCION Y LA VISTA DISTRIBUCION
    public static function num_acciones_anio_anterior($anio){
        $results = DB::table('acciones')
        ->select(
            DB::raw("COUNT(*) as cantidad_total")
        )
        ->where(
            function($subquery) use ($anio){
                $subquery->where('deleted_at','=', null)
                ->where('estado','=','C')
                ->where('tipo','=','A')
                ->where(DB::raw('extract( year from fechai)'),'<',$anio);
                // ->where(DB::raw('extract( year from fechaf)'),'>',($anio));
            }
        )
        ->orwhere(
            function($subquery) use ($anio){
                $subquery->where('deleted_at','=', null)
                ->where('estado','=','V')
                ->where('tipo','=','A')
                ->where(DB::raw('extract( year from fechai)'),'<',$anio)
                ->where(DB::raw('extract( year from fechaf)'),'>',($anio));
            }
        )
        ->orwhere(
            function($subquery) use($anio, $persona_id){
                if(!is_null($anio)){
                    $subquery->where('persona_id','=',$persona_id)
                    ->where('deleted_at','=',null)
                    ->where('tipo','=','I')
                    ->where('estado','=','C')
                    ->where(DB::raw('extract( year from fecha_transf)'),'<',$anio)
                    ->where(DB::raw('extract( year from fechaf)'),'>',$anio);
                }
            }
        );
       
    return $results;
    }

    //PARA CREAR LA DISTRIBUCION Y VER LA DISTRIBUCION
    //lista total de acciones por persona por mes 
    public static function list_por_persona($persona_id, $anio)
    {
       
        $results = DB::table('acciones')
            ->select(
                DB::raw("COUNT(*) as cantidad_mes"),
                DB::raw("extract( month from fecha_transf) as mes")
            )
            ->where('persona_id','=',$persona_id)
            ->where('deleted_at','=',null)
            ->where('tipo','=','A')
            ->where('estado','=','C')
            ->where(DB::raw('extract( year from fecha_transf)'),'=',$anio)
            // ->where(DB::raw('extract( year from fechaf)'),'>',$anio)
            ->orwhere(
                function($subquery) use($anio, $persona_id)
                {
                    if (!is_null($persona_id)) {
                        $subquery->where('persona_id','=',$persona_id)
                        ->where('deleted_at','=',null)
                        ->where('tipo','=','A')
                        ->where('estado','=','V')
                        ->where(DB::raw('extract( year from fecha_transf)'),'=',$anio)
                        ->where(DB::raw('extract( year from fechaf)'),'>',$anio);
                    }
                })
                ->orwhere(
                    function($subquery) use($anio, $persona_id){
                        if(!is_null($anio)){
                            $subquery->where('persona_id','=',$persona_id)
                            ->where('deleted_at','=',null)
                            ->where('tipo','=','I')
                            // ->where('estado','=','V')
                            ->where(DB::raw('extract( year from fecha_transf)'),'=',$anio)
                            ->where(DB::raw('extract( year from fechaf)'),'>',$anio);
                        }
                    }
                )
            ->groupBy(DB::raw('extract( month from fecha_transf)'))
            ->orderBy(DB::raw('extract( month from fecha_transf)'), 'ASC');

        return $results;
    }

   
    
    //PARA CREAR LA DISTRIBUCION Y VISTA DISTRIBUCION
    public static function list_enero($persona_id, $anio){

        $res = Acciones::where('persona_id',$persona_id )->where('deleted_at',null)->get();

        $results = DB::table('acciones')
        ->select(
            DB::raw("COUNT(*) as cantidad_total")
        )
        ->where('persona_id','=',$persona_id)
        ->where('deleted_at','=',null)
        ->where('tipo','=','A')
        ->where('estado','=','C')
        ->where(DB::raw('extract( year from fecha_transf)'),'<',$anio)
        // ->where(DB::raw('extract( year from fechaf)'),'>',$anio)
        ->orwhere(
            function($subquery) use($anio, $persona_id){
                if(!is_null($anio)){
                    $subquery->where('persona_id','=',$persona_id)
                    ->where('deleted_at','=',null)
                    ->where('tipo','=','A')
                    ->where('estado','=','V')
                    ->where(DB::raw('extract( year from fecha_transf)'),'<',$anio)
                    ->where(DB::raw('extract( year from fechaf)'),'>',$anio);
                }
            }
        )
        ->orwhere(
            function($subquery) use($anio, $persona_id){
                if(!is_null($anio)){
                    $subquery->where('persona_id','=',$persona_id)
                    ->where('deleted_at','=',null)
                    ->where('tipo','=','I')
                    // ->where('estado','=','V')
                    ->where(DB::raw('extract( year from fecha_transf)'),'<',$anio)
                    ->where(DB::raw('extract( year from fechaf)'),'>',$anio);
                }
            }
        )
        ->groupBy(DB::raw('persona_id'));

        return $results;
    }
/********************************************************************************************************************* */
    //LISTAR NUMERO DE ACCIONES POR MES Y POR PERSONA DEL AÑO DE DISTRIBUCION
    public static function lista_num_acciones_paso6($anio){
        $results = DB::table('acciones')
            ->select(
                    DB::raw('COUNT(distinct codigo) as cantidad'),
                    DB::raw('extract(month from fechai) as mes'),
                    'persona_id as persona_id',

            )
            ->where(DB::raw('extract(year from fechai)'), '=', $anio)
            ->where(DB::raw('extract(year from fecha_transf)'), '=', $anio)
            ->where('deleted_at', null)
            ->where(
                function($subquery) use ($anio){
                    $subquery->where('estado','C')
                    ->orwhere(
                        function($subquery) use($anio){
                            $subquery->where('estado', 'V')
                            ->where(DB::raw('extract(year from fechaf)'),'>',$anio);
                        }
                    );
                }
            )
            ->where(
                function($subquery) use ($anio){
                    $subquery->where('tipo','A')
                    ->orwhere(
                        function($subquery) use($anio){
                            $subquery->where('tipo', 'I')
                            ->where(DB::raw('extract(year from fechaf)'),'>',$anio);
                        }
                    );
                }
            )
            ->groupBy(DB::raw('extract( month from fechai)'),'persona_id')
            ->orderBy('persona_id','asc')
            ->orderBy(DB::raw('extract( month from fechai)'),'asc');

            return $results;

    }

    //LISTAR EL NUMERO DE ACCIONES CONTADAS HASTA DICIEMBRE DEL AÑO ANTERIOR A LA DISTRIBUCION DE TODAS LAS PERSONAS
    public static function listar_num_acciones_hasta_enero($anio){

        $results = DB::table('acciones')
            ->select(
                DB::raw('count(distinct codigo) as cantidad'),
                'persona_id as persona_id'
            )
            ->where('deleted_at', null)
            ->where(DB::raw('extract(year from fechai)'),'<', $anio)
            ->where(DB::raw('extract(year from fecha_transf)'),'<=', $anio)
            ->where(
                function($subquery) use($anio){
                    $subquery->where('estado','C')
                    ->orwhere(
                        function($subquery)use($anio){
                            $subquery->where('estado','V')
                            ->where(DB::raw('extract(year from fechaf)'),'>', $anio);
                        }
                    );
                }
            )
            ->where(
                function($subquery) use($anio){
                    $subquery->where('tipo','A')
                    ->orwhere(
                        function($subquery)use($anio){
                            $subquery->where('tipo','I')
                            ->where(DB::raw('extract(year from fechaf)'),'>', $anio);
                        }
                    );
                }
            )
            ->groupBy('persona_id')
            ->orderBy('persona_id','asc');

            return $results;

    }
/********************************************************************************************************************* */
    public static function boot()
    {
        parent::boot();

        static::created(function($marca)
        {

            $binnacle             = new Binnacle();
            $binnacle->action     = 'I';
            $binnacle->date      = date('Y-m-d H:i:s');
            $binnacle->ip         = Libreria::get_client_ip();
            $binnacle->user_id =  Auth::user()->id;
            $binnacle->table      = 'distribucion_utilidades';
            $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });

        static::updated(function($marca)
        {
            $binnacle             = new Binnacle();
            $binnacle->action     = 'U';
            $binnacle->date      = date('Y-m-d H:i:s');
            $binnacle->ip         = Libreria::get_client_ip();
            $binnacle->user_id = Auth::user()->id;
            $binnacle->table      = 'distribucion_utilidades';
            $binnacle->detail    =$marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });
        static::deleted(function($marca)
        {
            $binnacle             = new Binnacle();
            $binnacle->action     = 'D';
            $binnacle->date      = date('Y-m-d H:i:s');
            $binnacle->ip         = Libreria::get_client_ip();
            $binnacle->user_id = Auth::user()->id;
            $binnacle->table      = 'distribucion_utilidades';
            $binnacle->detail    = $marca->toJson(JSON_UNESCAPED_UNICODE);
            $binnacle->recordid = $marca->id;
            $binnacle->save();
        });
    }

    public function ingresos($fecha)
    {
     
        $fecha_completa= $fecha;
        $lista_mes_anterior = Caja::listIngresosastamesanterior($fecha_completa)->get();
        $sum_interese_recibidos_asta_mes_anterior=0;
        $sum_otros_asta_mes_anterior=0;
        if(count($lista_mes_anterior) >0 ){
            for($i=0; $i<count($lista_mes_anterior); $i++){
                $sum_interese_recibidos_asta_mes_anterior += round($lista_mes_anterior[$i]->intereces_recibidos,1);
                $sum_otros_asta_mes_anterior += round($lista_mes_anterior[$i]->comision_voucher,1);
            }
        }

        //lista de ingresos por concepto
        $lista_ingresos_por_concepto_mes_anterior = Caja::listIngresos_por_concepto_asta_mes_anterior($fecha_completa)->get();
        // calculo del total de ingresos del mes actual por concepto
        $sum_por_concepto_mes_anterior=0;
        if(count($lista_ingresos_por_concepto_mes_anterior) >0 ){
            for($i=0; $i<count($lista_ingresos_por_concepto_mes_anterior); $i++){
                $sum_por_concepto_mes_anterior += $lista_ingresos_por_concepto_mes_anterior[$i]->transaccion_monto;
            }
            $sum_otros_asta_mes_anterior += $sum_por_concepto_mes_anterior;
        }

        //calculo de ingresos acumulados asta la fecha
     
        $sum_interese_recibidos_acumulados=0;
       
        $sum_otros_acumulados=0;
        $sum_interese_recibidos_acumulados=($sum_interese_recibidos_asta_mes_anterior);
        $sum_otros_acumulados=($sum_otros_asta_mes_anterior);

        return array($sum_interese_recibidos_acumulados, $sum_otros_acumulados);
    }
    public function egresos($fecha)
    {    
        //identifico la fecha actual
        $fecha_completa= $fecha;
        $lista_mes_anterior = Caja::listEgresos_asta_mes_anterior($fecha_completa)->get();
        $sum_interes_pagado_mes_anterior=0;
        if(count($lista_mes_anterior) >0 ){
            for($i=0; $i<count($lista_mes_anterior); $i++){
                $sum_interes_pagado_mes_anterior += round($lista_mes_anterior[$i]->interes_ahorro,1);
            }
        }
        $lista_por_concepto_asta_mes_anteriorAdmin = Caja::listEgresos_por_concepto_asta_mes_anteriorAdmin($fecha_completa)->get();

        // calculo del total de egresos del mes actual por concepto
        $sum_gasto_administrativo_asta_mes_anterior=0;
        if(count($lista_por_concepto_asta_mes_anteriorAdmin) >0 ){
            for($i=0; $i<count($lista_por_concepto_asta_mes_anteriorAdmin); $i++){
                $sum_gasto_administrativo_asta_mes_anterior += $lista_por_concepto_asta_mes_anteriorAdmin[$i]->transaccion_monto;
            }
        }

        $lista_por_concepto_asta_mes_anteriorOthers = Caja::listEgresos_por_concepto_asta_mes_anteriorOthers($fecha_completa)->get();
        // calculo del total de egresos del mes actual por concepto
        $sum_otros_egresos_asta_mes_anterior =0;
        if(count($lista_por_concepto_asta_mes_anteriorOthers) >0 ){
            for($i=0; $i<count($lista_por_concepto_asta_mes_anteriorOthers); $i++){
                $sum_otros_egresos_asta_mes_anterior += $lista_por_concepto_asta_mes_anteriorOthers[$i]->transaccion_monto;
            }
        }
        //calculo de ingresos acumulados asta la fecha
        
        $sum_interes_pagado_acumulados=0;
        $sum_gasto_administrativo_acumulado =0;
        $sum_otros_gastos_acumulado =0;

        //-------suma
        $sum_interes_pagado_acumulados=($sum_interes_pagado_mes_anterior);
        $sum_gasto_administrativo_acumulado =( $sum_gasto_administrativo_asta_mes_anterior);
        $sum_otros_gastos_acumulado = ($sum_otros_egresos_asta_mes_anterior);
        return array($sum_interes_pagado_acumulados, $sum_gasto_administrativo_acumulado, $sum_otros_gastos_acumulado);
    }

}
