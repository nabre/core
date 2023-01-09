<?php

namespace Nabre\Database\Eloquent;

trait RecursiveSaveTrait
{

    function readValue($name)
    {
        $value = $this;
        collect(explode(".", $name))->each(function ($v) use (&$value) {
            if (!is_null($value)) {
                if (in_array($v, $this->getFillable())) {
                    $value = $value->getRawOriginal($v);
                } elseif (!is_null($rel = $this->relationshipFind($v))) {
                    $value = $value->$v;
                    switch ($rel->type) {
                        case "BelongsTo":
                        case "HasOne":
                            $value = optional($value)->id;
                            break;
                        case "HasMany":
                        case "BelongsToMany":
                            $value = optional($value)->modelKeys();
                            break;
                    }
                } else {
                    $value = $value->$v;
                }
            }
        });

        return $value;
    }

    function toArray($attributes = false)
    {
        $names = collect($this->getFillable())
            ->merge($this->definedRelations()->pluck('name'))
            ->push($this->getKeyName());

        if ($attributes) {
            $names = $names->merge($this->attributesList());
        }

        return $names->unique()
            ->map(function ($name) {
                $value = $this->readValue($name);
                return get_defined_vars();
            })->pluck('value', 'name')->toArray();
    }

    function recursiveSaveQuietly(array $data, $btmSync = true)
    {
        return $this->recursiveSave($data, $btmSync, true);
    }

