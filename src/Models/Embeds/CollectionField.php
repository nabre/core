<?php

namespace Nabre\Models\Embeds;

use Jenssegers\Mongodb\Relations\BelongsTo;
use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\Model;
use Nabre\Models\Collection;

class CollectionField extends Model
{
    protected $fillable = ['name', 'title'];
    protected $attributes = [];
    protected $casts = ['title' => LocalCast::class];

    function coll(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    function getStringAttribute()
    {
        return ucfirst($this->title ?? $this->relation_string ?? $this->name);
    }

    function getRelationStringAttribute()
    {
        $rel = $this->relation;
        return is_null($rel) ? null : optional(Collection::where('class', $rel->model)->first())->string;
    }

    function getRelationAttribute()
    {
        $class = optional($this->coll)->class;
        return (new $class)->relationshipFind($this->name);
    }

    function getIsRelationAttribute()
    {
        return !is_null($this->relation);
    }

    function getTypeAttribute()
    {
        $class = $this->coll->class;
        $model = new $class;

        if (!is_null($model->relationshipFind($this->name))) {
            return 'relation';
        } else
        if (in_array($this->name, $model->attributesList())) {
            return 'attribute';
        } else
        if (in_array($this->name, $model->getFillable())) {
            return 'fillable';
        }
    }

    function getIconTypeAttribute()
    {
        switch ($this->type) {
            case "fillable":
                return '<i class="fa-solid fa-pen-to-square"></i>';
                break;
            case "relation":
                return '<i class="fa-solid fa-link"></i>';
                break;
            case "attribute":
                return '<i class="fa-solid fa-tag"></i>';
                break;
        }
    }
}
