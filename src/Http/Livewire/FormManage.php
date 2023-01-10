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
    var string $model;
    var string $formClass;
    var bool $modal;

    #page
    var $mode = null;
    var $title = null;
    var string $emptyValue = '---';

    #form
    var array $printForm = [];
    var array $wireValues = [];

    #table
    var array $cols = [];
    var array $itemsTable = [];
    var $modelKey = null;

    private $form;
    private $embedForm;

    function mount()
    {
       /* $this->modePut($this->idData);
        return;*/

        if (is_null($this->idData) && is_null($this->mode) || $this->modal) {
            $this->modeTable();
        } else {
            $this->modePut($this->idData);
        }
    }

    function modePut(?string $idData = null)
    {
        $this->mode = 'put';
        $this->modeModelPut($idData);
    }

    function modeModelPut(?string $idData = null){
        $this->idData = $idData;
        $this->formGenerate($idData);
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