    function recursiveSave(array $data, $syncBool = true, $saveQuietly = false)
    {
        $keyName = $this->getKeyName();

        $relations = $this->definedRelations();
        $relations->whereIn('name', array_keys($data))->map(fn ($i) => data_set($i, 'value', data_get($data, data_get($i, 'name'))))->each(function ($rel) use ($syncBool) {
            $name = data_get($rel, 'name');
            $type = data_get($rel, 'type');
            $model = data_get($rel, 'model');
            $collection = new $model;
            $data = data_get($rel, 'data');

            $instance = $model::whereIn('_id', (array) $data)->get();
            $cont = $this->$name();

            switch ($type) {
                case 'BelongsTo':
                    $cont->dissociate();
                    $cont->associate(data_get($instance->first(), $collection->getKeyName()));
                    break;
                case 'BelongsToMany':
                    $cont->sync($instance->modelKeys(), $syncBool);
                    break;
                case 'HasOne':
                    $fk = data_get($rel, 'foreignKey');
                    $pk = data_get($rel, 'ownerKey');
                    $instance = $instance->first();
                    if (data_get($model, $pk) != data_get($instance, $fk)) {
                        $instance->unset($fk);
                        if (!is_null($instance)) {
                            $cont->save($instance);
                        }
                    }
                    break;
                case 'HasMany':
                    $fk = data_get($rel, 'foreignKey');
                    foreach ($cont as $a) {
                        $a->unset($fk);
                    }
                    $cont->saveMany($instance);
                    break;
                case 'EmbedsOne':

                    break;
                case 'EmbedsMany':

                    break;
            }
        });

        $data = collect($data)
            ->reject(fn ($v, $k) => in_array($k, $relations->pluck('name')->toArray()))
            ->reject(fn ($v, $k) => in_array($k, $this->attributesList()))
            ->map(function ($val, $key) {
                $type = data_get($this->casts, $key);
                switch ($type) {
                    case "array":
                        $val = array_values(array_filter((array)$val, 'strlen'));
                        break;
                    case "boolean":
                        $val = (bool)$val;
                        break;
                    case "integer":
                        $val = (int)$val;
                        break;
                    case "object":
                        $val = (object)$val;
                        break;
                    case "string":
                        $val = (string)$val;
                        break;
                }
                return $val;
            })->toArray();

        $this->fill($data);

        if ($saveQuietly) {
            $this->saveQuietly();
        } else {
            $this->save();
        }

        return $this;

        #carica model contronto getKeyName()

        /*  if (($model->$keyName ?? null) != ($data[$keyName] ?? null) && !is_null($data[$keyName] ?? null)) {
            $class = get_class($model);
            $model = $class::firstOrNew($data[$keyName]);
        }*/
        /*
        if(is_null(data_get($model,'id'))){
            $model->saveQuietly();
        }

        $data = collect(array_undot($data));

        #salva relazioni
        $find = $model->definedRelations()->pluck('name')->toArray();
        $dataSave = $this->findData($data, $find)->toArray();

        if(count($dataSave)){
            if ($saveQuietly) {
                $model->saveQuietly();
            } else {
                $model->save();
            }
        }

        foreach ($dataSave as $name => $value) {
            $rel = $model->relationshipFind($name);
            $modelRel = $rel->model;
            if (in_array($rel->type, ["EmbedsMany", "EmbedsOne"])) {
            } else
                ## Nested
                if (is_array($value) && isAssoc((array) $value)) {
                    $modelNest = (new $modelRel)->{__FUNCTION__}((array) $value);
                    $value = $modelNest->_id;
                } else {
                    $list = (array) $value;
                    $valList = [];
                    foreach ($list as $val) {
                        if (is_array($val) && isAssoc($val)) {
                            $modelNest = (new $modelRel)->{__FUNCTION__}($val, $btmSync);
                            $valList[] = data_get($modelNest,'id');
                        } else {
                            $valList[] = $val;
                        }
                    }
                    $value = $valList;
                }
            #####

            $type = $rel->type;
            $instance = $modelRel::whereIn('_id', (array) $value)->get();
            $asso = $model->$name();

            switch ($type) {
                case "HasOne":
                    $fk = $rel->foreignKey;
                    $pk = $rel->ownerKey;
                    $instance = $instance->first();
                    if (($model->$pk ?? null) != ($instance->$fk ?? null)) {
                        $instance->unset($fk);
                        if (!is_null($instance)) {
                            $asso->save($instance);
                        }
                    }
                    break;
                case "HasMany":
                    $fk = $rel->foreignKey;
                    foreach ($asso as $a) {
                        $a->unset($fk);
                    }
                    $asso->saveMany($instance);
                    break;
                case "BelongsTo":
                    $asso->dissociate();
                    $asso->associate($instance->first()->_id ?? null);
                    break;
                case "BelongsToMany":
                    $syncBool = $btmSync;
                    $asso->sync($instance->modelKeys(), $syncBool);
                    break;
                case "EmbedsOne":
                    if (is_null($value)) {
                        $model->unset($name);
                    } else {
                        $dbItem = $asso;
                        if (is_null($dbItem)) {
                            $dbItem = $asso->create();
                        }
                     //   $dbItem->recursiveSave((array)$value);
                    }
                    break;
                case "EmbedsMany":
                    if (is_null($value) || !count($value)) {
                        $model->unset($name);
                    } else {
                        $notDelete = [];
                        $keyNameEm = (new $rel->model)->getKeyName();
                        foreach ($value as $it) {
                            $it = (array)$it;
                            $dbItem = $asso->where($keyNameEm, $it[$keyNameEm] ?? null)->first();
                            if (is_null($dbItem)) {
                                $dbItem = $asso->create();
                            }
                            $i = $dbItem->recursiveSave($it);
                            $notDelete[] = $i->$keyNameEm;
                        }
                        $asso->whereNotIn($keyNameEm, $notDelete)->each->delete();
                    }
                    break;
            }
        }

        #salva variabili
        //$find = $model->getFillable();
        $find = array_diff(array_keys($data->toArray()), $find);
        $casts = $model->casts;
        $dataSave = $this->findData($data, $find)->map(function ($val, $key) use ($casts) {
            $type = $casts[$key] ?? null;
            switch ($type) {
                case "array":
                    $val = array_values(array_filter((array)$val, 'strlen'));
                    break;
                case "boolean":
                    $val = (bool)$val;
                    break;
                case "integer":
                    $val = (int)$val;
                    break;
                case "object":
                    $val = (object)$val;
                    break;
                case "string":
                    $val = (string)$val;
                    break;
            }
            return $val;
        })->toArray();

        $model->fill($dataSave);

        if ($saveQuietly) {
            $model->saveQuietly();
        } else {
            $model->save();
        }*/
    }

    protected function findData($data, array $find)
    {
        $varName = array_intersect(array_keys($data->toArray()), $find);
        return  $data->filter(function ($val, $key) use ($varName) {
            return in_array($key, $varName);
        });
    }
}
