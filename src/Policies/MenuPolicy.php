<?php

namespace Nabre\Policies;

use Nabre\Models\Menu as Model;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MenuPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {

    }

    function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Model $model)
    {
        return true;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user,  Model $model)
    {
        return true;
    }

    public function delete(User $user,  Model $model)
    {
        return true;
    }
}
