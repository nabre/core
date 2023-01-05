<?php

namespace Nabre\Repositories\FormTwo;

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
        }

        return data_set($target, $find, $set, true);
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

    private function rulesMessages(){
        collect([self::$update, self::$create])->each(function ($method) {
            if (is_null($this->getItemData('set.request.' . $method) ?? null)) {
                $this->request('nullable', $method);
            }
        });

        collect($this->requests())->sortBy(function($i){
            return ($i=='required')?0:1;
        })->values()->each(function ($i) {
            switch ($i) {
                case "required":
                    $this->info('<i class="fa-solid fa-asterisk" title="'. __('validation.required', ['attribute' => '"' . $this->getItemData('label') . '"']).'"></i>', 'danger');
                    break;
                case "nullable":
                    break;
                default:
                    $this->info($i, 'secondary');
                    break;
            }
        });

        return $this;
    }

    private function insert()
    {
        if (!is_null($this->item ?? null)) {
            $this->variableCheck();
            $this->output();
            $this->query();
            $this->labelDefine();
            $this->rulesMessages();

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

    #Output
    //static $requestOutput = ['email' => Field::TEXT];

    private function output()
    {
        $output = $this->getItemData('output');
        $type = $this->getItemData('type');
        $enabled = collect([]);

        if ($type != 'fake') {
            switch ($type) {
                case false:
                    break;
                case "fillable":
                    switch ($this->getItemData('cast')) {
                        case PasswordCast::class:
                            $enabled = $enabled->push(Field::PASSWORD);
                            break;
                        case LocalCast::class:
                            $enabled = $enabled->push(Field::TEXT_LANG);
                            break;
                        case SettingTypeCast::class:
                            $enabled = $enabled->push(Field::FIELD_TYPE_LIST);
                            break;
                        case "boolean":
                            $enabled = $enabled->push(Field::BOOLEAN);
                            break;
                        case CkeditorCast::class:
                            $enabled = $enabled->push(Field::TEXTAREA_CKEDITOR);
                            break;
                        default:
                            $enabled = $enabled->merge([Field::TEXT, Field::TEXTAREA, Field::TEXTAREA_CKEDITOR, Field::HIDDEN]);
                            /*collect(self::$requestOutput)->each(function ($en) use (&$enabled) {
                            $enabled = array_unique(array_merge((array)$en, (array)$enabled));
                        });*/
                            break;
                    }
                    break;
                case "attribute":
                    $enabled = $enabled->push(Field::STATIC);
                    break;
                case "relation":
                    switch ($this->getItemData('set.rel.type')) {
                        case "BelongsTo":
                        case "HasOne":
                            $enabled = $enabled->push(Field::SELECT);
                            break;
                        case "BelongsToMany":
                        case "HasMany":
                            $enabled = $enabled->merge([Field::CHECKBOX, Field::SELECT]);
                            break;
                        case "EmbedsMany":
                            $enabled = $enabled->push(Field::EMBEDS_MANY);
                            break;
                        case "EmbedsOne":
                            $enabled = $enabled->push(Field::EMBEDS_ONE);
                            break;
                    }
                    break;
            }

            $enabled = $enabled->push(Field::STATIC)->push(Field::HIDDEN)->unique()->values();

            if (!$enabled->filter(fn ($str) => $str == $output)->count() && $enabled->count()) {
                $output = $enabled->first();
            }
        }

        $this->setItemData('output', $output ?? Field::STATIC, true);
    }

    private function errors()
    {
        $mode = strtolower(env('APP_ENV', 'production'));

        switch ($mode) {
            case "local":
                break;
            default:
                $this->elements = (new QueryElements($this->elements))->removeInexistents()->results();
                break;
        }

        $this->elements = $this->elements->map(function ($i) {
            $errors = collect([]);
            $type = data_get($i, 'type', false);

            if (!$type) {
                $errors = $errors->push('Variabile non esistente.');
            } else {
                $output = data_get($i, 'output', false);
                $array = Field::fieldsListRequired();
                if (count($array) && in_array($output, $array)) {
                    $list = data_get($i, 'set.list.items', false);
                    if (!$list) {
                        $errors = $errors->push('Lista items non definita.');
                    }

                    $label = data_get($i, 'set.list.label', false);
                    if (!$label) {
                        $errors = $errors->push('Campo etichetta lista non definito.');
                    }

                    $model = data_get($i, 'set.rel.model');
                    if (!($this->isAttribute($label, $model) || $this->isFillable($label, $model))) {
                        $errors = $errors->push('Campo etichetta non valido.');
                    }
                }
            }

            if ($errors->count()) {
                $this->setData($i, 'errors', $errors);
            }
            return $i;
        })->values();
    }

    private function checkSubmitAviable()
    {
        if ((new QueryElements($this->elements))->removeInexistents()->withErrors()->results()->count()) {
            $this->submit = false;
            $this->submitError = true;
        }
        return $this;
    }
}
