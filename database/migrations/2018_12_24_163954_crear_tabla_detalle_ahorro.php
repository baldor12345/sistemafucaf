<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaDetalleAhorro extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_ahorro', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('capital',20,2);
            $table->decimal('interes',10,2);
            $table->timestamp('fecha_capitalizacion')->nullable();
            $table->integer('ahorros_id')->unsigned();
            $table->foreign('ahorros_id')->references('id')->on('ahorros')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('detalle_ahorro');
    }
}
