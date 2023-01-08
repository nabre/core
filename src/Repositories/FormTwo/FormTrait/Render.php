<?php

namespace Nabre\Repositories\FormTwo\FormTrait;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Repositories\FormTwo\Field;

trait Render
{
    private $back = true;
    private $submit = true;
    private $submitError = false;
    private $card = false;
    private $form = true;

    private function submitUrl()
    {
        if ($this->method == self::$create) {
            $find = 'store';
        } else {
            $find = 'update';
        }

        $url = $this->redirect[$find] ?? null;

        if (is_null($url)) {
            $this->submit = false;
        }
        return $url;
    }

    private function render()
    {
        return $this->elements->map(fn ($i) => $this->fieldItem($i))->implode('');
    }

    function fieldItem($i)
    {
        $this->item = $i;
        if (optional($this->getItemData('errors'))->count()) {
            return $this->itemHtml();
        }

        switch ($this->getItemData('output')) {
            case Field::MSG:
            case Field::HTML:
                $this->firstItem = true;
            case Field::HIDDEN:
                return $this->fieldGenerate() . "\r\n";
                break;
            default:
                return $this->itemHtml();
                break;
        }
    }

    protected $firstItem = true;

    function itemHtml($item = null, $field = null)
    {
        $this->item = $item ?? $this->item;
        $info = $this->infoField();
        $field = $field ?? $this->fieldGenerate();
        $first = $this->firstItem;
        if ($this->firstItem) {
            $this->firstItem = false;
        }

        $html = Html::div($this->getItemData('label') . ":", ['class' => 'col-md-1 pt-1']);
        $html .= Html::div($field, ['class' => 'col pt-1']);
        $html .= Html::div($info, ['class' => 'col-md-3 pt-1']);

        return (string) Html::div($html, ['class' => 'row mb-3 ' . ($first ? '' : 'border-top')]);
    }

    private function fieldGenerate()
    {
        $wire = implode(".", array_filter(['wireValues', $this->wire, $this->getItemData( 'variable')]));
        $this->item['set']['options']['wire:model.defer']=$wire;
        //$this->setItemData('set.options.wire:model.prevent', $wire, true);
        return (string) Field::generate($this->item);
    }

    private function infoField()
    {
        if ($this->haveError()) {
            return;
        }
        return $this->getItemData('set.info', collect([]))->map(fn ($i) => (string) Html::div(data_get($i, 'text'), ['class' => 'badge text-bg-' . data_get($i, 'theme')]))->implode('<br>');
    }

    private function haveError()
    {
        return (bool) (!$this->getItemData('type') || $this->getItemData('errors', collect([]))->count());
    }

    function buttonBack()
    {
        $url = $this->redirect['index'] ?? null;
        if (is_null($url)) {
            $this->back = false;
        }

        if (!$this->back) {
            return;
        }
        return Html::a('<i class="fa-solid fa-angles-left"></i>', ['class' => 'btn btn-secondary', 'href' => $url]) . '<hr>';
    }

    function buttonSubmit()
    {
        if ($this->submitError) {
            return '<hr>' . Html::div('Il form non può essere inviato a causa di un errore di elaboraizone dei campi. Contattare l\'amministratore.', ['class' => "alert alert-danger"]);
        }

        if (!$this->submit) {
            return;
        }
        return '<hr>' . Html::btn('<i class="fa-regular fa-floppy-disk"></i>', ['class' => 'btn btn-info', 'type' => 'submit']);
    }
}
