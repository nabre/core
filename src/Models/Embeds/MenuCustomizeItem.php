<?php

namespace Nabre\Models\Embeds;

use Jenssegers\Mongodb\Relations\BelongsTo;
use Nabre\Database\Eloquent\Model;
use Nabre\Models\Page;

class MenuCustomizeItem extends Model
{
    protected $fillable = ['position'];

    function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
