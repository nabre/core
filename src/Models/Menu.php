<?php

namespace Nabre\Models;

use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\EmbedsMany;
use Nabre\Database\Eloquent\Model;
use Nabre\Models\Embeds\MenuCustomizeItem;

class Menu extends Model
{
    protected $fillable = ['string', 'icon', 'text','tree'];
    protected $attributes = [
        'icon' => true,
        'text'    => true
    ];

    protected $casts = [
        'icon' => 'boolean',
        'text' => 'boolean',
        'tree' => 'boolean',
    ];

    function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    function items(): EmbedsMany
    {
        return $this->embedsMany(MenuCustomizeItem::class, null, 'page_items');
    }

    function getNameAttribute()
    {

        if ($this->auto) {
            return str_replace("/", ".", $this->page->uri);
        }
        return $this->string;
    }

    function getAutoAttribute()
    {
        return (bool) !is_null($this->page);
    }
}
