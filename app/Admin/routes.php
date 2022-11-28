<?php

use App\Admin\Http\Controllers\AuthController;
use App\Admin\Http\Controllers\HomeController;
use App\Admin\Http\Controllers\System\AdministratorController;
use App\Admin\Http\Controllers\System\RoleController;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\Controllers\ExtensionController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::registerHelperRoutes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->resource('auth/extensions', ExtensionController::class, ['only' => ['index', 'store', 'update']]);

    $router->get('auth/login', [AuthController::class, 'getLogin']);
    $router->post('auth/login', [AuthController::class, 'postLogin']);
    $router->get('auth/logout', [AuthController::class, 'getLogout']);
    $router->get('auth/setting', [AuthController::class, 'getSetting']);
    $router->put('auth/setting', [AuthController::class, 'putSetting']);

    $router->get('/', [HomeController::class, 'index']);

    // 系统管理
    $router->resource('system/administrators', AdministratorController::class)->names('system.administrators');
    $router->resource('system/roles', RoleController::class)->names('system.roles');
});
