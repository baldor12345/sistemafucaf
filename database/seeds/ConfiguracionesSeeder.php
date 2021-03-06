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
                'valor_recibo'       => '0.20',
                'taza_mora'       => '5.0',
                'ganancia_accion'       => '0.01',
                'limite_acciones'       => '0.2',
                'tasa_interes_credito'       => '0.025',
                'tasa_interes_multa'       => '0.01',
                'tasa_interes_ahorro'       => '0.01',

                'fecha'	=>'2018-09-08',
                'descripcion'       => 'PRECIO DE ACCION ES S/.10',
				'created_at'     => $now,
				'updated_at'     => $now
        ));
    }
}
