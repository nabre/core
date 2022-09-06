<?php

namespace Nabre\Forms\Admin\Users;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class RoleForm extends Structure {
    function build()
    {
        $this->add('name',Field::STATIC);
        $this->add('slug')->request('max:255');
    }
}
