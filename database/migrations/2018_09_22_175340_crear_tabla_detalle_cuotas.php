<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaDetalleCuotas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_cuotas', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('capital',10,2);
            $table->decimal('interes',10,2);
            $table->date('fecha_pago')->nullable();
            $table->char('situacion',1)->nullable();//C=>cancelado P=pendiente
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
        Schema::dropIfExists('detalle_cuotas');
    }
}
