<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlSocioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('control_socio', function (Blueprint $table) {
            $table->increments('id');
            $table->char('tardanza',1)->nullable();
            $table->char('inasistencia',1)->nullable();
            $table->char('estado',1)->nullable();
            $table->integer('persona_id')->unsigned();
            $table->integer('caja_id')->unsigned();
            $table->integer('concepto_id')->unsigned();
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('control_socio');
    }
}
