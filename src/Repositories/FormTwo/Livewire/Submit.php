<?php

namespace Nabre\Repositories\FormTwo\Livewire;

use Nabre\Repositories\FormTwo\Field;

trait Submit
{
    function submit()
    {
        $this->form();
        $this->validateRules = $this->form->rules();

        dd($this->wireValues, $this->validateRules);
    }
}
