<?php

namespace App\Admin\Models;

use Dcat\Admin\Models\Menu as BaseMenu;

class Menu extends BaseMenu
{
    protected $fillable = [
        'parent_id',
        'order',
        'title',
        'slug' ,
        'icon',
        'uri',
        'extension',
        'show',
    ];
}

