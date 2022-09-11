<?php

namespace Nabre\Models;

use Jenssegers\Mongodb\Relations\HasMany;
use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\Model;

class SettingGroup extends Model
{
    protected $fillable = [
        'title',
        'name',
    ];

    protected $casts = [
        'title' => LocalCast::class,
    ];

    function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    function getStringAttribute()
    {
        return $this->title ?? $this->name;
    }
}
