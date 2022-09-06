<?php

namespace Nabre\Forms\Admin\Users;

use App\Models\Role;
use Nabre\Repositories\Form\Structure;

class ListForm extends Structure
{
    function build()
    {
        $this->add('email')->request('required|max:255');

        $priority = optional(\Auth::user())->roles()->min("priority") ?? Role::max('priority');
        $sign = (\Auth::user()->id == $this->data->id && !is_null($this->data->id)) ? '<=' : '<';
        $disabled = Role::where('priority', $sign, $priority)->get()->modelKeys();

        $this->add('roles')->listLabel('eti')->disabled($disabled);
        $this->add('permissions')->listLabel('eti');
    }
}
