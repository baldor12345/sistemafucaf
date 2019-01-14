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

        DB::table('concepto')->insert(array(
            'titulo'          => 'Otros Egresos',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));
        DB::table('concepto')->insert(array(
            'titulo'          => 'Comision Voucher',
            'tipo'       => 'I',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Impresiones de Recibos',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Ganancia por accion',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Contribución de Ingreso',
            'tipo'       => 'I',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Multa por Inasistencia o Tardanza',
            'tipo'       => 'I',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Impresiones de recibos y certificados',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Pago Tesorero por elab. de contabilidad',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Saldo Deudor',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));
        DB::table('concepto')->insert(array(
            'titulo'          => 'Interes I',
            'tipo'       => 'I',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

        DB::table('concepto')->insert(array(
            'titulo'          => 'Interes E',
            'tipo'       => 'E',
            'created_at'     => $now,
            'updated_at'     => $now
        ));

    }
}
