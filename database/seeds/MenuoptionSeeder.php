<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuoptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;

		$menuoptioncategory_id = DB::table('menuoptioncategory')->where('name', '=', 'Usuarios')->first()->id;

		$datos = array(
				array(
					'name' => 'Categoría de opción de menu',
					'link'   => 'categoriaopcionmenu'
				),
				array(
					'name' => 'Opción de menu',
					'link'   => 'opcionmenu'
				),
				array(
					'name' => 'Tipos de usuario',
					'link'   => 'tipousuario'
				),
				array(
					'name' => 'Usuario',
					'link'   => 'usuario'
				)
			);

		for ($i=0; $i < count($datos); $i++) { 
			DB::table('menuoption')->insert(array(
					'name'                 => $datos[$i]['name'],
					'link'                   => $datos[$i]['link'],
					'order'                  => $i+1,
					'menuoptioncategory_id' => $menuoptioncategory_id,
					'created_at'             => $now,
					'updated_at'             => $now
				)
			);
		}

		$menuoptioncategory_id = DB::table('menuoptioncategory')->where('name', '=', 'Movimientos')->first()->id;

		$datos = array(
				array(
					'name' => 'Control de Asistencia Socios',
					'link'   => 'controlpersona'
				),
				array(
					'name' => 'Caja',
					'link'   => 'caja'
				),

				array(
					'name' => 'Créditos y Pago de Cuotas',
					'link'   => 'creditos'
				),

				array(
					'name' => 'Compra y Venta de Acciones',
					'link'   => 'acciones'
				),

				array(
					'name' => 'Deposito y Retiro de Ahorros',
					'link'   => 'ahorros'
				),
				array(
					'name' => 'Recibos de Cuotas a la fecha',
					'link'   => 'recibocuotas'
				),
				array(
					'name' => 'Distribucion de Utilidades',
					'link'   => 'distribucion_utilidades'
				)
			);

		for ($i=0; $i < count($datos); $i++) { 
			DB::table('menuoption')->insert(array(
					'name'                 => $datos[$i]['name'],
					'link'                   => $datos[$i]['link'],
					'order'                  => $i+1,
					'menuoptioncategory_id' => $menuoptioncategory_id,
					'created_at'             => $now,
					'updated_at'             => $now
				)
			);
		}

		$menuoptioncategory_id = DB::table('menuoptioncategory')->where('name', '=', 'Mantenimiento')->first()->id;

		$datos = array(
				array(
					'name' => 'Configuraciones',
					'link'   => 'configuraciones'
				),
				
				array(
					'name' => 'Conceptos',
					'link'   => 'concepto'
				),
				array(
					'name' => 'Certificados',
					'link'   => 'certificado'
				),

				array(
					'name' => 'Socios y Clientes',
					'link'   => 'persona'
				),

				
			);

		for ($i=0; $i < count($datos); $i++) { 
			DB::table('menuoption')->insert(array(
					'name'                 => $datos[$i]['name'],
					'link'                   => $datos[$i]['link'],
					'order'                  => $i+1,
					'menuoptioncategory_id' => $menuoptioncategory_id,
					'created_at'             => $now,
					'updated_at'             => $now
				)
			);
		}
    }
}
