<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('method');
            $table->datetime('start_datetime');
            $table->datetime('end_datetime');
            $table->integer('court');
            $table->integer('num_of_members');
            $table->integer('num_of_guests');
            $table->integer('user_id')->nullable();
            $table->timestamps();
            $table->time('duration')->nullable();;
            $table->string('players')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}
