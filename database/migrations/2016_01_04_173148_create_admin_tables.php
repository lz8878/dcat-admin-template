<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection()
    {
        return $this->config('database.connection') ?: config('database.default');
    }

    public function config($key)
    {
        return config('admin.'.$key);
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($usersTable = $this->config('database.users_table'), function (Blueprint $table) {
            $table->id();
            $table->string('username', 120)->unique();
            $table->string('password');
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });

        Schema::create($rolesTable = $this->config('database.roles_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create($permissionsTable = $this->config('database.permissions_table'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('slug')->unique();
            $table->string('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->integer('order')->default(0);
            $table->bigInteger('parent_id')->default(0);
            $table->timestamps();
        });

        Schema::create($menuTable = $this->config('database.menu_table'), function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('slug')->unique();
            $table->string('icon', 50)->nullable();
            $table->string('uri')->nullable();

            $table->timestamps();
        });

        Schema::create($this->config('database.role_users_table'), function (Blueprint $table) use ($usersTable, $rolesTable) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->unique(['role_id', 'user_id']);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on($usersTable)
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($rolesTable)
                ->onDelete('cascade');
        });

        Schema::create($this->config('database.role_permissions_table'), function (Blueprint $table) use ($permissionsTable, $rolesTable) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('permission_id');
            $table->unique(['role_id', 'permission_id']);
            $table->timestamps();

            $table->foreign('permission_id')
                ->references('id')
                ->on($permissionsTable)
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($rolesTable)
                ->onDelete('cascade');
        });

        Schema::create($this->config('database.role_menu_table'), function (Blueprint $table) use ($menuTable, $rolesTable) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('menu_id');
            $table->unique(['role_id', 'menu_id']);
            $table->timestamps();

            $table->foreign('menu_id')
                ->references('id')
                ->on($menuTable)
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($rolesTable)
                ->onDelete('cascade');
        });

        Schema::create($this->config('database.permission_menu_table'), function (Blueprint $table) use ($menuTable, $permissionsTable) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('menu_id');
            $table->unique(['permission_id', 'menu_id']);
            $table->timestamps();

            $table->foreign('menu_id')
                ->references('id')
                ->on($menuTable)
                ->onDelete('cascade');

            $table->foreign('permission_id')
                ->references('id')
                ->on($permissionsTable)
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->config('database.role_users_table'));
        Schema::dropIfExists($this->config('database.role_permissions_table'));
        Schema::dropIfExists($this->config('database.role_menu_table'));
        Schema::dropIfExists($this->config('database.permission_menu_table'));
        Schema::dropIfExists($this->config('database.users_table'));
        Schema::dropIfExists($this->config('database.roles_table'));
        Schema::dropIfExists($this->config('database.permissions_table'));
        Schema::dropIfExists($this->config('database.menu_table'));
    }
};
