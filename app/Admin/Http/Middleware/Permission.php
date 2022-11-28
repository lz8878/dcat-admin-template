<?php

namespace App\Admin\Http\Middleware;

use Closure;
use Dcat\Admin\Admin;
use Dcat\Admin\Http\Auth\Permission as Checker;
use Dcat\Admin\Http\Middleware\Authenticate;
use Dcat\Admin\Support\Helper;
use Illuminate\Http\Request;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (
            ! config('admin.permission.enable')
            || $this->shouldPassThrough($request)
            || $this->checkRoutePermission($request)
        ) {
            return $next($request);
        }

        Checker::error();
    }

    /**
     * 检查路由权限
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function checkRoutePermission(Request $request): bool
    {
        if ($user = Admin::user()) {
            $ability = $request->route()->getName();

            foreach ([
                'index' => 'list',
                'show' => 'view',
                // 'create' => 'create',
                'store' => 'create',
                'edit' => 'update',
                // 'update' => 'update',
                'destroy' => 'delete',
            ] as $method => $value) {
                $ability = preg_replace("/(.*)\.{$method}$/", '${1}.'.$value, $ability);
            }

            return $user->can($ability);
        }

        return false;
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isApiRoute($request)
    {
        return $request->routeIs(admin_api_route_name('*'));
    }

    /**
     * 确认请求的资源是否可直接访问
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function shouldPassThrough($request)
    {
        if ($this->isApiRoute($request) || Authenticate::shouldPassThrough($request)) {
            return true;
        }

        $excepts = array_merge(
            (array) config('admin.permission.except', []),
            Admin::context()->getArray('permission.except')
        );

        foreach ($excepts as $except) {
            if ($request->routeIs($except) || $request->routeIs(admin_route_name($except))) {
                return true;
            }

            $except = admin_base_path($except);

            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if (Helper::matchRequestPath($except)) {
                return true;
            }
        }

        return false;
    }
}
