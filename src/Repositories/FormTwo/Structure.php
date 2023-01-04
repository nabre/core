<?php

namespace Nabre\Repositories\FormTwo;

trait Structure
{
    private $elements = null;
    private $method = null;
    private $item;

    static $create = 'post';
    static $update = 'put';

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

    function embed($embed = null, $overwrite = false){
        $this->push(compact(__FUNCTION__), $overwrite);
        return $this;
    }

    function add($variable, $output = null)
    {
        $this->insert()->push(get_defined_vars());
        return $this;
    }

    private function push(array $array, $overwrite = false)
    {
        $this->item = (array)$this->item;
        collect($array)->each(function ($value, $key) use ($overwrite) {
            $this->setItemData($key, $value, $overwrite);
        });
    }

    private function setItemData($key, $value, $overwrite = false)
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

        data_set($this->item, $find, $set, true);

        return $this;
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

        $this->elements = collect([]);
        $this->build();
        $this->insert();

        $this->methodForm();

        $this->errors();
    }

    private function insert()
    {
        if (!is_null($this->item ?? null)) {
            $this->variableCheck();
            $this->output();
            $this->query();
            $this->labelDefine();

            collect([self::$update, self::$create])->each(function ($method) {
                if (is_null($this->getItemData('set.request.' . $method) ?? null)) {
                    $this->request('nullable', $method);
                }
            });

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
            $model = $this->getItemData('set.rel.model');
            $items = $model::get();
        }

        $this->setItemData('set.list.items', $items);
        $this->listLabel();

        return $this;
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

    #request & method
    function request($string, $method = null)
    {
        $this->checkMethods($method);
        $string = explode("|", implode("|", (array)$string));
        collect($method)->each(function ($method) use ($string) {
            $value = collect((array)  $this->getItemData('set.request.' . $method))->merge($string)->unique()->filter()->values()->toArray();
            $this->setItemData('set.request.' . $method, $value);
        });
        return $this;
    }

    protected function checkMethods(&$method)
    {
        if (is_null($method)) {
            $method = [self::$update, self::$create];
        }
        $method = array_intersect([self::$update, self::$create], (array) $method);
    }

    private function methodForm()
    {
        if(is_null(data_get($this->data,'id'))){
            $this->method=self::$create;
        }else{
            $this->method=self::$update;
        }

        return $this;
    }

    #Output
    static $requestOutput = ['email' => Field::TEXT];

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

            if (!$enabled->filter(fn ($str) => $str == $output)->count()) {
                $output = $enabled->first();
            }
        }

        $this->setItemData('output', $output ?? Field::STATIC, true);
    }

    private function errors()
    {
        $mode ="local"; strtolower(env('APP_ENV', 'production'));

        switch ($mode) {
            case "local":
                break;
            default:
                $this->elements = $this->elements->filter(function ($i) {
                    return data_get($i, 'type', false);
                })->values();
                break;
        }

        $this->elements = $this->elements->map(function ($i) {
            //return data_get($i,'type',false);
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

                    if(!($this->isAttribute($label, $this->collection) || $this->isFillable($label, $this->collection))){
                        $errors = $errors->push('Campo etichetta non valido.');
                    }
                }
            }

            if ($errors->count()) {
                data_set($i, 'errors', $errors);
            }
            return $i;
        })->values();
    }
}
