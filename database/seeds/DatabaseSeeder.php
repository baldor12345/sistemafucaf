<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(MenuoptioncategorySeeder::class);
        $this->call(MenuoptionSeeder::class);
        $this->call(UsertypeSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PersonSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ConfiguracionesSeeder::Class);
    }
}
