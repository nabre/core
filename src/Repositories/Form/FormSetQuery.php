<?php

namespace Nabre\Repositories\Form;

trait FormSetQuery
{

    static $create = 'POST';
    static $update = 'PUT';

    private function defaultSettings()
    {
        switch ($this->item['type']) {
            case "relation":
                $this->listLabel()
                    ->listSort()
                    ->query();
                break;
        }

        $this->itemVisible();

        collect([self::$update, self::$create])->each(function ($method) {
            if (is_null($this->item['set']['request'][$method] ?? null)) {
                $this->request('nullable', $method);
            }
        });
    }

    protected function query()
    {
        if ($this->item['type'] != 'relation') {
            return $this;
        }

        $string = collect(explode(".", $this->item['variable']))->map(function ($part) {
            $part = ucfirst($part);
            return get_defined_vars();
        })->implode('part', '');
        $fn = 'query' . $string;

        if (!is_null($this->item['set']['rel'] ?? null)) {
            $this->model = new $this->item['set']['rel']->model;
        }

        if (method_exists($this, $fn)) {
            $this->item['set']['list']['items'] = $this->$fn();
        } elseif (!is_null($this->model ?? null)) {
            $this->item['set']['list']['items'] = $this->model->get();
        }

        if (!is_null($this->collection ?? null)) {
            $this->model = new $this->collection;
        }

        return $this;
    }

    function embedsForm($class, $text = null,$method=[])
    {
        $this->item['set']['embeds']['form'] = $class;
        $this->item['set']['embeds']['method'] = $method;
        $this->item['set']['embeds']['model'] = $this->collection;

        $this->itemVisibleUpdate($text);
    }

    function listLabel($label = null)
    {
        $this->item['set']['list']['label'] = $this->item['set']['list']['label'] ?? $label ?? optional($this->model)->getKeyName();
        return $this;
    }

    function listSort($sort = 'asc')
    {
        $this->item['set']['list']['sort'] = $this->item['set']['list']['sort'] ?? $sort;
        return $this;
    }

    function info($text=null,$theme='secondary'){
        if(!is_null($text)){
            $this->item['set']['info'][] = get_defined_vars();
        }

        return $this;
    }

    function listEmpty($empty = null)
    {
        $this->item['set']['list']['empty'] = $this->item['set']['list']['empty'] ?? $empty;
        return $this;
    }

    function disabled($array = null)
    {
        $this->item['set']['list']['disabled'] = (array)$array;
    }

    function request($string, $method = null)
    {
        $this->checkMethods($method);
        $string = explode("|", implode("|", (array)$string));
        collect($method)->each(function ($method) use ($string) {
            $this->item['set']['request'][$method] = collect((array)($this->item['set']['request'][$method] ?? null))->merge($string)->unique()->filter()->values()->toArray();
        });
        return $this;
    }

    function requestCreate($string)
    {
        $this->request($string, self::$create);
        return $this;
    }

    function requestUpdate($string)
    {
        $this->request($string, self::$update);
        return $this;
    }

    function requestRequired($method = null)
    {
        $this->request("required", $method);
        return $this;
    }

    function psw_confirm()
    {
        $this->requestRequired();
        $this->request('confirmed');
        $variable = $this->item['variable'] . "_confirmed";
        $output = $this->item['output'] = Field::PASSWORD;
        $label = "Conferma password";
        $this->insert()->item($variable, $output, $label, 'fake');
    }

    function itemVisible($method = null)
    {
        $this->checkMethods($method);
        $this->item['set']['visible'] = $this->item['set']['visible'] ?? $method;
        return $this;
    }

    function itemVisibleCreate($text = null)
    {
        $this->itemVisible(self::$create);
        $this->addMsg($text, 'secondary',  self::$update);
        return $this;
    }

    function itemVisibleUpdate($text = null)
    {
        $this->itemVisible(self::$update);
        $this->addMsg($text, 'secondary', self::$create);
        return $this;
    }

    function addMsg($text = null, $theme = 'secondary', $method = null)
    {
        if (!is_null($text)) {
            $this->add('', Field::MSG,  null, 'fake')->itemVisible($method);
            $this->setFakeValue(compact('text', 'theme'));
        }
        return $this;
    }

    function addHtml($html=null,$method=null){
        if (!is_null($html)) {
            $this->add('', Field::HTML,  null, 'fake')->itemVisible($method);
            $this->setFakeValue(compact('html'));
        }
        return $this;
    }

    function setFakeValue($value = null)
    {
        if ($this->item['type'] == 'fake') {
            $this->item['value'] = $value;
        }
        return $this;
    }

    protected function checkMethods(&$method)
    {
        if (is_null($method)) {
            $method = [self::$update, self::$create];
        }
        $method = array_intersect([self::$update, self::$create], (array) $method);
    }
}
