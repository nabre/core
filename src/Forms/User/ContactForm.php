<?php

namespace Nabre\Forms\User;

use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class ContactForm extends Structure
{
    function build()
    {
        $this->add('firstname')->requestRequired();
        $this->add('lastname')->requestRequired();
        $this->add('email')->requestRequired();
        $this->add('phone');
    }
}
