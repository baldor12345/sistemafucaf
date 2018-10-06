<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;

		$now = new DateTime;
		DB::table('persona')->insert(array(
                'codigo'              =>'SFUCAF0001',
                'dni'               =>'12345678',
                'nombres'       =>'BALDOR',
                'apellidos'              =>'MANAYAY',
                'fecha_nacimiento'	=>'2018-09-08',
                'sexo'              =>'M',
                'estado_civil'              =>'S',
                'personas_en_casa'      =>'4',
                'direccion'         =>'JUNIN 23 LAMBAYEQUE',
                'ocupacion'         =>'ESTUDIANTE',
                'email'             =>'BALDOR@GMAIL.COM',
                'telefono_fijo'     =>'123456789',
                'tipo'              =>'S',
                'fechai'	=>'2018-09-08',
                'ingreso_personal'              =>'300.00',
                'ingreso_familiar'              =>'500.00',
				'created_at'     => $now,
				'updated_at'     => $now
            ));
            DB::table('persona')->insert(array(
                'codigo'              =>'SFUCAF0002',
                'dni'               =>'12345679',
                'nombres'       =>'AZORIN',
                'apellidos'              =>'MANAYAY',
                'fecha_nacimiento'	=>'2018-09-08',
                'sexo'              =>'M',
                'estado_civil'              =>'S',
                'personas_en_casa'      =>'4',
                'direccion'         =>'JUNIN 23 CHICLAYO',
                'ocupacion'         =>'ESTUDIANTE',
                'email'             =>'AZORIN@GMAIL.COM',
                'telefono_fijo'     =>'123456789',
                'tipo'              =>'S',
                'fechai'	=>'2018-09-08',
                'ingreso_personal'              =>'300.00',
                'ingreso_familiar'              =>'500.00',
				'created_at'     => $now,
				'updated_at'     => $now
			));

    }
}