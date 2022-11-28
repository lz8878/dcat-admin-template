<?php

namespace App\Admin\Http\Controllers\System;

use App\Admin\Models\Administrator as AdministratorModel;
use App\Admin\Models\Permission as PermissionModel;
use App\Admin\Models\Role as RoleModel;
use Dcat\Admin\Admin;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\UserController;
use Dcat\Admin\Http\Repositories\Administrator;

class AdministratorController extends UserController
{
    protected $translation = 'admin_administrator';

    protected function grid()
    {
        return Grid::make(Administrator::with(['roles']), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('username');
            $grid->column('name');

            if (config('admin.permission.enable')) {
                $grid->column('roles')->pluck('name')->label('primary', 3);

                $nodes = (new PermissionModel())->allNodes();
                $grid->column('permissions')
                    ->if(function () {
                        return ! $this->roles->isEmpty();
                    })
                    ->showTreeInDialog(function (Grid\Displayers\DialogTree $tree) use (&$nodes) {
                        $tree->nodes($nodes);

                        foreach (array_column($this->roles->toArray(), 'slug') as $slug) {
                            if (RoleModel::isAdministrator($slug)) {
                                $tree->checkAll();
                            }
                        }
                    })
                    ->else()
                    ->display('');
            }

            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            if (Admin::user()->can('dcat.admin.system.administrators.create')) {
                $grid->showCreateButton();
                $grid->enableDialogCreate();
            }

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if ($actions->getKey() != AdministratorModel::DEFAULT_ID) {
                    $actions->disableDelete(! Admin::user()->can('dcat.admin.system.administrators.delete'));
                }

                $actions->disableQuickEdit(! Admin::user()->can('dcat.admin.system.administrators.update'));
                $actions->disableView(! Admin::user()->can('dcat.admin.system.administrators.view'));
            });

            $grid->filter(function ($filter) {
                $filter->like('username')->width(4);
                $filter->like('name')->width(4);
            });
        });
    }
}
