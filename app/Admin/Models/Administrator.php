<?php

namespace App\Admin\Models;

use Dcat\Admin\Models\Administrator as BaseAdministrator;
use Illuminate\Support\Collection;

class Administrator extends BaseAdministrator
{
    /**
     * {@inheritdoc}
     */
    public function allPermissions(): Collection
    {
        if ($this->allPermissions) {
            return $this->allPermissions;
        }

        // 全部权限
        $allPermissions = Permission::all()->keyBy('id');

        // 属于此用户的所有角色的权限集合
        $rolePermissions = $this->roles
            ->pluck('permissions')
            ->flatten()
            ->keyBy('id');

        // 全部的角色权限(角色权限及其所有上级)
        $allRolePermissions = $rolePermissions->collect();

        foreach ($rolePermissions as $permission) {
            if ($allRolePermissions->has($permission->parent_id)) {
                continue;
            }

            $parent = $allPermissions->get($permission->parent_id);

            while ($parent !== null) {
                $allRolePermissions->put($parent->id, $parent);

                $parent = $allPermissions->get($parent->parent_id);
            }
        }

        return $this->allPermissions = $allRolePermissions;
    }

    /**
     * {@inheritdoc}
     */
    public function can($ability): bool
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if (! $ability) {
            return false;
        }

        $permissions = $this->allPermissions();

        return $permissions->pluck('slug')->contains($ability) ?:
            $permissions
            ->pluck('id')
            ->contains($ability);
    }

    /**
     * {@inheritdoc}
     */
    public function canSeeMenu($menu)
    {
        $slug = $menu['slug'] ?? null;

        if ($slug === null) {
            return true;
        }

        return $this->can($slug);
    }
}
