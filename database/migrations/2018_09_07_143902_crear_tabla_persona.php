<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaPersona extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('persona', function (Blueprint $table) {
            $table->increments('id');

            $table->string('codigo', 40)->nullable();
            $table->char('dni',8)->nullable();
            $table->string('nombres', 100)->nullable();
            $table->string('apellidos', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            //en caso de ser menor de edad =>apoderado
            $table->integer('apoderado_id')->unsigned()->nullable();
            $table->char('sexo', 1)->nullable(); // M->Masculino, F->Femenino
            $table->char('estado_civil', 2)->nullable(); // S->Soltero, C=>Casado, V=>Viudo, D=>Divorciado
            $table->integer('personas_en_casa')->nullable();
            $table->string('direccion', 80)->nullable();
            $table->string('ocupacion', 80)->nullable();
            $table->string('email',60)->nullable();
            $table->string('telefono_fijo',18)->nullable();
            $table->string('telefono_movil1',18)->nullable();
            $table->string('telefono_movil2',18)->nullable();
            $table->decimal('ingreso_personal',10,2)->nullable();
            $table->decimal('ingreso_familiar',10,2)->nullable();
            $table->char('estado', 1)->nullable();
            //acciones
            $table->char('tipo',2)->nullable(); // S=>socio, C=>Cliente, 2=>SC
            $table->date('fechai')->nullable();
            $table->foreign('apoderado_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('persona');
    }
}
