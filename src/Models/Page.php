<?php

namespace Nabre\Models;

use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\BelongsToMany;
use Jenssegers\Mongodb\Relations\HasMany;
use Jenssegers\Mongodb\Relations\HasOne;
use Nabre\Casts\FontawesomeCast;
use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['uri', 'name', 'title', 'protected', 'lvl', 'folder', 'icon', 'disabled'];

    protected $attributes = [
        'protected' => false,
        'disabled' => false,
        'folder'    => true
    ];

    protected $casts = [
        'name' => 'string',
        'uri' => 'string',
        'disabled' => 'boolean',
        'enabled' => 'boolean',
        'protected' => 'boolean',
        'folder' => 'boolean',
        'title' => LocalCast::class,
        'icon' => FontawesomeCast::class,
    ];

    function childs(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    function redirectdefpage(): BelongsTo
    {
        return $this->belongsTo(self::class, 'redirectdefpage_id');
    }

    function redirectedfolder(): HasOne
    {
        return $this->hasOne(self::class, 'redirectdefpage_id');
    }

    function menu(): HasOne
    {
        return $this->hasOne(Menu::class);
    }

    function customMenu(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, null, 'pages_items_ids', 'menu_items_ids');
    }

    function getRootAttribute()
    {
        if ($this->folder) {
            return $this->uri;
        }
        return collect(explode("/", $this->uri))->reverse()->skip(1)->reverse()->implode("/");
    }

    function getStringAttribute()
    {
        $title = (string) ($this->title ??
            collect(explode("/", $this->uri))->filter()->last() ??
            collect(explode("/", $this->name))->last());
        return ucfirst(strtolower($title));
    }

    function getDefPageAttribute()
    {
        return !is_null(optional($this->redirectedfolder)->_id) || $this->folder === true || $this->uri == '/' ? 0 : 1;
    }

    function getEnabledAttribute(){
        return !$this->disabled;
    }

    function getShowStringAttribute()
    {
        return $this->uri;
    }
}
