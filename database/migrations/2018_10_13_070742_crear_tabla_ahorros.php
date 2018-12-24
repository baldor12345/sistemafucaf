<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAhorros extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ahorros', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('importe',20,2);
            $table->timestamp('fecha_deposito')->nullable();
            $table->timestamp('fecha_retiro')->nullable();
            $table->decimal('interes',10,2);
            $table->char('estado', 1);
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
        Schema::dropIfExists('ahorros');
    }
}
