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
    protected $fillable = ['title', 'class','with'];
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

    function topfilter(): BelongsToMany
    {
        return $this->belongsToMany(self::class, null, 'top_filter2_ids', 'top_filter_ids');
    }

    function getStringAttribute()
    {
        return ucfirst(trim($this->title ?? collect(explode("\\", $this->class))->reverse()->first()));
    }
}
