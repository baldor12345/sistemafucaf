<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaAcciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acciones', function (Blueprint $table) {
            $table->increments('id');
            $table->char('estado',1)->nullable();//C=compra V=venta
            $table->date('fechai')->nullable();
            $table->date('fechaf')->nullable();
            $table->string('descripcion', 400)->nullable();
            $table->integer('persona_id')->unsigned();
            $table->integer('configuraciones_id')->unsigned();
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('configuraciones_id')->references('id')->on('configuraciones')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('acciones');
    }
}
