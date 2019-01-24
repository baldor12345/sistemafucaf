<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCertificadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificado', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('capital',20,2)->nullable();
            $table->integer('inicio')->nullable();
            $table->integer('fin')->nullable();
            $table->integer('num_acciones')->nullable();
            $table->string('codigo', 20)->nullable();
            $table->string('semestre', 40)->nullable();
            $table->char('estado',1)->nullable();
            $table->timestamp('fechai')->nullable();
            $table->timestamp('fechaf')->nullable();
            $table->integer('persona_id')->unsigned()->nullable();
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
        Schema::dropIfExists('certificado');
    }
}
