<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->integer('reservation_id');
            $table->integer('user_id');
            $table->string('type');
            $table->timestamps();
            $table->datetime('curr_start_datetime');
            $table->datetime('curr_end_datetime');
            $table->integer('curr_court');
            $table->datetime('prev_start_datetime')->nullable();
            $table->datetime('prev_end_datetime')->nullable();
            $table->integer('prev_court')->nullable();
            $table->integer('new_user_id')->nullable();
            $table->integer('deleted_user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
}