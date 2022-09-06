<?php

namespace Nabre\Tables\Admin\Users;

use Nabre\Repositories\Table\Structure;
use App\Models\Permission as Model;

class PermissionTable extends Structure
{

    var $model = Model::class;

    function columns()
    {
        return ['eti', 'name'];
    }

    function actions()
    {
        return [
            'create' => 'nabre.admin.users.permission.create',
            'edit' => 'nabre.admin.users.permission.edit',
            'destroy' => 'nabre.admin.users.permission.destroy'
        ];
    }

    function query()
    {
        return $this->model::all()->sortBy('eti')->values();
    }
}
