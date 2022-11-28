<?php

namespace Database\Seeders;

use App\Admin\Models\Menu;
use App\Admin\Models\Permission;
use Illuminate\Database\Seeder;

class AdminPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nodes = [
            /*
            |--------------------------------------------------------------------------
            | 主页
            |--------------------------------------------------------------------------
            */
            'dashboard' => [
                'name' => '主页',
                'icon' => 'fa fa-home',
                'uri' => '/',
                'children' => []
            ],

            /*
            |--------------------------------------------------------------------------
            | 系统管理
            |--------------------------------------------------------------------------
            */
            'system' => [
                'name' => '系统管理',
                'icon' => 'feather icon-settings',
                'uri' => '',
                'children' => [
                    'administrators' => [
                        'name' => '用户管理',
                        'icon' => '',
                        'uri' => 'system/administrators',
                        'resource' => ['list', 'create', 'update', 'view'],
                        'children' => [],
                    ],
                    'roles' => [
                        'name' => '角色管理',
                        'icon' => '',
                        'uri' => 'system/roles',
                        'resource' => ['list', 'create', 'update', 'delete', 'view'],
                        'children' => [],
                    ],
                ],
            ],
        ];

        $this->handleMenus($nodes);
        $this->handlePermissions($nodes);
    }

    /**
     * 处理系统菜单
     *
     * @param array $nodes
     * @param \App\Admin\Models\Menu|null $parent
     * @return void
     */
    protected function handleMenus(array $nodes, ?Menu $parent = null)
    {
        if ($parent === null) {
            Menu::query()->update(['show' =>  0]);
        }

        foreach ($nodes as $slug => $node) {
            if (! is_array($node) || ! array_key_exists('uri', $node)) {
                continue;
            }

            $menu = Menu::updateOrCreate([
                'slug' => ($parent->slug ?? 'dcat.admin') . '.' . $slug,
            ], [
                'parent_id' => $parent->id ?? 0,
                'title' => $node['name'],
                'icon' => $node['icon'],
                'uri' => $node['uri'],
                'show' => 1
            ]);

            $this->handleMenus($node['children'] ?? [], $menu);
        }

        // 删除无效的菜单
        if ($parent === null) {
            Menu::where('show', 0)->delete();
        }
    }

    /**
     * 处理系统权限
     *
     * @param array $nodes
     * @param \App\Admin\Models\Permission|null $parent
     * @return void
     */
    protected function handlePermissions(array $nodes, ?Permission $parent = null): void
    {
        foreach ($nodes as $slug => $node) {
            $permission = Permission::updateOrCreate([
                'slug' => ($parent->slug ?? 'dcat.admin') . '.' . $slug,
            ], [
                'parent_id' => $parent->id ?? 0,
                'name' => is_array($node) ? $node['name'] : $node,
            ]);

            if (! is_array($node)) {
                continue;
            }

            // 处理资源路由权限
            if (array_key_exists('resource', $node)) {
                $abilityMap = [
                    'list' => '列表',
                    'create' => '新增',
                    'update' => '编辑',
                    'delete' => '删除',
                    'view' => '查看',
                ];

                $abilities = [];

                if (is_array($node['resource'])) {
                    $abilities = $node['resource'];
                } elseif ($node['resource'] === true) {
                    $abilities = array_keys($abilityMap);
                }

                foreach ($abilities as $ability) {
                    Permission::updateOrCreate([
                        'slug' => $permission->slug . '.' . $ability,
                    ], [
                        'parent_id' => $permission->id,
                        'name' => $abilityMap[$ability],
                    ]);
                }
            }

            $this->handlePermissions($node['children'] ?? [], $permission);
        }
    }
}
