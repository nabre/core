<?php

namespace Nabre\Models;

use Jenssegers\Mongodb\Relations\BelongsToMany;
use Jenssegers\Mongodb\Relations\EmbedsMany;
use Jenssegers\Mongodb\Relations\HasOne;
use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\Model;
use Nabre\Models\Embeds\CollectionField;

class Collection extends Model
{
    protected $fillable = ['title', 'class', 'with', 'position'];
    protected $attributes = [];
    protected $casts = ['title' => LocalCast::class];

    function fields(): EmbedsMany
    {
        return $this->embedsMany(CollectionField::class);
    }

    function parents(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'childs_ids', 'parents_ids');
    }

    function childs(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'parents_ids', 'childs_ids');
    }

    function filter(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'filter2_ids', 'filter_ids');
    }

    function filter2(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'filter_ids', 'filter2_ids');
    }

    function topfilter(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'top_filter2_ids', 'top_filter_ids');
    }

    function topfilter2(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'top_filter_ids', 'top_filter2_ids');
    }

    function system(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'system2_ids', 'system_ids');
    }

    function system2(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'system_ids', 'system2_ids');
    }

    function hide(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'hide_ids', 'hide2_ids');
    }

    function getStringAttribute()
    {
        return ucfirst(trim($this->title ?? collect(explode("\\", $this->class))->reverse()->first()));
    }
}
