<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->tinyInteger('gender')->default(0)->comment('性别: 0 未知, 1 男, 2 女');
            $table->date('birthday')->nullable()->comment('生日');
            $table->string('location')->nullable()->comment('位置');
            $table->tinyInteger('status')->default(0)->comment('状态: 0 未激活, 1 激活的');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
