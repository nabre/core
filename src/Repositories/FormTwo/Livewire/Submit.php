<?php

namespace Nabre\Repositories\FormTwo\Livewire;

use Nabre\Repositories\FormTwo\Field;

trait Submit
{
    function submit()
    {
        $this->form();
        $success = $this->form->save($this->wireValues);

        if ($success) {
        } else {
        }
    }
}
