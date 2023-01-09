<?php

namespace Nabre\Http\Livewire;

use Livewire\Component;
use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Support\Str;
use Nabre\Repositories\FormTwo\Field;
use Nabre\Repositories\FormTwo\Livewire\Put;
use Nabre\Repositories\FormTwo\Livewire\Table;

class FormManage extends Component
{
    use Put;
    use Table;

    #input
    var $idData;
    var $model;
    var $formClass;

    #page
    var $mode = null;
    var $title = null;

    #form
    var $printForm = [];
    var $wireValues = [];

    #table
    var $itemsTable = [];
    var $modelKey;

    private $form;
    private $embedForm;

    function mount()
    {
        if (is_null($this->idData) && is_null($this->mode)) {
            $this->modeTable();
        } else {
            $this->modePut($this->idData);
        }
    }

    function modePut($idData = null)
    {
        $this->mode = 'put';
        $this->idData = $idData;
        $this->formGenerate();
    }

    function modeTable()
    {
        $this->mode = 'table';
        $this->tableGenerate();
    }

    public function render()
    {
        return view('Nabre::livewire.form-manage.index');
    }


    private function form()
    {
        $this->form = $this->form ?? (new $this->formClass)->input($this->model::find($this->idData) ?? $this->model::make());
        return $this->form;
    }
}
