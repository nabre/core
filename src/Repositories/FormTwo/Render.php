<?php

namespace Nabre\Repositories\FormTwo;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Illuminate\Http\Request;

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
        $html .= Form::open(compact(['url', 'method', 'class'])) . "\r\n";
        $html .= $this->buttonBack() . "\r\n";

        $html .= $this->fieldsOut();

        $html .= $this->buttonSubmit() . "\r\n";
        $html .= Form::close();

        if ($this->card) {
            $titlebar = (is_null($this->title ?? null) ? null : '<div class="card-header">' . $this->title . '</div>');
            return '<div class="card">' . $titlebar . '<div class="card-body">' . $html . '</div></div>';
        }

        return $html;
    }

    private function fieldsOut()
    {
        return $this->elements->map(fn ($i, $n) => $this->fieldItem($i, (bool)!$n))->implode('');
    }

    private function fieldItem($i)
    {
        if (optional(data_get($i, 'errors'))->count()) {
            return $this->itemHtml($i);
        }

        switch (data_get($i, 'output')) {
            case Field::HIDDEN:
            case Field::MSG:
            case Field::HTML:
                $this->firstItem = true;
                return $this->fieldGenerate($i) . "\r\n";
                break;
            default:
                return $this->itemHtml($i);
                break;
        }
    }

    protected $firstItem = true;

    private function itemHtml($i)
    {
        $info = $this->info($i);
        $field = $this->fieldGenerate($i);
        $first = $this->firstItem;
        if ($this->firstItem) {
            $this->firstItem = false;
        }
        return '<div class="row mb-3 ' . ($first ? '' : 'border-top') . '">
        <div class="col-md-1 pt-1">' . data_get($i, 'label') . '</div>
        <div class="col pt-1">' . $field . '</div>
        <div class="col-md-3 pt-1">' . $info . '</div>
        </div>' . "\r\n";
    }

    private function fieldGenerate($i)
    {
        return Field::generate($i);
    }

    private function info($i)
    {
        return 'info_text';
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
        return '<a href="javascript:history.back()" class="btn btn-secondary"><i class="fa-solid fa-angles-left"></i></a><hr>';
    }

    private function buttonSubmit()
    {
        if ($this->submitError) {
            return '<hr><div class="alert alert-danger">Il form non può essere inviato a causa di un errore di elaboraizone dei campi. Contattare l\'amministratore.</div>';
        }

        if (!$this->submit) {
            return;
        }
        return '<hr>' . Html::btn('<i class="fa-regular fa-floppy-disk"></i>', ['class' => 'btn btn-info', 'type' => 'submit']);
    }
}
