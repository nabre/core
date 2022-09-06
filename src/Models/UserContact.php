<?php

namespace Nabre\Models;

use App\Models\User;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Jenssegers\Mongodb\Relations\BelongsToMany;
use Jenssegers\Mongodb\Relations\HasMany;
use Jenssegers\Mongodb\Relations\HasOne;
use Nabre\Casts\FontawesomeCast;
use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\Model;
use Nabre\Permission\Traits\HasRoles;

class UserContact extends Model
{
    use HasRoles;
    protected $fillable = ['firstname', 'lastname', 'email', 'phone'];
/*
    protected $attributes = [
        'protected' => false,
        'folder'    => true
    ];*/

    protected $casts = [];

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function getFullnameAttribute()
    {
        return $this->lastname . " " . $this->firstname;
    }
}
