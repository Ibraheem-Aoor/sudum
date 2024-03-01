<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studyschedules', function (Blueprint $table) {

            $table->increments('id');
            $table->integer('bundle_id')->unsigned();
            $table->string('webinar_id');
            $table->string('day')->nullable();
            $table->string('date')->nullable();
            $table->string('start_time');
            $table->string('end_time');
            $table->text('note')->nullable();
            $table->timestamps();
            //$table->foreign('bundle_id')->on('bundles')->references('id')->cascadeOnDelete();
            //$table->foreign('webinar_id')->on('webinars')->references('id')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('studyschedules');
    }
};
