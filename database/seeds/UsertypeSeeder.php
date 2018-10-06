<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsertypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;

		DB::table('usertype')->insert(array(
				array(
					'name' => 'ADMINISTRADOR PRINCIPAL',
					'created_at' => $now,
					'updated_at' => $now
				)
			));

		DB::table('usertype')->insert(array(
				array(
					'name' => 'TESORERO',
					'created_at' => $now,
					'updated_at' => $now
				)
			));
			/*
		DB::table('usertype')->insert(array(
				array(
					'name' => 'EMPLEADO',
					'created_at' => $now,
					'updated_at' => $now
				)
			));
			*/
    }
}
