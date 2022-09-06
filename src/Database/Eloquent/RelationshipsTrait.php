<?php

namespace Nabre\Database\Eloquent;

use ErrorException;
use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Str;

trait RelationshipsTrait
{
    public function definedRelations()
    {
        $model = new static;
        $reflector = new ReflectionClass($model);

        return collect($reflector->getMethods())->filter(function ($method) {

            $instance = !is_null($method->getReturnType())  && method_exists($method->getReturnType(), 'getname') ? $method->getReturnType()->getName() : null;
            return empty($method->getParameters()) && (str_contains(
                $instance,
                'Illuminate\Database\Eloquent\Relations'
            ) || str_contains(
                $instance,
                'Jenssegers\Mongodb\Relations'
            ));
        })->map(function ($method) use ($model) {
            try {
                $return = $method->invoke($model);

                $ownerKey = null;
                if ((new ReflectionClass($return))->hasMethod('getOwnerKey'))
                    $ownerKey = $return->getOwnerKey();
                else {
                    $segments = explode('.', $return->getQualifiedParentKeyName());
                    $ownerKey = $segments[count($segments) - 1];
                }

                try {
                    $foreignKey = (new ReflectionClass($return))->hasMethod('getForeignKey')
                        ? $return->getForeignKey()
                        : $return->getForeignKeyName();
                } catch (\Throwable $th) {
                    $foreignKey = null;
                }

                $relationships = (object) [
                    'name' => $method->getName(),
                    'type' => (new ReflectionClass($return))->getShortName(),
                    'model' => (new ReflectionClass($return->getRelated()))->getName(),
                    'foreignKey' => $foreignKey,
                    'ownerKey' => $ownerKey,
                ];
            } catch (ErrorException $e) {
            }

            return $relationships ?? null;
        })->filter()->sortBy("name")->values();
    }

    function reletionshipFind(string $name)
    {
        return $this->definedRelations()->where('name', $name)->first();
    }

    public function attributesList()
    {
        $model = new static;
        return collect((new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC))->pluck('name')->filter(function ($name) {
            $posGet = strpos($name, 'get');
            $posAtt = strpos($name, 'Attribute');
            return $posGet !== false && $posGet == 0 && $posAtt !== false && $posAtt == (strlen($name) - 9)  && strlen($name) > (3 + 9);
        })->map(function ($name) {
            return Str::snake(substr($name, 3, -9));
        })->sort()->values()->toArray();
    }
}
