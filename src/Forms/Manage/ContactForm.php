<?php

namespace Nabre\Forms\Manage;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class ContactForm extends Structure
{
    function build()
    {
        $this->add('firstname')->requestRequired();
        $this->add('lastname')->requestRequired();
        $this->add('email');
        $this->add('phone');
        $this->add('permissions',Field::SELECT)->listLabel('eti');
    }
}
