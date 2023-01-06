<?php

namespace Nabre\Http\Livewire;

use Livewire\Component;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Str;

class FormEmbed extends Component
{
    var $embed;
    private $parent;
    private $values;

    function mount()
    {
        $this->loadValue();
    }

    private function loadValue(){
        $model = data_get($this->embed,'parent.model');
        $id = data_get($this->embed,'parent.dataKey');
        $variabe = data_get($this->embed,'parent.variable');
        $this->parent=$model::findOrFail($id);
        $this->values=$this->parent->readValue($variabe);
        return $this;
    }

    public function render()
    {
        return '<div>' . var_export($this->embed) .'</div>';
    }
}
