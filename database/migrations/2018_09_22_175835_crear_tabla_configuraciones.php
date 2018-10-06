<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaConfiguraciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('codigo', 80)->nullable();
            $table->decimal('precio_accion',10,2);
            $table->decimal('ganancia_accion',10,2);//en porcentaje por cada accion comprada
            $table->decimal('limite_acciones');//limite de accines=20% con respecto a la cantidad total de acciones de la empresa
            $table->date('fecha')->nullable();
            $table->string('descripcion', 200)->nullable();
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
        Schema::dropIfExists('configuraciones');
    }
}
