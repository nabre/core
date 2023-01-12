<?php

namespace Nabre\Repositories\FormTwo\Livewire;

use Collective\Html\HtmlFacade as Html;
use Nabre\Repositories\FormTwo\Field;
use Nabre\Repositories\FormTwo\FormConst;

trait Put
{
    private function formGenerate()
    {
        $this->values();
        $this->printForm();
        $this->method=$this->form()->method;
        $this->title = $this->model . ": " . (is_null($this->idData) ? 'crea' : 'modifica')." istanza";
    }

    private function values()
    {
        $this->wireValues = $this->form()->values();
    }
    function info($i)
    {
        if ($this->haveError($i)) {
            return;
        }
        return collect(data_get($i, FormConst::INFO, []))->map(fn ($i) => (string) Html::div(data_get($i, 'text'), ['class' => 'badge text-bg-' . data_get($i, 'theme')]))->implode('<br>');
    }

    function embedItRemove($param, $id=null)
    {
        if(is_null($id)){
            data_set($this->wireValues, $param, null);
            return;
        }
        $list = collect(data_get($this->wireValues, $param, []))->reject(fn ($v, $k) => $k == $id)->values()->toArray() ?? [];
        data_set($this->wireValues, $param, $list);
    }

    function embedItAdd($param)
    {
        $list = collect(data_get($this->wireValues, $param, []))->push($this->embedArray($param))->values()->toArray();
        data_set($this->wireValues, $param, $list);
    }

    private function embedArray($param)
    {
        return [];
        //   $data = $data ?? $this->model::make();
        //  return (new $this->form)->data($data)->embedMode()->values();
    }

    private function printForm()
    {
        $this->printForm = collect([])->merge($this->recursivePrint())->toArray();
        return $this;
    }

    function htmlItem($item, $field = null)
    {
        return (string) $this->form()->itemHtml($item, $field);
    }

    private function haveError($i)
    {
        return (bool) (!data_get($i, FormConst::TYPE) || data_get($i, FormConst::ERROR, collect([]))->count());
    }

    private function recursivePrint($elements = null)
    {
        $elements = $elements ?? $this->form()->elements;

        $elements = $elements->map(function ($i) {
            $this->embedForm = null;
            switch (data_get($i, FormConst::OUTPUT)) {
                case Field::EMBEDS_MANY:
                    $wire = '.*';
                case Field::EMBEDS_ONE:
                    $wire = data_get($i, FormConst::EMBED_VARIABLE) . ($wire ?? null);
                    $form = data_get($i, FormConst::EMBED_FORM);
                    $model = data_get($i, FormConst::REL_MODEL);
                    $add = $this->generateEmbedItem($form, $model, $wire)->elements;
                    data_set($i, FormConst::EMBED_ELEMENTS, $add);
                    break;
            }
            return $i;
        });

        return $elements;
    }

    private function generateEmbedItem($form, $model, $wire)
    {
        $this->embedForm = $this->embedForm ?? (new $form($model))->embedMode($wire);
        return $this->embedForm;
    }
}
