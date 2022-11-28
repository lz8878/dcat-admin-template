<?php

namespace App\Admin\Http\Controllers\System;

use App\Admin\Models\Permission as PermissionModel;
use App\Admin\Models\Role as RoleModel;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Auth\Permission;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Http\Repositories\Role;
use Dcat\Admin\Show;
use Dcat\Admin\Support\Helper;
use Dcat\Admin\Widgets\Tree;

class RoleController extends AdminController
{
    protected $translation = 'admin_role';

    protected function grid()
    {
        return new Grid(new Role(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');

            $grid->column('id', 'ID')->sortable();
            $grid->column('slug')->label('primary');
            $grid->column('name');

            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            if (Admin::user()->can('dcat.admin.system.roles.create')) {
                $grid->showCreateButton();
                $grid->enableDialogCreate();
            }

            $grid->actions(function (Grid\Displayers\Actions $actions) {
                if (! RoleModel::isAdministrator($actions->row->slug)) {
                    $actions->disableDelete(! Admin::user()->can('dcat.admin.system.roles.delete'));
                }

                $actions->disableQuickEdit(! Admin::user()->can('dcat.admin.system.roles.update'));
                $actions->disableView(! Admin::user()->can('dcat.admin.system.roles.view'));
            });

            $grid->filter(function ($filter) {
                $filter->like('slug')->width(4);
                $filter->like('name')->width(4);
            });
        });
    }

    protected function detail($id)
    {
        return Show::make($id, new Role('permissions'), function (Show $show) {
            $show->field('id');
            $show->field('slug');
            $show->field('name');

            $show->field('permissions')->unescape()->as(function ($permission) {
                $nodes = (new PermissionModel())->allNodes();

                $tree = Tree::make($nodes);

                if (RoleModel::isAdministrator($this->slug)) {
                    $tree->checkAll();
                } else {
                    $keyName = (new PermissionModel())->getKeyName();
                    $tree->check(
                        array_column(Helper::array($permission), $keyName)
                    );
                }

                return $tree->render();
            });

            $show->field('created_at');
            $show->field('updated_at');

            if ($show->getKey() == RoleModel::ADMINISTRATOR_ID) {
                $show->disableDeleteButton();
            }
        });
    }

    public function form()
    {
        return Form::make(Role::with(['permissions']), function (Form $form) {
            $roleTable = config('admin.database.roles_table');
            $connection = config('admin.database.connection');

            $id = $form->getKey();

            $form->display('id', 'ID');

            $form->text('slug', trans('admin.slug'))
                ->required()
                ->creationRules(['required', "unique:{$connection}.{$roleTable}"])
                ->updateRules(['required', "unique:{$connection}.{$roleTable},slug,$id"]);

            $form->text('name', trans('admin.name'))->required();

            $form->tree('permissions')
                ->nodes(function () {
                    $permissionModel = config('admin.database.permissions_model');
                    $permissionModel = new $permissionModel();

                    return $permissionModel->allNodes();
                })
                ->customFormat(function ($v) {
                    if (! $v) {
                        return [];
                    }

                    return array_column($v, 'id');
                });

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            if ($id == RoleModel::ADMINISTRATOR_ID) {
                $form->disableDeleteButton();
            }
        })->saved(function () {
            $model = config('admin.database.menu_model');
            (new $model())->flushCache();
        });
    }

    public function destroy($id)
    {
        if (in_array(RoleModel::ADMINISTRATOR_ID, Helper::array($id))) {
            Permission::error();
        }

        return parent::destroy($id);
    }
}
