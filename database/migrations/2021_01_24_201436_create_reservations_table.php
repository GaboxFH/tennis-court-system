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
            $table->string('name');
            $table->string('method');
            $table->date('date');
            $table->bigInteger('start');
            $table->bigInteger('end');
            $table->time('duration');
            $table->string('category');//court_num
            $table->integer('num_of_members');
            $table->integer('num_of_guests');
            $table->integer('host_id')->nullable();
            $table->integer('reoccur_id')->nullable();
            $table->boolean('timed');
            $table->timestamps();
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
