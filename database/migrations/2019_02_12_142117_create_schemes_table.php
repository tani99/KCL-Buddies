<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schemes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description', 2000);
            $table->integer('type_id')->unsigned();
            $table->foreign('type_id')->references('id')->on('scheme_types')->onDelete('cascade');
            $table->string('icon')->nullable();
            $table->string('departments', 2000)->nullable();
            $table->date('date_start');
            $table->date('date_end');
            $table->date('last_run')->nullable();
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
        Schema::dropIfExists('schemes');
    }
}
