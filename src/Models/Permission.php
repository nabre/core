<?php

namespace Nabre\Models;

use Illuminate\Database\Eloquent\Concerns\HasEvents;
use Maklad\Permission\Contracts\PermissionInterface;
use Maklad\Permission\Models\Permission as Original;
use Nabre\Casts\LocalCast;
use Nabre\Database\Eloquent\RelationshipsTrait;
use Nabre\Database\Eloquent\RecursiveSaveTrait;

class Permission extends Original
{
    use RelationshipsTrait;
    use RecursiveSaveTrait;
    use HasEvents;

    protected $fillable = [
        'name',
        'slug',
        'guard_name',
    ];
    protected $attributes = [
        'guard_name' => 'web',
    ];

    protected $casts=[
        'slug'=> LocalCast::class,
    ];


    function getEtiAttribute()
    {
        if (empty($this->slug)) {
            return $this->name;
        }
        return $this->slug;
    }

    function getShowStringAttribute()
    {
        return $this->name;
    }

    public static function findByName(string $name, string $guardName = null): PermissionInterface
    {
        $guardName = $guardName ?? (new Guard())->getDefaultName(static::class);

        $permission = static::getPermissions()->filter(function ($permission) use ($name, $guardName) {
            return $permission->name === $name && $permission->guard_name === $guardName;
        })->first();

        if (!$permission) {
            self::firstOrCreate(['name' => $name, 'guard_name' => $guardName]);
            $permission = self::findByName($name, $guardName);
        }

        return $permission;
    }
}
