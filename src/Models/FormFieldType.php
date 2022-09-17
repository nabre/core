<?php

namespace Nabre\Models;

use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\Model;
use Nabre\Repositories\Form\Field;

class FormFieldType extends Model
{
    protected $fillable = [
        'name',
        'key'
    ];

    protected $attributes = [
        'type' => Field::TEXT,
    ];

    protected $casts = [
        'name' => LocalCast::class,
    ];

    function getStringAttribute()
    {
        return ucfirst($this->name ?? $this->key);
    }

    function getShowStringAttribute()
    {
        return $this->key;
    }
}
