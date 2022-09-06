<?php

namespace Nabre\Forms\Builder\Collections;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class FieldsForm extends Structure
{
    function build()
    {
        $this->add('class')->request('required');
        $this->add('title');
        $this->add('fields')->embedsForm(FieldsFormEmbedsField::class, 'Gli elementi del menu possono essere aggiunti modificando il record.',['sort'=>false,'create'=>false,'destroy'=>false]);
    }
}
