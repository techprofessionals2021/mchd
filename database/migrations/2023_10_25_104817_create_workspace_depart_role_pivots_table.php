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
        Schema::create('workspace_depart_role_pivots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('depart_user_role_id'); 
            $table->unsignedBigInteger('user_workspace_id'); 

            $table->foreign('depart_user_role_id')
                  ->references('id') 
                  ->on('depart_user_roles'); 

            $table->foreign('user_workspace_id')
                 ->references('id') 
                  ->on('user_workspaces'); 
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
        Schema::dropIfExists('workspace_depart_role_pivots');
    }
};
