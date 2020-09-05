<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->integer('id')->unsigned();
            $table->primary('id');
            $table->string('title', 100)->unique();
            $table->integer('type_id');
            $table->foreign('type_id')->references('id')->on('question_types')->onDelete('cascade');
            $table->string('user_type_ids', 1024)->default('[]');
            $table->string('validation', 1024)->nullable();
            $table->integer('insertion_order')->unsigned()->default(0);
            $table->timestamps();
        });
        DB::table('questions')->insert([
            [
                'id' => 0,
                'title' => 'Gender preference',
                'type_id' => -1,
                'user_type_ids' => '[1, 2]',
                'validation' => null,
                'insertion_order' => 2
            ],
            [
                'id' => 1,
                'title' => 'Cuisines',
                'type_id' => 1,
                'user_type_ids' => '[1, 2]',
                'validation' => '{"min":-2,"max":2,"options":5}',
                'insertion_order' => 0
            ],
            [
                'id' => 2,
                'title' => 'Activities',
                'type_id' => 2,
                'user_type_ids' => '[1, 2]',
                'validation' => '{"options":6}',
                'insertion_order' => 0
            ],
            [
                'id' => 3,
                'title' => 'Travel destination',
                'type_id' => 3,
                'user_type_ids' => '[1, 2]',
                'validation' => null,
                'insertion_order' => 0
            ],
            [
                'id' => 4,
                'title' => 'Age',
                'type_id' => 7,
                'user_type_ids' => '[1, 2]',
                'validation' => '{"min":0,"max":120}',
                'insertion_order' => 0
            ],
            [
                'id' => 5,
                'title' => 'Interests',
                'type_id' => 6,
                'user_type_ids' => '[1, 2]',
                'validation' => '{"options":10}',
                'insertion_order' => 0
            ],
            [
                'id' => 6,
                'title' => 'Favourite colour',
                'type_id' => 8,
                'user_type_ids' => '[1, 2]',
                'validation' => null,
                'insertion_order' => 0
            ],
            [
                'id' => 7,
                'title' => 'Age preference',
                'type_id' => -2,
                'user_type_ids' => '[1, 2]',
                'validation' => null,
                'insertion_order' => 1
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
        Schema::dropIfExists('questions');
    }
}
