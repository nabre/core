<?php

namespace Nabre\Forms\Builder\Navigation\Menu;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class CustomForm extends Structure
{
    function build()
    {
        $this->add('string')->requestRequired();
        $this->add('icon');
        $this->add('text');
        $this->add('tree');
        $this->add('items')->embedsForm(CustomFormEmbedsItem::class, 'Gli elementi del menu possono essere aggiunti modificando il record.');
    }

    function queryPage()
    {
        return $this->model->where('folder', true)->get();
    }
}
