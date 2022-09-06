<?php

namespace Nabre\Forms\Builder\Navigation\Menu;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class AutoForm extends Structure {
    function build()
    {
        $this->add('page')->listLabel('uri')->listEmpty('-Seleziona-')->requestRequired();
        $this->add('icon');
        $this->add('text');
    }

    function queryPage(){
        return $this->model->where('folder',true)->get();
    }
}
