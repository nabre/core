<?php

namespace Nabre\Forms\Builder\Collections;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class RelationsForm extends Structure
{
    function build()
    {
        $this->add('collection')->listLabel('class')->request('required');
    }
}
