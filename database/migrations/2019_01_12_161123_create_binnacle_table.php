<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBinnacleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('binnacle', function (Blueprint $table) {
            $table->increments('id');
            $table->char('action', 1); // C->create, R->Read, U->Update, D->Delete
            $table->timestamp('date');
            $table->string('ip', 40);
            $table->integer('user_id')->unsigned();
            $table->integer('recordid')->unsigned();
            $table->string('table', 200);
            $table->text('detail');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('usuario')->onDelete('restrict')->onUpdate('restrict');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('binnacle');
    }
}
