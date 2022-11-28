<?php

use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Show;

/**
 * Dcat-admin - admin builder based on Laravel.
 * @author jqh <https://github.com/jqhph>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 *
 * extend custom field:
 * Dcat\Admin\Form::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Column::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Filter::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Grid::resolving(function (Grid $grid) {
    // 禁用行选择器
    $grid->disableRowSelector();
    // 禁用刷新按钮
    $grid->disableRefreshButton();
    // 禁用创建按钮
    $grid->disableCreateButton();

    $grid->actions(function (Grid\Displayers\Actions $actions) {
        // 禁用编辑按钮
        $actions->disableEdit();
        // 禁用删除按钮
        $actions->disableDelete();
        // 禁用详情按钮
        $actions->disableView();
    });

    $grid->filter(function (Grid\Filter $filter) {
        $filter->panel();

        $filter->expand();
    });
});

Form::resolving(function (Form $form) {
    // 禁用继续创建选择框
    $form->disableCreatingCheck();
    // 禁用继续编辑选择框
    $form->disableEditingCheck();
    // 禁用查看选择框
    $form->disableViewCheck();

    $form->tools(function (Form\Tools $tools) {
        // 禁用删除按钮
        $tools->disableDelete();
        // 禁用查看按钮
        $tools->disableView();
   });
});

Show::resolving(function (Show $show) {
    // 禁用编辑按钮
    $show->disableEditButton();
    // 禁用删除按钮
    $show->disableDeleteButton();
});
