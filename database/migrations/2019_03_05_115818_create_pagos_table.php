<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('monto_pago',20, 7);
            $table->decimal('monto_recibido',20, 7);
            $table->decimal('parte_entregado', 20, 7);
            $table->char('estado',1)->nullable();//E=>vuelto entregado, F=>falta entregar vuelto
            $table->char('ini_tabla',2)->nullable();//inicial tabla: AC=>accion, AH=>ahorro, CR=>credito , etc
            $table->timestamp('fecha')->nullable();
            $table->integer('persona_id')->unsigned()->nullable();
            $table->integer('caja_id')->unsigned()->nullable();
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
        Schema::dropIfExists('pagos');
    }
}
