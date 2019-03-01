<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTwoFactorAuthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('two_factor_auths', function (Blueprint $table) {
            $table->string('id')->nullable();

            // Laravel 5.8 changed the type of "id" on "users" from "increments"
            // to "bigIncrements". Hence, we potentially could run into trouble:
            //
            // 1. We update from 5.7 to 5.8 and keep using "increments"
            // 2. We start a fresh Laravel project which uses "bigIncrements"
            //
            // Both scenarios are possible and we need to be able to account for
            // it. The only way to do that in a reliable way is to check the type
            // of "id" on "users" before we create our foreign key.
            //
            // Why is this not ideal?
            //
            // 1. There now is a dependency on doctrine/dbal
            // 2. This sort of conditional logic has no place in a migration file
            if (DB::getDoctrineSchemaManager()->listTableDetails('users')->getColumn('id')->getType() instanceof \Doctrine\DBAL\Types\IntegerType) {
                $table->increments('user_id');
            } else {
                $table->bigIncrements('user_id');
            }

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('two_factor_auths');
    }
}
