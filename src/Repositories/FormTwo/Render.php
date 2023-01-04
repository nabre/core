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

    private function render($url)
    {
        $this->checkUrl($url);
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

        return $html;
    }

    private function fieldsOut()
    {
        return $this->elements->map(fn ($i, $n) => $this->fieldItem($i, (bool)!$n))->implode('');
    }

    private function fieldItem($i)
    {
        $this->item = $i;
        if (optional($this->getItemData('errors'))->count()) {
            return $this->itemHtml();
        }

        switch ($this->getItemData('output')) {
            case Field::HIDDEN:
            case Field::MSG:
            case Field::HTML:
                $this->firstItem = true;
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
        $html = '';

        if ($this->isRequired()) {
            $html .= Html::div('<i class="fa-solid fa-asterisk"></i>', ['class' => 'badge bg-danger', 'title' => __('validation.required', ['attribute' => '"' . $this->getItemData('label') . '"'])]);
        }

        $html .= $this->getItemData('set.info', collect([]))->map(fn ($i) => (string) Html::div(data_get($i, 'text'), ['class' => 'alert m-0 p-1 alert-' . data_get($i, 'theme')]))->implode('');
        return $html;
    }

    private function checkUrl(&$url)
    {
        if (is_null($url)) {
            $this->submit = false;
        }
        return $this;
    }

    private function buttonBack()
    {
        if (!$this->back) {
            return;
        }
        return Html::a('<i class="fa-solid fa-angles-left"></i>', ['class' => 'btn btn-secondary', 'href' => 'javascript:history.back()']) . '<hr>';
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
