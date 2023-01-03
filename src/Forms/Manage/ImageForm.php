<?php

namespace Nabre\Forms\Manage;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class ImageForm extends Structure
{
    function build()
    {
        $this->add('src',Field::TEXT,'Percorso file','fake')->requestRequired();
    }
}
