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
    var $wireValues = [];
    var $output = null;

    private $parent;
    private $values;
    private $prefix;

    function mount()
    {
        $model = data_get($this->embed, 'parent.model');
        $id = data_get($this->embed, 'parent.dataKey');
        $this->prefix = data_get($this->embed, 'parent.variable');
        $this->parent = is_null($id) ? $model::make() : $model::findOrFail($id);
        $this->values = $this->parent->readValue($this->prefix);

        $this->model = data_get($this->embed, 'wire.model');
        $this->form = data_get($this->embed, 'wire.form');
        $this->output = data_get($this->embed, 'wire.output');
        $this->values();
    }

    function addItem()
    {
        $this->wireValues[] = $this->array();
    }

    function removeItem(int $id){
        unset($this->wireValues[$id]);
        $this->wireValues = array_values($this->wireValues);
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
                $this->wireValues = $this->array($this->values ?? null);
                break;
        }
    }

    private function array($data = null)
    {
        $data = $data ?? $this->model::make();
        return (new $this->form)->data($data)->embedMode()->values();
    }

    private function moveButton()
    {
        $btn = Html::div('<i class="fa-solid fa-grip-vertical"></i>', ['class' => 'btn btn-dark btn-sm h-100']);
        return Html::div($btn, ['class' => 'col-auto']);
    }

    private function removeButton(int $num)
    {
        $btn = Html::div(
            '<i class="fa-solid fa-trash-can"></i>',
            [
                'title' => 'Elimina',
                'wire:click' => "removeItem($num)",
                'class' => 'btn btn-danger btn-sm h-100',
            ]
        );
        return Html::div($btn, ['class' => 'col-auto']);
    }

    private function addButton()
    {
        return Html::tag(
            'button',
            '<i class="fa-regular fa-square-plus"></i>',
            [
                'title' => 'Aggiungi',
                'wire:click' => 'addItem',
                'type' => 'button',
                'class' => " text-center list-group-item list-group-item-action list-group-item-success"
            ]
        );
    }

    private function itemRender($num = null)
    {
        $prefix = $this->prefix . (!is_null($num) ? '.' . $num : '');
        $wire = 'wireValues' . (!is_null($num) ? '.' . $num : '');
        return (new $this->form)->model($this->model)->embedMode($prefix, $wire)->generate();
    }

    private function generate()
    {
        $this->html = '';
        switch ($this->output) {
            case Field::EMBEDS_MANY:
                $this->html .= collect($this->wireValues)->keys()->map(function ($num) {
                    $html = '';
                    $html .= (count($this->wireValues) > 1) ? $this->moveButton() : null;
                    $html .= Html::div($this->itemRender($num), ['class' => 'col']);
                    $html .= $this->removeButton($num);
                    return (string) Html::div(Html::div($html, ['class' => 'row']), ['class' => 'list-group-item p-1']);
                })->implode('');
                $this->html .= $this->addButton();
                break;
            case Field::EMBEDS_ONE:
                $this->html = $this->itemRender();
                break;
        }
    }

    public function render()
    {
        $this->generate();
        $param = [];
        switch ($this->output) {
            case Field::EMBEDS_MANY:
                $param = ['class' => 'list-group'];
                break;
            case Field::EMBEDS_ONE:
                $param = [];
                break;
        }

        return '<div>' . Html::div($this->html, $param) . '</div>';
    }
}
