<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaCaja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('caja', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo', 200)->nullable();
            $table->string('descripcion', 400)->nullable();
            $table->timestamp('fecha_horaApert')->nullable();
            $table->timestamp('fecha_horaCierre')->nullable();
            $table->decimal('monto_iniciado',20,2);
            $table->decimal('monto_cierre',20,2)->nullable();
            $table->decimal('diferencia_monto',20,2)->nullable();
            $table->char('estado', 1)->nullable();// A=>abierto; C=>cerrado
            $table->integer('persona_id')->unsigned();
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('caja');
    }
}
