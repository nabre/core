<?php

namespace Nabre\Tables\Admin\Users;

use Nabre\Repositories\Table\Structure;
use App\Models\Role as Model;

class RoleTable extends Structure
{
    var $model = Model::class;

    function columns()
    {
        return ['eti', 'name'];
    }

    function actions()
    {
        return [
            'edit' => 'nabre.admin.users.role.edit'
        ];
    }

    function query()
    {
        return $this->model::get()->sortBy('eti')->values();
    }
}
