<?php

namespace Nabre\Repositories\FormTwo;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;

trait Render
{
    private $back = true;
    private $submit = true;
    private $card = false;

    private function render($url)
    {
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

    private function fieldItem($i, bool $first)
    {
        $field = Field::generate($i);
        switch (data_get($i, 'output')) {
            case Field::HIDDEN:
            case Field::MSG:
            case Field::HTML:
                return $field . "\r\n";
                break;
            default:
                $info = 'testo';
                return '<div class="row mb-3 ' . ($first ? '' : 'border-top') . '">
                ' . data_get($i, 'label') . '
                <div class="col pt-1">' . $field . '</div>
                <div class="col-md-3 pt-1">' . $info . '</div>
                </div>'."\r\n";
                break;
        }
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
        if (!$this->submit) {
            return;
        }
        return '<hr>' . Html::btn('<i class="fa-regular fa-floppy-disk"></i>', ['class' => 'btn btn-info', 'type' => 'submit']);
    }
}
