<?php

namespace Nabre\Http\Livewire;

use Livewire\Component;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Str;
use Nabre\Repositories\FormTwo\Field;

class FormEmbed extends Component
{
    var $embed = null;
    var $html = null;
    var $model = null;
    var $form = null;
    private $parent;
    private $values;

    function mount()
    {
        $this->loadValue();
        $this->generate();
    }

    private function loadValue()
    {
        $model = data_get($this->embed, 'parent.model');
        $id = data_get($this->embed, 'parent.dataKey');
        $variable = data_get($this->embed, 'parent.variable');
        $this->parent = is_null($id) ? $model::make() : $model::findOrFail($id);
        $this->values = $this->parent->readValue($variable);

        $this->model = data_get($this->embed, 'wire.model');
        $this->form = data_get($this->embed, 'wire.form');
        return $this;
    }

    private function generate()
    {
        switch (data_get($this->embed, 'wire.output')) {
            case Field::EMBEDS_MANY:
                $this->html .= $this->values->map(function ($item) {
                    return '<div class="row">
                                <div class="col-auto">|</div>
                                <div class="col">' . $this->itemRender($item) . '</div>
                                <div class="col-auto">Rimuovi</div>
                            </div>';
                })->implode('');
                $this->html .= $this->addButton();
                break;
            case Field::EMBEDS_ONE:
                $item = $this->values ?? $this->model::make();
                $this->html = $this->itemRender($item);
                break;
        }
    }

    private function addButton()
    {
        return '<div>Aggiungi</div>';
    }

    private function itemRender($data)
    {
        return (new $this->form)->data($data)->embedMode()->generate();
    }

    public function render()
    {
        return '<div>' . $this->html . '</div>';
    }
}
