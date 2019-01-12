<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CrearTablaTransaccion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaccion', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('fecha')->nullable();
            $table->decimal('monto',20,1);
            $table->decimal('interes_ahorro',20,1)->nullable();
            $table->decimal('cuota_parte_capital',20,1)->nullable();
            $table->decimal('cuota_interes',20,1)->nullable();
            $table->decimal('cuota_mora',20,1)->nullable();
            $table->decimal('acciones_soles',20,1)->nullable();
            $table->decimal('monto_ahorro',20,1)->nullable();
            $table->decimal('monto_credito',20,1)->nullable();
            $table->decimal('comision_voucher',20,1)->nullable();
            $table->decimal('ganancia_accion',20,1)->nullable();
            $table->decimal('otros_egresos',20,1)->nullable();
            $table->decimal('utilidad_distribuida',20,1)->nullable();
            $table->integer('id_tabla')->nullable();
            $table->char('inicial_tabla',2)->nullable();
            $table->integer('concepto_id')->unsigned()->nullable();
            $table->string('descripcion', 400)->nullable();
            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('usuario_id')->unsigned();
            $table->integer('caja_id')->unsigned();
            $table->foreign('concepto_id')->references('id')->on('concepto')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('persona_id')->references('id')->on('persona')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('usuario_id')->references('id')->on('user')->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('transaccion');
    }
}
