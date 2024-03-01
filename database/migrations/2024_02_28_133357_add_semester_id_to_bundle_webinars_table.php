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
        Schema::table('bundle_webinars', function (Blueprint $table) {
            $table->unsignedBigInteger('bundle_semester_id')->nullable()->after('webinar_id');
            $table->foreign('bundle_semester_id')->references('id')->on('bundle_semesters')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bundle_webinars', function (Blueprint $table) {
            $table->dropForeign('bundle_semester_id');
        });
    }
};
