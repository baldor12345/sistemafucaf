<?php

use Illuminate\Database\Seeder;

class ConceptoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;
        DB::table('concepto')->insert(array(
                'titulo'          => 'Compra de acciones',
                'tipo'       => 'I',
                'created_at'     => $now,
				'updated_at'     => $now
        ));
        DB::table('concepto')->insert(array(
                'titulo'          => 'Venta de acciones',
                'tipo'       => 'I',
                'created_at'     => $now,
				'updated_at'     => $now
        ));
        DB::table('concepto')->insert(array(
            'titulo'          => 'Crédito',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Pago de cuotas',
            'tipo'       => 'I',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Deposito de ahorros',
            'tipo'       => 'I',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Retiro de ahorros',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));
    }
}
