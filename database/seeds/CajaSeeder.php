<?php

use Illuminate\Database\Seeder;

class CajaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;
        $persona_id = DB::table('persona')->where('dni','=','12345678')->first()->id;
        DB::table('caja')->insert(array(
                'titulo'          => 'Caja 00001',
                'descripcion'       => 'ok',
                'monto_iniciado'    =>'10000',
                'estado'    =>'A',
                'persona_id'    =>$persona_id,
                'fecha_horaApert'=> $now,
                'created_at'     => $now,
				'updated_at'     => $now
        ));
        DB::table('concepto')->insert(array(
                'titulo'          => 'Venta de acciones',
                'tipo'       => 'I',
                'created_at'     => $now,
				'updated_at'     => $now
        ));
    }
}