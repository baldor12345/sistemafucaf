<?php

use Illuminate\Database\Seeder;

class ConfiguracionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;
        DB::table('configuraciones')->insert(array(
                'codigo'          => 'C00001',
                'precio_accion'       => '10',
                'ganancia_accion'       => '0.01',
                'limite_acciones'       => '0.2',
                'fecha'	=>'2018-09-08',
                'descripcion'       => 'EL PRECIO DE ACCION ES S/.10 CON GANANCIA DE 1% POR DIA',
				'created_at'     => $now,
				'updated_at'     => $now
        ));
    }
}
