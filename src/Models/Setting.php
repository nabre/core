<?php

namespace Nabre\Models;

use App\Models\User;
use Jenssegers\Mongodb\Relations\BelongsTo;
use Nabre\Casts\LocalCast;
use Nabre\Casts\SettingTypeCast;
use Nabre\Database\Eloquent\Model;
use Nabre\Repositories\Form\Field;

class Setting extends Model
{
    protected $fillable = [
        'type',
        'name',
        'description',
        'user_set',
    ];

    function __construct()
    {
        $this->fillable[] = config('setting.database.key');
        $this->fillable[] = config('setting.database.value');

        parent::__construct();
    }

    protected $attributes = [
        'value' => null,
    ];

    protected $casts = [
        'type' => SettingTypeCast::class,
        'name' => LocalCast::class,
        'user_set' => 'boolean',
    ];

    function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    function settingGroup(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class);
    }

    function getTypeLoadAttribute()
    {
        return $this->getRawOriginal('type');
    }

    function getStringAttribute()
    {
        return $this->name ?? $this->key;
    }

    function getShowStringAttribute()
    {
        $key=config('setting.database.key');
        return $this->$key;
    }
}
