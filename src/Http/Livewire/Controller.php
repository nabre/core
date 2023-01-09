<?php

namespace Nabre\Http\Livewire;

use Livewire\Component;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Str;
use Nabre\Repositories\FormTwo\Field;

class Controller extends Component
{
    var $controller;

    function mount()
    {
    }

    function index(){
        $this->controller=__FUNCTION__;
        $this->mount();
        return $this->render();
    }

    function render(){
        return '<div>Controller</div>';
    }
}
