<?php

namespace Nabre\Forms\Builder\Collections;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class FieldsFormEmbedsField extends Structure
{
    function build()
    {
        if (optional($this->data)->is_relation) {
            $relationName=optional($this->data)->relation_string;
            $this->addMsg('Il nome previsto rella relazione è: '. $relationName, 'info');
        }
        $this->add('icon_type');
        $this->add('name', Field::STATIC);
        $this->add('title');

    }
}
