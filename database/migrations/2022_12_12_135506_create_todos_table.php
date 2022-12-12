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
        Schema::create('todos', function (Blueprint $table) {
            $table->id();
            $table->string('CompanyName')->nullable();
            $table->string('CompanyType')->nullable();
            $table->string('CompanyCode')->nullable();

            $table->string('Start')->nullable();
            $table->string('End')->nullable();
            $table->string('InvTypeCode')->nullable();
            $table->string('RatePlanCode')->nullable();

            $table->string('InvCount')->nullable();
            $table->string('CountType')->nullable();
            $table->integer ('ParentId')->nullable();
            

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
        Schema::dropIfExists('todos');
    }
};
