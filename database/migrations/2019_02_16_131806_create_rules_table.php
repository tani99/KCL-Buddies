<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unique('name');
            $table->string('description', 500);
            $table->string('default_value', 500)->nullable();
            $table->string('validation', 1000)->nullable();
            $table->timestamps();
        });
        DB::table('rules')->insert([
            [
                'id' => 1,
                'name' => 'Max buddies',
                'description' => 'The maximum number of buddies per group of newbies.',
                'default_value' => '1',
                'validation' => '{"type": "integer", "min": 1, "max": 2}'
            ],
            [
                'id' => 2,
                'name' => 'Max newbies',
                'description' => 'The maximum number of newbies per buddies-newbies matching.',
                'default_value' => '1',
                'validation' => '{"type": "integer", "min": 1}'
            ],
            [
                'id' => 3,
                'name' => 'Wave period',
                'description' => 'The number of weeks between each wave..',
                'default_value' => '2',
                'validation' => '{"type": "integer", "min": 1, "max": 52}'
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
        Schema::dropIfExists('rules');
    }
}
