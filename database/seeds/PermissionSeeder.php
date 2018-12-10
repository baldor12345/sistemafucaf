<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now          = new DateTime;
		$usertype_id = DB::table('usertype')->where('name', '=', 'ADMINISTRADOR PRINCIPAL')->first()->id;
		$list          = DB::table('menuoption')->get();
		foreach ($list as $key => $value) {
			DB::table('permission')->insert(array(
				array(
					'usertype_id' => $usertype_id,
					'menuoption_id'  => $value->id,
					'created_at'     => $now,
					'updated_at'     => $now
					)
				));
		}
		$usertype_id = DB::table('usertype')->where('name', '=', 'TESORERO')->first()->id;
		$menuoption_id  = DB::table('menuoption')->where('name', '=', 'Personas')->first()->id;
		DB::table('permission')->insert(array(
			array(
				'usertype_id' => $usertype_id,
				'menuoption_id'  => $menuoption_id,
				'created_at'     => $now,
				'updated_at'     => $now
				)
			));
		
		$menuoption_id = DB::table('menuoption')->where('name', '=', 'Creditos')->first()->id;
		DB::table('permission')->insert(array(
			array(
				'usertype_id' => $usertype_id,
				'menuoption_id'  => $menuoption_id,
				'created_at'     => $now,
				'updated_at'     => $now
				)
			));
		$menuoption_id = DB::table('menuoption')->where('name', '=', 'Caja')->first()->id;
		DB::table('permission')->insert(array(
			array(
				'usertype_id' => $usertype_id,
				'menuoption_id'  => $menuoption_id,
				'created_at'     => $now,
				'updated_at'     => $now
				)
		));
		$menuoption_id = DB::table('menuoption')->where('name', '=', 'Ahorros')->first()->id;
		DB::table('permission')->insert(array(
			array(
				'usertype_id' => $usertype_id,
				'menuoption_id'  => $menuoption_id,
				'created_at'     => $now,
				'updated_at'     => $now
				)
		));
		$menuoption_id = DB::table('menuoption')->where('name', '=', 'Acciones')->first()->id;
		DB::table('permission')->insert(array(
			array(
				'usertype_id' => $usertype_id,
				'menuoption_id'  => $menuoption_id,
				'created_at'     => $now,
				'updated_at'     => $now
				)
		));
		$menuoption_id = DB::table('menuoption')->where('name', '=', 'Configuraciones')->first()->id;
		DB::table('permission')->insert(array(
			array(
				'usertype_id' => $usertype_id,
				'menuoption_id'  => $menuoption_id,
				'created_at'     => $now,
				'updated_at'     => $now
				)
		));
		$menuoption_id = DB::table('menuoption')->where('name', '=', 'Conceptos')->first()->id;
		DB::table('permission')->insert(array(
			array(
				'usertype_id' => $usertype_id,
				'menuoption_id'  => $menuoption_id,
				'created_at'     => $now,
				'updated_at'     => $now
				)
		));
    }
}
