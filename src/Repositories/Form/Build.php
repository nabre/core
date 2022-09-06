<?php

namespace Nabre\Repositories\Form;

use Collective\Html\HtmlFacade as Html;
use Collective\Html\FormFacade as Form;
use Nabre\Services\CollectionService;

class Build
{
    use StructureNavigateTrait;

    var $data;
    var $method;
    var $back = true;
    var $card = true;
    var $submit = true;
    var $view = false;
    var $title;
    var $model;

    var $prefix = false;
    var $wireVar = false;
    var $embedsRemoveVar = false;

    static $create = 'POST';
    static $update = 'PUT';

    function title($title = null)
    {
        $this->title = $title ?? $this->title ?? $this->structure->title ?? null;
        return $this;
    }

    function boolParam($name, $bool = true)
    {
        if (in_array($name, ['back', 'card', 'view'])) {
            $this->$name = $bool;
        }

        return $this;
    }

    function html($route, $data = null)
    {
        $this->data($data);
        return $this->generate($route);
    }

    function embedHtml($data = null)
    {
        $this->data($data);
        return $this->generateEmbed();
    }

    function setWireCreate($var)
    {
        $this->wireVar = $var;
        return $this;
    }

    private function generateEmbed($embedMode = true)
    {
        $html = '';

        $this->elementsFilter()->each(function ($it,$itNum) use (&$html, $embedMode) {
            $this->item = $it;
            $this->first=!$itNum;

            $label = $this->label();


            if ($embedMode) {
                if ($this->wireVar) {
                    $this->item['set']['options']['wire:model.defer'] = $this->wireVar . "." . $this->item['variable'];
                }

                if ($this->prefix) {
                    $this->item['variable'] = $this->prefix . "." . $this->item['variable'];
                }

                if ($this->embedsRemoveVar) {
                    $this->item['variable'] = '';
                }
            }

            $content = Field::generate($this->item);
            $info = $this->submit ? $this->info() : null;

            switch ($this->item['output']) {
                case Field::HIDDEN:
                case Field::MSG:
                    $html .= $content . "\r\n";
                    break;
                default:
                    $html .= $this->item($content, $label, $info) . "\r\n";
                    break;
            }
        });

        return $html;
    }

    private function generate($url)
    {
        $this->title();
        $html = '';
        $class = 'container';
        $method = $this->method();
        $html .= Form::open(compact(['url', 'method', 'class'])) . "\r\n";
        $html .= $this->buttonBack() . "\r\n";

        $html .= $this->generateEmbed(false);

        $html .= $this->buttonSubmit() . "\r\n";
        $html .= Form::close();

        if ($this->card) {
            $titlebar = (is_null($this->title) ? null : '<div class="card-header">' . $this->title . '</div>');
            return '<div class="card">' . $titlebar . '<div class="card-body">' . $html . '</div></div>';
        }

        return $html;
    }

    private function printOutputTemplate()
    {
    }

    function elementsFilter()
    {
        return $this->elements()->filter(function ($i) {
            return in_array($this->method(), $i['set']['visible']);
        });
    }

    private function info()
    {
        //$it = $this->item;
        $html = '';

        if ($this->isRequired()) {
            $html .= '<div>*</div>';
        }

        collect($this->item['set']['info']??[])->each(function($i)use(&$html){
            $html.=Html::div($i['text'],['class'=>'alert m-0 p-1 alert-'.$i['theme']]);
        });
        return $html;
    }

    private function isRequired()
    {
        if (in_array('required', $this->requests())) {
            return true;
        }
        return false;
    }

    private function requests()
    {
        return (array)($this->item['set']['request'][$this->method()] ?? null);
    }

    private function label()
    {
        $it = $this->item;
        return Form::label($it['variable'], $it['label'] . ":", ["class" => "col-md-2 col-form-label"]);
    }

    private function item($content, $label = null, $info = null)
    {

        return '<div class="row mb-3 '.($this->first?'':'border-top').'">
                    ' . $label . '
                    <div class="col pt-1">' . $content . '</div>
                    <div class="col-md-3 pt-1">' . $info . '</div>
                </div>';
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

    function data($data = null)
    {
        $collection = $this->collection();
        if (!is_null($this->data) && is_null($data)) {
            $data = $this->data;
        } elseif (!is_null($collection) && !($data instanceof $collection)) {
            $data = new $collection;
        }

        $this->method($data);
        $this->data = $data;

        $this->structure->elements = $this->elements()->map(function ($it) {
            $this->item = $it;
            $this->defineOutput()
                ->defineLabel()
                ->setValue()
                ->setList();
            return $this->item;
        })->reject(function ($i) {
            return $i['type'] === false;
        })->values();

        $this->submit = (bool) $this->elements()->filter(function ($it) {
            return !in_array($it['output'], [Field::STATIC, Field::MSG]);
        })->count();

        return $this;
    }

    private function method($data = null)
    {
        if (!is_null($data)) {
            $key = $data->{$data->getKeyName()};
            $this->method = is_null($key) ? self::$create : self::$update;
        } else {
            $this->method = $this->method ?? self::$create;
        }

        return $this->method;
    }

    function isCreateMethod()
    {
        return $this->method() == self::$create;
    }

    function isUpdateMethod()
    {
        return $this->method() == self::$update;
    }

    private function setValue()
    {
        if ($this->item['type'] != 'fake') {
            $value = $this->data->readValue($this->item['variable']);

            if ($this->item['type'] == 'relation') {
                switch ($this->item['set']['rel']->type) {
                    case "EmbedsMany":
                    case "EmbedsOne":
                        $this->item['set']['embeds']['modelKey'] = $this->data->{$this->data->getKeyName()} ?? null;
                        $this->item['set']['embeds']['variable'] = $this->item['variable'];
                        break;
                }
            }

            $this->item['value'] = $value;
        }

        return $this;
    }

    private function setList()
    {
        if ($this->item['type'] == 'relation') {
            $model = new $this->item['set']['rel']->model;
            $label = $this->item['set']['list']['label'];
            $this->item['set']['list']['items'] = $this->item['set']['list']['items']->sortBy($label)->pluck($label, $model->getKeyName());
        }
    }


    private function defineOutput()
    {
        $type = $this->item['type'];
        $cast = $this->item['cast'] ?? null;
        $set = $this->item['set'];
        $view = $this->view;
        $request = $this->requests();

        Define::outputType($this->item['output'], $type, $cast, $set, $request, $view);

        return $this;
    }

    private function defineLabel()
    {
        $variable = $this->item['variable'];
        $class = $this->structure->collection;
        //  dd(get_defined_vars());
        $queryStr = !is_null($class) ? CollectionService::getString(new $class, $variable) : null; #carica label dal DB
        $this->item['label'] = $this->item['label'] ?? $queryStr ?? $variable;
        return $this;
    }
}
