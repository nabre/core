<?php

namespace Nabre\Repositories\FormTwo\FormTrait;

use Nabre\Repositories\FormTwo\Field;
use Nabre\Repositories\FormTwo\QueryElements;
use Nabre\Repositories\FormTwo\Rule;

trait Structure
{
    private $elements = null;
    private $item;

    function build()
    {
    }

    function label($label = null, $overwrite = false)
    {
        $this->push(compact(__FUNCTION__), $overwrite);
        return $this;
    }

    function listLabel($label = null, $overwrite = false)
    {
        $this->push(['set.list.label' => $label ?? 'id'], $overwrite);
        return $this;
    }

    function listSort(bool $asc = true, $overwrite = false)
    {
        $this->push(['set.list.sort' => $asc], $overwrite);
        return $this;
    }

    function embed($embed = null, $overwrite = false)
    {
        $this->push(compact(__FUNCTION__), $overwrite);
        return $this;
    }

    function add($variable, $output = null)
    {
        $this->insert()->push(get_defined_vars());
        return $this;
    }

    function addHtml($html)
    {
        $this->add(null, Field::HTML)->push(['value' => get_defined_vars()], true)->fake();
        return $this;
    }

    function addMsg($text, $theme = 'secondary')
    {
        $this->add(null, Field::MSG)->push(['value' => get_defined_vars()], true)->fake();
        return $this;
    }

    function info($text = null, $theme = 'secondary')
    {
        if (!is_null($text)) {
            $array = $this->getItemData('set.info', collect([]))->push(get_defined_vars());
            $this->setItemData('set.info', $array, true);
        }

        return $this;
    }

    function fake()
    {
        $this->push(['type' => 'fake'], true);
        return $this;
    }

    function value($value){
        $this->push(get_defined_vars(), true);
        return $this;
    }

    private function push(array $array, $overwrite = false)
    {
        $this->item = (array)$this->item;
        collect($array)->each(function ($value, $key) use ($overwrite) {
            $this->setItemData($key, $value, $overwrite);
        });
        return $this;
    }

    private function setItemData($key, $value, $overwrite = false)
    {
        $this->setData($this->item, $key, $value, $overwrite);

        return $this;
    }

    private function setData(&$target, $key, $value, $overwrite = false)
    {
        $var = collect(explode('.', $key))->reverse()->take(1)->implode('.');
        $find = collect(explode('.', $key))->reverse()->skip(1)->reverse()->implode('.');
        if (empty($find)) {
            $find = $key;
            $set = $value;
        } else {
            $set = (array)$this->getItemData($find);
            data_set($set, $var, $value, $overwrite);
            $overwrite=true;
        }

        return data_set($target, $find, $set, $overwrite);
    }

    private function getItemData($key, $default = null)
    {
        $this->item = (array)$this->item;
        return data_get($this->item, $key, $default);
    }

    private function structure()
    {
        if (!is_null($this->elements)) {
            return $this;
        }

        $this->methodForm();

        $this->elements = collect([]);
        $this->build();
        $this->insert();

        $this->errors();
        $this->checkSubmitAviable();

        return $this;
    }

    private function rulesMessages()
    {
        collect([self::$update, self::$create])->each(function ($method) {
            if (is_null($this->getItemData('set.request.' . $method) ?? null)) {
                $this->rule(Rule::nullable(), $method);
            }
        });

        $rules = collect($this->requests())
            ->map(fn ($fn) => (new Rule)->parseRule($fn, "\"" . $this->getItemData('label') . "\""));

        $this->setItemData('set.rules.fn', $rules->pluck('fn')->unique()->values()->toArray(), true);

        $rulesOut = collect([]);

        $rules->reject(fn ($i) => in_array(data_get($i, 'fn'), Rule::allSubRule()))->each(function ($i) use ($rules, &$rulesOut) {
            $fn = data_get($i, 'fn');
            if (in_array($fn, Rule::combinedRule())) {
                $rules->whereIn('fn', Rule::subRule($fn))->each(function ($s) use ($i, &$rulesOut) {
                    $fn = data_get($i, 'fn') . "." . data_get($s, 'fn');
                    data_set($i, 'fn', $fn, true);
                    $params = array_unique(array_merge(data_get($i, 'params'), data_get($s, 'params')));
                    data_set($i, 'params', $params, true);
                    $rulesOut = $rulesOut->push($i);
                });
            } else {
                $rulesOut = $rulesOut->push($i);
            }
        });

        $rulesOut->sortBy(function ($i) {
            $fn = data_get($i, 'fn');
            return ($fn == 'required') ? 0 : 1;
        })->values()->each(function ($i) {
            $fn = data_get($i, 'fn');
            $msg = trim(__('Nabre::validation.' . $fn, data_get($i, 'params')));

            switch ($fn) {
                case "required":
                    $this->info('<i class="fa-solid fa-asterisk" title="' . htmlspecialchars($msg) . '"></i>', 'danger');
                    break;
                case "nullable":
                    break;
                default:
                    if (!empty($msg)) {
                        $this->info($msg, 'secondary');
                    }
                    break;
            }
        });

        return $this;
    }

