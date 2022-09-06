<?php

namespace Nabre\Forms\Auth;

use App\Models\User;
use Nabre\Repositories\Form\Field;
use Nabre\Repositories\Form\Structure;

class UserRegisterForm extends Structure {
    var $collection=User::class;
    function build()
    {
        $this->add('email')->request('required|string|email|max:255|unique:users');
        $this->add('password')->request('required|string|min:8')->psw_confirm();
    }
}
