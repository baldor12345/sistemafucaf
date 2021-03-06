<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;
		$usertype_id = DB::table('usertype')->where('name', '=', 'ADMINISTRADOR PRINCIPAL')->first()->id;
		$persona_id = DB::table('persona')->where('dni','=','41604698')->first()->id;
		DB::table('usuario')->insert(array(
				'login'          => 'admin',
				'password'       => Hash::make('123456'),
				'fechai'	=>'2018-09-08',
				'estado'	=>'A',
				'persona_id'	 => $persona_id,
				'usertype_id' => $usertype_id,
				'created_at'     => $now,
				'updated_at'     => $now
			));
		$usertype_id = DB::table('usertype')->where('name', '=', 'TESORERO')->first()->id;
		$persona_id = DB::table('persona')->where('dni','=','17643035')->first()->id;
		DB::table('usuario')->insert(array(
				'login'          => '12345678',
				'password'       => Hash::make('123456'),
				'fechai'	=>'2018-09-08',
				'estado'	=>'A',
				'persona_id'	 => $persona_id,
				'usertype_id' => $usertype_id,
				'created_at'     => $now,
				'updated_at'     => $now
			));
    }
}
