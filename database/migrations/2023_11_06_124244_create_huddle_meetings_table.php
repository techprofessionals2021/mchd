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
        Schema::create('huddle_meetings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->bigInteger('meeting_cundocter_id');
            $table->string('color');
            $table->date('meeting_date');
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
        Schema::dropIfExists('huddle_meetings');
    }
};
