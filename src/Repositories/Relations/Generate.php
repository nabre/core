<?php

namespace Nabre\Repositories\Relations;

use Nabre\Models\Collection;
use Illuminate\Support\Str;

class Generate
{
    var $classes = null;
    var $classesName;
    var $tmp = null;
    var $relation;
    var $combination;

    function __construct()
    {
        $this->combination = $this->relation = collect([]);
        $this->classesCall();
        $this->combination();
        $this->parent = $this->combination->where('parent', true)->values();

        $this->tmp = $this->classesName->map(function ($class) {
            $parentsRel = $this->parent->where('from', $class)->sort()->values();
            $parents = $parentsRel->pluck('to')->unique()->sort()->values()->toArray();
            $filterTo = $parentsRel->where('step', 1)->pluck('to')->unique()->values()->toArray();
            $btm = $parentsRel->where('step', 1)->where('type', 'BelongsToMany')->pluck('to')->unique()->values()->toArray();
            $system = $this->combination->where('from', $class)->pluck('to')->push($class)->unique()->sort()->values()->toArray();
            //unset($parentsRel);
            return get_defined_vars();
        });

        $this->recursiveParent();

        $this->tmp = $this->tmp->map(function ($_item) {
            foreach (array_keys($_item) as $_key) {
                $$_key = data_get($_item, $_key);
            }
            unset($_key);
            unset($_item);

            $filterFrom = array_diff($parents, $filterTo);
            $selector = $this->combination->whereIn('from', $filterFrom)->whereIn('to', $parents)->reject(function ($i) use ($parents) {
                return count(array_diff($i->classes, $parents));
            })->values();

            $with = $selector->groupBy('from')->map(function ($with, $from) {
                $with = $with->groupBy('to')->map(function ($with, $to) {
                    $with =  $this->withOptimize($with->pluck('with'))->toArray();
                    return get_defined_vars();
                })->values()->toArray();
                return get_defined_vars();
            })->values()->toArray();

            return get_defined_vars();
        })->pluck(null, 'class');

        $this->collectionPosition();

        $this->tmp->each(function ($item) {
            $model = $this->classes->where('class', data_get($item, 'class'))->first();
            $data = [
                'filter' => $this->convertClassToId($item, 'filterTo'),
                'topfilter' => $this->convertClassToId($item, 'filterFrom'),
                'parents' => $this->convertClassToId($item, 'parents'),
                'system' => $this->convertClassToId($item, 'system'),
                'with' => data_get($item, 'with'),
                'position' => data_get($item, 'position'),
            ];
            $model->recursiveSave($data);
        });
    }

    private function collectionPosition($position = 1, $lvl = 0)
    {
        $sorted = $this->tmp->whereNotNull('position')->pluck('class')->toArray();

        $items = $this->tmp->whereNotIn('class', $sorted)->reject(function ($i) use ($sorted) {
            return count(array_diff(data_get($i, 'parents'), $sorted, (array)data_get($i, 'class')));
        })->map(function ($i) {
            data_set($i, 'parents_count', count(data_get($i, 'parents')));
            return $i;
        });

        $min=$items->pluck('parents_count')->min();

        $items->where('parents_count',$min)->each(function ($i, $k) use (&$position) {
            data_set($i, 'position', $position);
            $this->tmp = $this->tmp->put($k, $i);
            $position++;
        });

        if (count($sorted) < $this->tmp->count() && $lvl <= $this->tmp->count()) {
            $this->collectionPosition($position, $lvl + 1);
        }
    }

    private function withOptimize($with)
    {
        return $with->reject(function ($haystack) use ($with) {
            $bool = false;
            $with->each(function ($needle) use (&$bool, $haystack) {
                if (!$bool) {
                    $pos = strpos($haystack, $needle);
                    $bool = $pos !== false && $pos == 0 && $haystack != $needle;
                }
            });
            return $bool;
        })->sort()->values();
    }

