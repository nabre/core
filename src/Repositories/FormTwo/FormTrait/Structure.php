<?php

namespace Nabre\Repositories\FormTwo\FormTrait;

use Nabre\Repositories\FormTwo\Field;
use Nabre\Repositories\FormTwo\FormConst;
use Nabre\Repositories\FormTwo\QueryElements;
use Nabre\Repositories\FormTwo\Rule;

trait Structure
{
    var $elements = null;
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
        $this->push([FormConst::string('LIST_LABEL') => $label ?? $this->collection->getKeyName()], $overwrite);
        return $this;
    }

    function listEmpty($name = null, $overwrite = false)
    {
        if (is_null($name)) {
            $name = FormConst::labelSelect();
        }
        $this->push([FormConst::string('LIST_EMPTY') => $name], $overwrite);
        return $this;
    }

    function listSort(bool $asc = true, $overwrite = false)
    {
        $this->push([FormConst::string('LIST_SORT') => $asc], $overwrite);
        return $this;
    }

    function embed($embed = null, $overwrite = false)
    {
        $this->push([FormConst::string('EMBED_FORM') => $embed], $overwrite);
        return $this;
    }

    function add($variable, $output = null)
    {
        $this->insert()->push(get_defined_vars());
        return $this;
    }

    function addHtml($html)
    {
        $this->add(null, Field::HTML)->push([FormConst::string('VALUE') => get_defined_vars()], true)->fake();
        return $this;
    }

    function addMsg($text, $theme = 'secondary')
    {
        $this->add(null, Field::MSG)->push([FormConst::string('VALUE') => get_defined_vars()], true)->fake();
        return $this;
    }

    function info($text = null, $theme = 'secondary')
    {
        if (!is_null($text)) {
            $array = $this->getItemData(FormConst::INFO, collect([]))->push(get_defined_vars());
            $this->setItemData(FormConst::INFO, $array, true);
        }

        return $this;
    }

    function fake()
    {
        $this->push([FormConst::string('TYPE') => 'fake'], true);
        return $this;
    }

    function value($value)
    {
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
        $key = is_array($key) ? $key : explode('.', $key);
        $var = collect($key)->reverse()->take(1)->implode('.');
        $find = collect($key)->reverse()->skip(1)->reverse()->implode('.');
        if (empty($find)) {
            $find = $key;
            $set = $value;
        } else {
            $set = (array)data_get($target, $find, []);
            data_set($set, $var, $value, $overwrite);
            $overwrite = true;
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

        $this->checkErrors();

        return $this;
    }

    private function rulesMessages()
    {
        collect([self::$update, self::$create])->each(function ($method) {
            if (is_null($this->getItemData(FormConst::request($method)) ?? null)) {
                $this->rule(Rule::nullable(), $method);
            }
        });

        $rules = collect($this->requests())
            ->map(fn ($fn) => (new Rule)->parseRule($fn, "\"" . $this->getItemData('label') . "\""));

        $this->setItemData(FormConst::RULES_FN, $rules->pluck('fn')->unique()->values()->toArray(), true);

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
            return ($fn == Rule::required()) ? 0 : 1;
        })->values()->each(function ($i) {
            $fn = data_get($i, 'fn');
            $msg = trim(__('Nabre::validation.' . $fn, data_get($i, 'params')));

            switch ($fn) {
                case Rule::required():
                    $this->info('<i class="fa-solid fa-asterisk" title="' . htmlspecialchars($msg) . '"></i>', 'danger');
                    break;
                case Rule::nullable():
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
            if (!$this->checkDubble()) {
                $this->variableCheck();
                $this->rulesMessages();
                $this->output();
                $this->query();
                $this->labelDefine();

                $wire = implode(".", array_filter(['wireValues', $this->wire, data_get($this->item, FormConst::VARIABLE)]));
                $this->setItemData(FormConst::OPTIONS_WIREMODEL, $wire, true);

                $this->setItemData(FormConst::INFO, $this->getItemData(FormConst::INFO, collect([]))->toArray());

                $this->elements = $this->elements->push($this->item);
            }
            $this->item = null;
        }

        return $this;
    }

    private function checkDubble()
    {
        return in_array($this->getItemData(FormConst::VARIABLE), $this->elements->pluck(FormConst::VARIABLE)->toArray());
    }

    private function query()
    {
        if ($this->getItemData(FormConst::TYPE) != 'relation') {
            return $this;
        }

        $string = collect(explode(".", $this->getItemData(FormConst::VARIABLE)))->map(function ($part) {
            return ucfirst($part);
        })->implode('');
        $fn = 'query' . $string;

        if (method_exists($this, $fn)) {
            $items = $this->$fn();
        } else {
            $model = $this->queryGetModel();
            $items = $model->get();
        }

        $this->listLabel();
        $this->listSort();

        $label = $this->getItemData(FormConst::LIST_LABEL);

        $fnSort = 'sortBy';
        if (!$this->getItemData(FormConst::LIST_SORT)) {
            $fnSort .= 'Desc';
        }
        $items = $items->pluck($label, $model->getKeyName())->$fnSort($label);
        $empty = $this->getItemData(FormConst::LIST_EMPTY);
        if (!is_null($empty)) {
            $items = $items->prepend($empty, '');
        }
        $items = $items->toArray();
        $this->setItemData(FormConst::LIST_ITEMS, $items);

        return $this;
    }

    function queryGetModel()
    {
        $model = $this->getItemData(FormConst::REL_MODEL);
        return new $model;
    }

    #Controllo della variabile
    private function variableCheck()
    {
        $model = new $this->model;
        $variable = $this->getItemData(FormConst::VARIABLE);
        $type = $this->getItemData(FormConst::TYPE, true);
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

        #Correzione automatica
        $type = $this->getItemData(FormConst::REL_TYPE);
        $request = $this->getItemData(FormConst::request($this->method), []);
        switch ($type) {
            case "HasMany":
            case "BelongsToMany":
                $this->setItemData(FormConst::LIST_EMPTY, null, true);
                break;
            case "BelongsTo":
            case "HasOne":
                if (in_array(Rule::required(), $request)) {
                    $this->setItemData(FormConst::LIST_EMPTY, null, true);
                } else {
                    $this->listEmpty();
                }
                break;
            case "EmbedsOne":
            case "EmbedsMany":
                $request = array_values(array_intersect($request, [Rule::nullable(), Rule::required()]));
                data_set($i, FormConst::request($this->method), $request);
                break;
        }
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
        $rel = $model->relationshipFind($v);

        #Conrollo bolonstTo,HasOne

        $bool = (bool) !is_null($rel);
        $model = $bool ? new $rel->model : $model;
        return $bool;
    }

    #label
    private function labelDefine()
    {
        $label = $this->getItemData(FormConst::VARIABLE);
        $this->label($label);

        return $this;
    }
}
