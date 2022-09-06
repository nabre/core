<?php

namespace Nabre\Policies;

use App\Models\UserContact as Model;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserContactPolicy
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
        return is_null(optional($model->user)) || $user->{$user->getKeyName()} != optional($model->user)->{optional($model->user)->getKeyName()};
    }

    public function userCreate(User $user,Model $model){
        return is_null($model->user) && !is_null($model->email);
    }
}
