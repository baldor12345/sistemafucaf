<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('directivos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo', 200)->nullable();
            $table->timestamp('periodoi')->nullable();
            $table->timestamp('periodof')->nullable();
            $table->char('estado',1)->nullable();
            $table->string('descripcion', 200)->nullable();
            $table->integer('presidente_id')->unsigned()->nullable();
            $table->integer('tesorero_id')->unsigned()->nullable();
            $table->integer('secretario_id')->unsigned()->nullable();
            $table->integer('vocal_id')->unsigned()->nullable();
            $table->foreign('presidente_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('tesorero_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('secretario_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('vocal_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('directivos');
    }
}
