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
                'codigo'              =>'SFUCAF008',
                'dni'               =>'41604698',
                'nombres'       =>'Harold Helbert',
                'apellidos'              =>'Lopez Osorio',
                'fecha_nacimiento'	=>'1980-05-02',
                'sexo'              =>'M',
                'estado_civil'              =>'CA',
                'personas_en_casa'      =>'3',
                'direccion'         =>'Las Cascadas #155 Urb San Lorenzo',
                'ocupacion'         =>'Ingenieria',
                'email'             =>'harherlopez@hotmail.com',
                'telefono_fijo'     =>'989143985',
                'tipo'              =>'S',
                'fechai'	=>'2009-01-03',
                'ingreso_personal'              =>'2000.00',
                'ingreso_familiar'              =>'3400.00',
                'estado'              =>'A',
				'created_at'     => $now,
				'updated_at'     => $now
            ));
        DB::table('persona')->insert(array(
            'codigo'              =>'SFUCAF018',
            'dni'               =>'17643035',
            'nombres'       =>'Rocio del Pilar',
            'apellidos'              =>'Castillo Rojas',
            'fecha_nacimiento'	=>'2018-09-08',
            'sexo'              =>'F',
            'estado_civil'              =>'S',
            'personas_en_casa'      =>'5',
            'direccion'         =>'Garcilazo de la Vega N° 449 - Motupe',
            'ocupacion'         =>'Profesora',
            'email'             =>'',
            'telefono_fijo'     =>'952266540',
            'tipo'              =>'S',
            'fechai'	=>'2011-12-22',
            'ingreso_personal'              =>'1000.00',
            'ingreso_familiar'              =>'2500.00',
            'estado'              =>'A',
            'created_at'     => $now,
            'updated_at'     => $now
        ));
        DB::table('persona')->insert(array(
            'codigo'              =>'FUCAF',
            'dni'               =>'11111111',
            'nombres'       =>'Fondo Social',
            'apellidos'              =>'',
            'fecha_nacimiento'	=>'2018-09-08',
            'sexo'              =>'M',
            'estado_civil'              =>'S',
            'personas_en_casa'      =>'1',
            'direccion'         =>'',
            'ocupacion'         =>'',
            'email'             =>'',
            'telefono_fijo'     =>'',
            'tipo'              =>'E',
            'fechai'	=>'2011-12-22',
            'ingreso_personal'              =>'1000.00',
            'ingreso_familiar'              =>'2500.00',
            'estado'              =>'A',
            'created_at'     => $now,
            'updated_at'     => $now
        ));
        DB::table('persona')->insert(array(
            'codigo'              =>'FUCAF',
            'dni'               =>'22222222',
            'nombres'       =>'Reserva Legal',
            'apellidos'              =>'',
            'fecha_nacimiento'	=>'2018-09-08',
            'sexo'              =>'M',
            'estado_civil'              =>'CA',
            'personas_en_casa'      =>'3',
            'direccion'         =>'',
            'ocupacion'         =>'',
            'email'             =>'',
            'telefono_fijo'     =>'',
            'tipo'              =>'E',
            'fechai'	=>'2009-01-03',
            'ingreso_personal'              =>'1000.00',
            'ingreso_familiar'              =>'1000.00',
            'estado'              =>'A',
            'created_at'     => $now,
            'updated_at'     => $now
        ));
        DB::table('persona')->insert(array(
            'codigo'              =>'FUCAF',
            'dni'               =>'33333333',
            'nombres'       =>'Intereses Moratorios',
            'apellidos'              =>'',
            'fecha_nacimiento'	=>'2018-09-08',
            'sexo'              =>'M',
            'estado_civil'              =>'CA',
            'personas_en_casa'      =>'3',
            'direccion'         =>'',
            'ocupacion'         =>'',
            'email'             =>'',
            'telefono_fijo'     =>'',
            'tipo'              =>'E',
            'fechai'	=>'2009-01-03',
            'ingreso_personal'              =>'1000.00',
            'ingreso_familiar'              =>'1000.00',
            'estado'              =>'A',
            'created_at'     => $now,
            'updated_at'     => $now
        ));
        DB::table('persona')->insert(array(
            'codigo'              =>'FUCAF',
            'dni'               =>'44444444',
            'nombres'       =>'Multas por Tardanza o Faltas',
            'apellidos'              =>'',
            'fecha_nacimiento'	=>'2018-09-08',
            'sexo'              =>'M',
            'estado_civil'              =>'S',
            'personas_en_casa'      =>'1',
            'direccion'         =>'',
            'ocupacion'         =>'',
            'email'             =>'',
            'telefono_fijo'     =>'',
            'tipo'              =>'E',
            'fechai'	=>'2011-12-22',
            'ingreso_personal'              =>'1000.00',
            'ingreso_familiar'              =>'2500.00',
            'estado'              =>'A',
            'created_at'     => $now,
            'updated_at'     => $now
        ));
    }
}