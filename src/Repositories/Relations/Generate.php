<?php

namespace Nabre\Repositories\Relations;

use Nabre\Models\CollectionRelation;

class Generate
{
    var $classes = null;
    var $classesName;
    var $tmp=null;
    var $relation;
    var $combination;

    function __construct()
    {
        $this->combination = $this->relation = collect([]);
        $this->classesCall()
            ->combination();
        $this->parent=$this->combination->where('parent',true)->values();

        $this->tmp=$this->classesName->map(function($class){
            $parentsRel=$this->parent->where('from',$class)->sort()->values();
            $parents=$parentsRel->pluck('to')->unique()->values()->toArray();
            $filterTo=$parentsRel->where('step',1)->pluck('to')->unique()->values()->toArray();
            $filterFrom=$parentsRel->whereNotIn('to',$filterTo)->pluck('to')->unique()->values()->toArray();
            $selector=$this->combination->whereIn('from',$filterFrom)->whereIn('to',$parents)->reject(function($i)use($parents){
                return count(array_diff($i->classes,$parents));
            })->values();
            $parents=collect([]);
            $selector->pluck('classes')->each(function($array)use(&$parents){
                $parents=$parents->merge($array)->unique()->sort()->values();
            });
            $parents=$parents->toArray();
            $machine=$selector->groupBy('from')->map->pluck('with');
            unset($parentsRel);
            unset($selector);
            return get_defined_vars();
        })->dd();
    }

    function classesCall()
    {
        $this->classes = CollectionRelation::get()->existClass('collection.class');
        $this->classesName = $this->classes->pluck('collection.class')->filter()->values();
        return $this;
    }

    function combination()
    {
        $this->classesName->each(function ($class) {
            $this->relationLink($class);
        });
        $this->relation->each(function ($item){
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
        $exclude=$node->links->pluck('modelFrom')->toArray();
        $this->relation->where('modelFrom', $modelFrom)->whereNotIn('model',$exclude)->each(function ($add) use ($node) {
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
        if (count(array_intersect($relTypeList, ['HasMany', 'HasOne'])) || (count(array_intersect($relTypeList, ['BelongsToMany'])) && $relType->last() != 'BelongsToMany')) {
            $node->parent = false;
        } else {
            $node->parent = true;
        }

        $node->with=$links->pluck('name')->implode('.');
        $node->classes=$links->pluck('model')->sort()->values()->toArray();
        $node->step=$links->count();
        $this->combination = $this->combination->push($node);
        return $node;
    }
}
