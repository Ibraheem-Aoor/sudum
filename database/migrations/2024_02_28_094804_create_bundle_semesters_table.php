<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        #2024_02_28_094804
        Schema::create('bundle_semesters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('max_number_of_hours')->comment('Max Hours Student Allows To Register In This Semester');
            $table->unsignedInteger('bundle_id');
            $table->foreign('bundle_id')->references('id')->on('bundles')->cascadeOnDelete();
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
        Schema::dropIfExists('bundle_semesters');
    }
};
