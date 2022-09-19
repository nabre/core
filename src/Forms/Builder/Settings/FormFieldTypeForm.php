<?php

namespace Nabre\Forms\Builder\Settings;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class FormFieldTypeForm extends Structure
{
    function build()
    {
        $this->add(config('setting.database.key'), Field::STATIC);
        $this->add('name')->request('max:255');
    }
}
