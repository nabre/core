<?php

namespace Nabre\Http\Livewire;

use Livewire\Component;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Repositories\Form\Build;
use Nabre\Repositories\Form\Field;
use Illuminate\Support\Str;

class FormEmbedsMany extends Component
{

    var $value;
    var $prefix;
    var $embedsModel;
    var $toPut;
    var $create;
    var $item;
    protected $method;
    protected $embedsForm;
    protected $formStructure;

    function mount()
    {
        $this->generate();
    }

    function add()
    {
        $this->value = $this->value->push([]);
        $this->generate();
    }

    function remove($id)
    {
        $this->value = $this->value->reject(function ($v, $k) use ($id) {
            return $k == $id;
        })->values();
        $this->generate();
    }

    function updateOrder($list)
    {
        $list = collect($list)->pluck('order', 'value')->toArray();
        $this->value = $this->value->map(function ($v, $k) use ($list) {
            $v['order'] = $list[$k] ?? null;
            return $v;
        })->sortBy('order')->values();
        $this->generate();
    }

    protected function generate()
    {
        collect(['create', 'destroy', 'sort'])->each(function ($method) {
            $this->method[$method] = $this->toPut['method'][$method] ?? true;
        });

        $this->embedFormStructure();
        $this->embedsItems();
    }

    protected function embedsItems()
    {
        $this->value = $this->value->map(function ($data, $prefixId) {
            return $this->embedFormRender($data, $prefixId);
        });
    }

    protected function embedFormRender($data = null, $prefixId = null)
    {
        if (!($data instanceof $this->embedsModel)) {
            $vars = $data;
            $data = new $this->embedsModel;
            $data = $data->recursiveSave($vars);
        }

        $prefixId = $prefixId ?? count((array) $this->embedsForm);
        $eForm = $this->formStructure;
        $eForm = $eForm->data($data);
        $eForm = $eForm->generate();
        $eForm = $eForm->add('_id', Field::HIDDEN)->lastInsert();
        $build = (new Build)->structure($eForm);
        $build->prefix = $this->prefix . '.' . $prefixId;
        $build->wireVar = 'value.' . $prefixId;
        $embed = $build->embedHtml($data);

        $handle = !$this->method['sort'] ? null : '<div class="col-auto" wire:sortable.handle ><i class="fa-solid fa-grip"></i></div>';
        $destroy = !$this->method['destroy'] ? null : '<div class="col-auto"><button type="button" wire:click="remove(' . $prefixId . ')" class="btn btn-danger btn-sm" ><i class="fa-regular fa-trash-can"></i></button></div>';
        $this->embedsForm .= '<li class="list-group-item" ' . (!$this->method['sort'] ? null : 'wire:sortable.item="' . $prefixId . '"') . ' >
        <div class="row">
        ' . $handle . '
        <div class="col">' . $embed . '</div>' . $destroy . '
        </div></li>';

        return $build->elementsFilter()->pluck('value', 'variable');
    }

    protected function embedFormStructure()
    {
        $eForm = new $this->toPut['form'];
        $eForm = $eForm->collection($this->embedsModel);
        $this->formStructure = $eForm;
        return $this;
    }

    public function render()
    {
        $create = !$this->method['create'] ? null :  '<li class="list-group-item"><div><button type="button" wire:click="add" class="btn btn-success btn-sm w-100"><i class="fa-solid fa-plus"></i></button></div></li>';
        return '<div><ul class="list-group" wire:sortable="updateOrder">' . $this->embedsForm . '' . $create . '</ul></div>';
    }
}
