<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaOperacionMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operacion_menu', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operacion_id')->unsigned();
            $table->foreign('operacion_id')->references('id')->on('operacion')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('menuoption_id')->unsigned();
            $table->foreign('menuoption_id')->references('id')->on('menuoption')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('operacion_menu');
    }
}
