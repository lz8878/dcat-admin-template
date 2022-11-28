<?php

namespace App\Admin\Models;

use Dcat\Admin\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    protected $sortable = [
        'sort_when_creating' => false,
    ];
}

