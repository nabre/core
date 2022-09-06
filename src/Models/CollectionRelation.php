<?php

namespace Nabre\Models;

use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\EmbedsMany;
use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\Model;
use Nabre\Models\Embeds\CollectionField;

class CollectionRelation extends Model
{
    protected $fillable = [];
    protected $attributes = [];
    protected $casts = [];

    function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
