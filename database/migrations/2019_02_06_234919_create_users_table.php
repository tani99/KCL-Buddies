<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email', 254);
            $table->string('password')->nullable();
            $table->string('department', 100)->nullable();
            $table->string('avatar')->nullable();
            $table->string('nickname', 32)->nullable();
            $table->tinyInteger('gender')->unsigned()->default(4);
            $table->date('birthdate')->nullable();
            $table->string('country')->nullable();
            $table->string('bio', 250)->default('');
            $table->string('alt_email', 254)->nullable();
            $table->string('phone_number', 12)->nullable()->unique();
            $table->boolean('profile_private')->default(false);
            $table->boolean('banned')->default(false);
            $table->rememberToken();
            $table->timestamps();
        });
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Root',
                'email' => 'no.reply@kcl.ac.uk',
                'password' => Hash::make('password123')
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
        Schema::dropIfExists('users');
    }
}