    private function insert()
    {
        if (!is_null($this->item ?? null)) {
            $this->variableCheck();
            $this->rulesMessages();

            $this->output();
            $this->query();
            $this->labelDefine();


            $this->elements = $this->elements->push($this->item);
            $this->item = null;
        }

        return $this;
    }

    private function query()
    {
        if ($this->getItemData('type') != 'relation') {
            return $this;
        }

        $string = collect(explode(".", $this->getItemData('variable')))->map(function ($part) {
            $part = ucfirst($part);
            return get_defined_vars();
        })->implode('part', '');
        $fn = 'query' . $string;

        if (method_exists($this, $fn)) {
            $items = $this->$fn();
        } else {
            $model = $this->queryGetModel();
            $items = $model->get();
        }

        $this->listLabel();
        $this->listSort();

        $label = $this->getItemData('set.list.label');

        $fnSort = 'sortBy';
        if (!$this->getItemData('set.list.sort')) {
            $fnSort .= 'Desc';
        }
        $items = $items->pluck($label, $model->getKeyName())->$fnSort($label);
        $this->setItemData('set.list.items', $items);

        return $this;
    }

    function queryGetModel()
    {
        $model = $this->getItemData('set.rel.model');
        return new $model;
    }

    #Controllo della variabile
    private function variableCheck()
    {
        $model = new $this->model;
        $variable = $this->getItemData('variable');
        $type = $this->getItemData('type', true);
        $str =
            $cast =
            $setrel = null;

        $newVariable = [];

        if ($type === true) {
            collect(explode(".", $variable))->each(function ($v) use (&$model, &$type, &$newVariable, &$str, &$cast, &$setrel) {
                if ($type === true || $type == 'relation') {
                    $newVariable[] = $str = $v;

                    if (!is_null($model ?? null)) {
                        $rel = null;
                        if ($this->isRelation($v, $model, $rel)) {
                            $type = 'relation';
                            $setrel = $rel;
                        } else
                    if ($this->isAttribute($v, $model)) {
                            $type = 'attribute';
                        } else
                    if ($this->isFillable($v, $model)) {
                            $type = 'fillable';
                            $cast = $model->getCasts()[$v] ?? null;
                        } else {
                            $type = false;
                        }
                    }
                }
            });

            $newVariable = implode(".", (array) $newVariable);
            if ($newVariable != $variable) {
                $type = false;
            }
            ${'set.rel'} = $setrel;
        }
        unset($newVariable);
        unset($model);
        unset($setrel);

        $this->push(get_defined_vars(), true);
    }

    private function isFillable($v, $model)
    {
        if (is_string($model)) {
            $model = new $model;
        }

        return in_array($v, $model->getFillable());
    }

    private function isAttribute($v, $model)
    {
        return method_exists($model, 'get' . str_replace("_", "", $v) . 'attribute');
    }

    private function isRelation($v, &$model, &$rel)
    {
        $rel = $model->reletionshipFind($v);

        #Conrollo bolonstTo,HasOne

        $bool = (bool) !is_null($rel);
        $model = $bool ? new $rel->model : $model;
        return $bool;
    }

    #label
    private function labelDefine()
    {
        $label = $this->getItemData('variable');
        $this->label($label);

        return $this;
    }

    private function checkSubmitAviable()
    {
        if (!(new QueryElements($this->elements))->removeInexistents()->excludeWithErrors()->results()->count()) {
            $this->submit = false;
            $this->submitError = true;
        }
        return $this;
    }
}