    function convertClassToId($target, $key)
    {
        $value = (array)data_get($target, $key);
        return $this->classes->whereIn('class', $value)->pluck('id')->toArray();
    }

    function recursiveParent($bool = false, $inc = 0)
    {
        $this->tmp = $this->tmp->map(function ($item) use (&$bool) {
            $parents = collect(data_get($item, 'parents'));
            $newParents = $parents;
            $class = data_get($item, 'class');
            $btm = data_get($item, 'btn');
            $this->tmp->whereIn('class', data_get($item, 'parents'))->whereNotIn('class', $btm)->pluck('parents')->reject(function ($parents) use ($class) {
                return in_array($class, $parents);
            })->each(function ($add) use (&$newParents) {
                $newParents = $newParents->merge($add)->unique()->sort()->values();
            });

            $condition = count(array_diff($newParents->toArray(), $parents->toArray()));
            if ($condition) {
                $bool = true;
            }
            $item['parents'] = $newParents->toArray();
            return $item;
        });

        if ($inc < 20) {
            $this->recursiveParent($bool, $inc + 1);
        }
    }
    /*
    function order($order = 0)
    {
        $list = $this->tmp->whereHas('order');
        $parents = $list->pluck('class')->values()->toArray();
        $this->tmp = $this->tmp->map(function ($item) use (&$order, $parents) {
            if (!in_array(data_get($item, 'class'), $parents) && !count(array_diff(data_get($item, 'parents'), $parents))) {
                $item['order'] = $order;
                $order++;
            }
            return $item;
        });

        if ($this->tmp->whereDoesntHave('order')->count()) {
            $this->order($order);
        }
    }*/

    function classesCall()
    {
        $this->classes = Collection::get();
        $this->classesName = $this->classes->pluck('class')->filter()->values();
        return $this;
    }

    function combination()
    {
        $this->classesName->each(function ($class) {
            $this->relationLink($class);
        });
        $this->relation->each(function ($item) {
            $node = $this->node($item);
            $this->linkRelation($node);
        });
        return $this;
    }

    function relationLink($class, array $relations = ['BelongsTo', 'BelongsToMany', 'HasMany', 'HasOne'])
    {
        $model = new $class;
        $rel = $model->definedRelations()->whereIn('type', $relations)->whereIn('model', $this->classesName->toArray())->map(function ($rel) use ($class) {
            $rel->modelFrom = $class;
            return $rel;
        })->values();
        $this->relation = $this->relation->merge($rel);
        return $this;
    }

    function linkRelation($node)
    {
        $modelFrom = $node->links->pluck('model')->last() ?? $node->from;
        $exclude = $node->links->pluck('modelFrom')->toArray();
        $this->relation->where('modelFrom', $modelFrom)->whereNotIn('model', $exclude)->each(function ($add) use ($node) {
            $this->linkRelation($this->node($add, $node));
        });
        return $this;
    }

    function node($add, $node = null)
    {
        $node = is_null($node) ? (object)[] : clone $node;
        $links = $node->links = (clone ($node->links ?? collect([])))->push($add);
        $node->from = $links->pluck('modelFrom')->first();
        $node->to = $links->pluck('model')->last();

        $relType = $links->pluck('type');
        $relTypeList = $relType->unique()->values()->toArray();
        if (
            count(array_intersect($relTypeList, ['HasMany', 'HasOne', 'BelongsToMany']))
            /* || $links->where('type', 'BelongsToMany')->count() > 1
            || ($links->where('type', 'BelongsToMany')->count() == 1 && $links->count() > 1 && $relType->last() != 'BelongsToMany')*/
        ) {
            $node->parent = false;
        } else {
            $node->parent = true;
            $node->type = $links->last()->type;
        }

        $node->with = $links->pluck('name')->implode('.');
        $node->classes = $links->pluck('model')->sort()->values()->toArray();
        $node->step = $links->count();
        $this->combination = $this->combination->push($node);
        return $node;
    }
}
