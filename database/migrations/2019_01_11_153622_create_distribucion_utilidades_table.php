<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDistribucionUtilidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('distribucion_utilidades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo', 200)->nullable();
            //egresos
            $table->decimal('gast_admin_acum',20,1);
            $table->decimal('int_pag_acum',20,1);
            $table->decimal('otros_acum',20,1);
            //ingresos
            $table->decimal('intereses',20,1);
            $table->decimal('utilidad_distribuible',20,1);
            $table->decimal('otros',20,1);
            $table->timestamp('fechai')->nullable();
            $table->timestamp('fechaf')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distribucion_utilidades');
    }
}
