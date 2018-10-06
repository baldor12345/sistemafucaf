<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('login',400);
            $table->string('password',100);
            $table->date('fechai');
            $table->integer('usertype_id')->unsigned();
            $table->integer('persona_id')->unsigned();
            $table->rememberToken();
            $table->foreign('usertype_id')->references('id')->on('usertype')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('user');
    }
}
