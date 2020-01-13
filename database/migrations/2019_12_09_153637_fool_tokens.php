<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FoolTokens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('fool_tokens', function (Blueprint $table) {
            $table->string('token', 60)->primary();
            $table->unsignedInteger('user_id')->default(0)->comment('User ID');
            $table->string('guard', 30)->index()->default('')->comment('Guard');
            $table->string('remember_token', 60)->default('')->comment('R m');
            $table->timestamp('expired_at');
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
        //
        Schema::dropIfExists('fool_tokens');
    }
}
