<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPermisoOperacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permiso_operacion', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('operacionmenu_id')->unsigned();
            $table->foreign('operacionmenu_id')->references('id')->on('operacion_menu')->onDelete('restrict')->onUpdate('restrict');
            $table->integer('usertype_id')->unsigned();
            $table->foreign('usertype_id')->references('id')->on('usertype')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('permiso_operacion');
    }
}
