<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_types', function (Blueprint $table) {
            $table->integer('id')->unsigned()->primary();
            $table->string('name_singular')->unique();
            $table->string('name_plural')->unique();
            $table->string('description');
            $table->timestamps();
        });
        DB::table('user_types')->insert([
            [
                'id' => 1,
                'name_singular' => 'Newbie',
                'name_plural' => 'Newbies',
                'description' => 'Students who are new to King\'s College London.'
            ],
            [
                'id' => 2,
                'name_singular' => 'Buddy',
                'name_plural' => 'Buddies',
                'description' => 'Students who have completed their first year at King\'s College London.'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_types');
    }
}
