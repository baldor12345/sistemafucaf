<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersHistorialAccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_accion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('cantidad');
            $table->char('estado',1)->nullable();//C=compra V=venta
            $table->timestamp('fecha')->nullable();
            $table->string('descripcion', 400)->nullable();
            $table->integer('persona_id')->unsigned();
            $table->integer('configuraciones_id')->unsigned()->nullable();
            $table->integer('caja_id')->unsigned()->nullable();
            $table->integer('concepto_id')->unsigned()->nullable();
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('caja_id')->references('id')->on('caja')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('configuraciones_id')->references('id')->on('configuraciones')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('concepto_id')->references('id')->on('concepto')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('historial_accion');
    }
}
