<?php

namespace Nabre\Permission\Traits;

use App\Models\Role as ModelsRole;
use Illuminate\Support\Collection;
use Maklad\Permission\Traits\HasRoles as TraitsHasRoles;
use Maklad\Permission\Contracts\RoleInterface as Role;
use Jenssegers\Mongodb\Relations\BelongsToMany;

trait HasRoles
{
    use TraitsHasRoles;
    use HasPermissions;

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('permission.models.role'));
    }

    public function hasAnyRole(Role|array|string|Collection $roles): bool
    {
        $roles = \is_array($roles) ? $roles : \explode('|', $roles);
        $priority = ModelsRole::whereIn("name",$roles)->get()->min('priority');
        $roles= array_unique(array_merge(ModelsRole::where('priority','<',$priority)->get()->pluck('name')->toArray(),$roles));

        return $this->hasRole($roles);
    }
}
