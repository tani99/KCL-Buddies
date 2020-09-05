<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchemeTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheme_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('description', 2000);
            $table->timestamps();
        });
        DB::table('scheme_types')->insert([
            [
                'id' => 1,
                'name' => 'Deadline',
                'description' => 'Users must sign up before a set deadline.'
            ],
            [
                'id' => 2,
                'name' => 'Waves',
                'description' => 'New users can periodically sign up in batches/waves.'
            ],
            [
                'id' => 3,
                'name' => 'Manual',
                'description' => 'Scheme administrators may manually run the algorithm to pair new users.'
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
        Schema::dropIfExists('scheme_types');
    }
}
