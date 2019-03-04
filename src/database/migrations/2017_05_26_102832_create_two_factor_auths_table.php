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

            if ($this->isUnsignedInteger()) {
                $table->unsignedInteger('user_id');
            } else {
                $table->unsignedBigInteger('user_id');
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

    /**
     * Determine if the column type for "user_id" should be of type "unsignedInteger"
     * using the type of "id" on "users" as a reference.
     *
     * Why do we do this?
     *
     * Laravel 5.8 changed the type of "id" on "users" from "increments"
     * to "bigIncrements". Hence, we potentially could run into trouble if we
     * don't support the following two potential scenarios
     *
     * 1. We update from 5.7 to 5.8, but decide to keep using "increments"
     * 2. We start a fresh Laravel project which uses "bigIncrements" by default
     *
     * The only way to account for both scenarios in a reliable way is to check
     * the type of "id" on "users" before we create our foreign key.
     *
     * Why is this not ideal?
     *
     * 1. There now is a dependency on doctrine/dbal
     * 2. We now have to use conditional logic in our migration file
     *
     * @return bool
     */
    private function isUnsignedInteger()
    {
        return DB::getDoctrineSchemaManager()
            ->listTableDetails('users')
            ->getColumn('id')
            ->getType() instanceof \Doctrine\DBAL\Types\IntegerType;
    }
}
