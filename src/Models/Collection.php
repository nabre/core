<?php

namespace Nabre\Models;

use Jenssegers\Mongodb\Relations\EmbedsMany;
use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\Model;
use Nabre\Models\Embeds\CollectionField;

class Collection extends Model
{
    protected $fillable = ['title', 'class'];
    protected $attributes = [];
    protected $casts = ['title'=>LocalCast::class];

    function fields(): EmbedsMany
    {
        return $this->embedsMany(CollectionField::class);
    }

    function getStringAttribute(){
        return ucfirst(trim($this->title ?? collect(explode("\\",$this->class))->reverse()->first()));
    }
}
