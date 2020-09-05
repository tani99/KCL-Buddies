<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_types', function (Blueprint $table) {
            $table->integer('id');
            $table->primary('id');
            $table->string('name');
            $table->unique('name');
            $table->timestamps();
        });
        DB::table('question_types')->insert([
            [
                'id' => -2,
                'name' => 'preference_age'
            ],
            [
                'id' => -1,
                'name' => 'preference_gender'
            ],
            [
                'id' => 1,
                'name' => 'number_range'
            ],
            [
                'id' => 2,
                'name' => 'ranking'
            ],
            [
                'id' => 3,
                'name' => 'location'
            ],
            [
                'id' => 4,
                'name' => 'frequency'
            ],
            [
                'id' => 5,
                'name' => 'date'
            ],
            [
                'id' => 6,
                'name' => 'checkbox_multiple'
            ],
            [
                'id' => 7,
                'name' => 'number'
            ],
            [
                'id' => 8,
                'name' => 'colour'
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
        Schema::dropIfExists('question_types');
    }
}
