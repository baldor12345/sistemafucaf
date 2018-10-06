<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuoptioncategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;

		DB::table('menuoptioncategory')->insert(array(
				array(
					'name'     => 'Administración',
					'order'      => 1,
					'icon'      => 'fa fa-bank',
					'position'      => 'V',
					'created_at' => $now,
					'updated_at' => $now
				),
				array(
					'name'     => 'Mantenimiento',
					'order'      => 2,
					'icon'      => 'fa fa-bank',
					'position'      => 'V',
					'created_at' => $now,
					'updated_at' => $now
				),
				array(
					'name'     => 'Usuarios',
					'order'      => 3,
					'icon'      => 'fa fa-bank',
					'position'      => 'V',
					'created_at' => $now,
					'updated_at' => $now
				)
			)
		);
    }
}
