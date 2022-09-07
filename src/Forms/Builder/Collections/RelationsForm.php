<?php

namespace Nabre\Forms\Builder\Collections;

use Nabre\Models\Collection;
use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class RelationsForm extends Structure
{
    function build()
    {
        $this->add('collection')->listLabel('class')->request('required');
    }

    function queryCollection(){
        return Collection::whereDoesntHave('relation')->orWhere('_id',optional($this->data->collection)->_id)->get();
    }
}
