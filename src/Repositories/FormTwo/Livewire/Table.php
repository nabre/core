<?php

namespace Nabre\Repositories\FormTwo\Livewire;

use Collective\Html\HtmlFacade as Html;
use Nabre\Repositories\FormTwo\Field;

trait Table
{
    private function tableGenerate()
    {
        $this->modelKey = (new $this->model)->getKeyName();
        $this->cols=$this->form()->elements->toArray();
        $this->itemsTable = $this->query()->map(function ($data) {
            $item = (new $this->formClass)->input($data)->values();
            data_set($item, $this->modelKey, data_get($data, $this->modelKey));
            return $item;
        })->toArray();

        $this->title='Elenco';
    }

    private function query()
    {
        return $this->model::get();
    }
}
