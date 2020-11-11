<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecretSantasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('secret_santas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('party_id');
            $table->unsignedBigInteger('from_id');
            $table->unsignedBigInteger('to_id');
            $table->timestamps();

            $table->foreign('party_id')->references('id')->on('parties');
            $table->foreign('from_id')->references('id')->on('participants');
            $table->foreign('to_id')->references('id')->on('participants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('secret_santas');
    }
}
