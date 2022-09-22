<?php

namespace Nabre\Policies;

use App\Models\User as Model;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
        return ($user->lvl_role <= $model->lvl_role || (is_null($model->lvl_role) && !is_null($user->lvl_role)));
    }

    public function delete(User $user,  Model $model)
    {
        return $user->{$user->getKeyName()} != $model->{$model->getKeyName()} && ($user->lvl_role <= $model->lvl_role || (is_null($model->lvl_role) && !is_null($user->lvl_role)));
    }
}
