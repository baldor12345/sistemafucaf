<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaCredito extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credito', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('valor_credito',10,1);
            $table->integer('periodo');
            $table->string('descripcion', 200)->nullable();
            $table->decimal('tasa_interes',10,2);//en porcentaje =>interes
            $table->timestamp('fechai')->nullable();
            $table->timestamp('fechaf')->nullable();
            $table->char('estado',1)->nullable();// C=>cancelado, P=>pendiente M=>Mora
            $table->decimal('tasa_multa',10,2);
            $table->integer('persona_id')->unsigned();
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
            //persona que avala al cliente para el prestamo
            $table->integer('pers_aval_id')->unsigned()->nullable();
            $table->foreign('pers_aval_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('credito');
    }
}
