<?php

namespace Nabre\Repositories\FormTwo;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;

trait Render
{
    private $back = true;
    private $submit = true;
    private $submitError = false;
    private $card = false;

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
        $url = $this->submitUrl();
        $html = '';
        $class = 'container';
        $method = $this->method;
        //  $html.=var_export(request());
        $html .= Form::open(compact(['url', 'method', 'class'])) . "\r\n";
        $html .= $this->buttonBack() . "\r\n";

        $html .= $this->fieldsOut();

        $html .= $this->buttonSubmit() . "\r\n";
        $html .= Form::close();

        if ($this->card) {
            $title = (is_null($this->title ?? null) ? null : Html::div($this->title, ['class' => 'card-header']));
            $body = Html::div($html, ['class' => 'card-body']);
            return Html::div($title . $body, ['class' => 'card']);
        }
        session()->forget('errors');
        return $html;
    }

    private function fieldsOut()
    {
        return $this->elements->map(fn ($i) => $this->fieldItem($i))->implode('');
    }

    private function fieldItem($i)
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

    private function itemHtml()
    {
        $info = $this->infoField();
        $field = $this->fieldGenerate();
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
        return Field::generate($this->item);
    }

    private function infoField()
    {
        if ($this->haveError()) {
            return;
        }
        return $this->getItemData('set.info', collect([]))->map(fn ($i) => (string) Html::div(data_get($i, 'text'), ['class' => 'badge text-bg-' . data_get($i, 'theme')]))->implode('<br>');
    }

    private function haveError(){
        return (bool) (!$this->getItemData('type') || $this->getItemData('errors',collect([]))->count());
    }

    private function buttonBack()
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

    private function buttonSubmit()
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
