<?php

namespace Nabre\Forms\Manage;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class ImageForm extends Structure
{
    function build()
    {
        $this->add('src',null,'Percorso file','fake')->requestRequired();
    }
}
