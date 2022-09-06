<?php

namespace Nabre\Observers;

use App\Models\User as Model;
use App\Models\UserContact;
use Carbon\Carbon;

class UserObserver
{
    function creating(Model $model)
    {
        if (is_null($model->password) && is_null($model->email_verified_at)) {
            $model->email_verified_at = Carbon::now()->format('Y-m-d\TH:i:s');
        }

    }

    function saved(Model $model)
    {
        if (is_null($model->contact)) {
            $contact = UserContact::where('email', $model->email)->first();
            if (!is_null($contact)) {
                $model->recursiveSaveQuietly(['contact' => $contact->id]);
            }
        }
    }
}
