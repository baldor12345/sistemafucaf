<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaTransaccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaccion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('acciones_id')->unsigned()->nullable();
            $table->integer('detalle_cuotas_id')->unsigned()->nullable();
            $table->integer('ahorros_id')->unsigned()->nullable();
            $table->integer('gastos_id')->unsigned()->nullable();
            $table->integer('credito_id')->unsigned()->nullable();
            $table->integer('caja_id')->unsigned()->nullable();
            $table->timestamp('fecha')->nullable();
            $table->foreign('acciones_id')->references('id')->on('acciones')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('detalle_cuotas_id')->references('id')->on('detalle_cuotas')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('ahorros_id')->references('id')->on('ahorros')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('gastos_id')->references('id')->on('gastos')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('credito_id')->references('id')->on('credito')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('caja_id')->references('id')->on('caja')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('transaccion');
    }
}
