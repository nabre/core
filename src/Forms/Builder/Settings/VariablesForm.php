<?php

namespace Nabre\Forms\Builder\Settings;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class VariablesForm extends Structure
{
    function build()
    {
        $this->add(config('setting.database.key'), Field::STATIC);
        $this->add('name')->request('max:255');
        $this->add('description', Field::TEXTAREA);
        $this->add('type');
        $this->add('user_set');
    }
}
