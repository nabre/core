<?php

namespace Nabre\Services;

use App\Models\User;

class UserContactService
{
    static function generateUser($contact)
    {
        $user = User::where('email',$contact->email)->firstOrCreate();
        $contact->recursiveSave(['user' => $user->id]);
    }
}
