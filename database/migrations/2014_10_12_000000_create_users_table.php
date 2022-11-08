<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->unsignedBigInteger('person_id');
            $table->enum('rol', ['Cliente', 'Administrador'])->default('Cliente');
            $table->string('email')->unique();
            $table->string('user')->unique();
            $table->string('password');
            $table->string('token')->nullable();
            $table->string('token_recovery')->nullable();
            $table->boolean('isActive');
            $table->timestamps();
        });

        Schema::table('users', function ($table) {
            $table->foreign('person_id')->references('id')->on('persons');
        });
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
