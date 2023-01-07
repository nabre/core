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
    var $wireValues = null;
    private $parent;
    private $output;
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
        $this->output = data_get($this->embed, 'wire.output');
        $this->values();

        return $this;
    }

    private function values()
    {
        switch ($this->output) {
            case Field::EMBEDS_MANY:
                $this->wireValues = [];
                $this->values->each(function ($item) {
                    $this->wireValues[] = $this->array($item);
                });
                break;
            case Field::EMBEDS_ONE:
                $item = $this->values ?? $this->model::make();
                $this->wireValues = $this->array($item);
                break;
        }
    }

    private function array($data)
    {
        return [];
    }

    private function generate()
    {
        switch ($this->output) {
            case Field::EMBEDS_MANY:
                $this->html .= collect($this->wireValues)->keys()->map(function ($num) {
                    return '<div class="row">
                                <div class="col-auto">|</div>
                                <div class="col">' . $this->itemRender($num) . '</div>
                                <div class="col-auto">Rimuovi</div>
                            </div>';
                })->implode('');
                $this->html .= $this->addButton();
                break;
            case Field::EMBEDS_ONE:
                $this->html = $this->itemRender();
                break;
        }
    }

    private function addButton()
    {
        return '<div>Aggiungi</div>';
    }

    private function itemRender($num = null)
    {
        $prefix = null;
        $wire = 'wireValues' . !is_null($num) ? '.' . $num : '';
        return (new $this->form)->model($this->model)->embedMode($prefix, $wire)->generate();
    }

    public function render()
    {
        return '<div>' . $this->html . '</div>';
    }
}
