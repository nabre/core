<?php

namespace Nabre\Tables\Admin\Users;

use Nabre\Repositories\Table\Structure;
use App\Models\User as Model;

class ImpersonateTable extends Structure
{

    var $model = Model::class;

    function table(){
        $this->setIcon('edit','fa-solid fa-person-walking-arrow-right','dark');
    }

    function columns()
    {
        return ['email', 'name'];
    }

    function actions()
    {
        return [
            'edit' => 'nabre.admin.users.impersonate.edit',
        ];
    }

    function query()
    {
        return $this->model::where('_id','!=',\Auth::user()->id)->get()->policy('update',Model::class)->sortBy('email')->values();
    }
}
