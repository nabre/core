<?php

namespace Nabre\Observers;

use App\Models\UserContact as Model;

class UserContactObserver
{
    function saved(Model $model)
    {
        //aggiorna user
        $user = $model->user;
        $data = ['email' => $model->email, 'name' => $model->fullname];
        optional($user)->recursiveSave($data);
    }

    function created(Model $model)
    {
        //inserisci valori User
        if (!is_null($user = $model->user)) {
            $data = ['email' => $user->email, 'firstname' => $user->name];
            $model->recursiveSave($data);
        }
    }
}
