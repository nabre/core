<?php

namespace Nabre\Forms\Builder\Navigation\Menu;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class CustomFormEmbedsItem extends Structure {
    function build()
    {
        $this->add('page')->requestRequired()->listLabel('uri');
    }
}
