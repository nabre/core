<?php

namespace Nabre\Forms\Admin\Users;

use Nabre\Repositories\Form\Structure;

class PermissionForm extends Structure {
    function build()
    {
        $this->add('name')->request('required|max:255');
        $this->add('slug')->request('max:255');
    }
}
