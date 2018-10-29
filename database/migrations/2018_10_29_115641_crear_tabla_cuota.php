<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaCuota extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuota', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('parte_capital',10,2);
            $table->decimal('interes',10,2);
            $table->date('fecha_programada_pago')->nullable();
            $table->date('fecha_pago')->nullable();
            $table->char('estado',1)->nullable();//C=>cancelado P=pendiente
            $table->integer('credito_id')->unsigned();
            $table->foreign('credito_id')->references('id')->on('credito')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('cuota');
    }
}
