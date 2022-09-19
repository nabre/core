<?php

namespace Nabre\Database\Seeders;

use Carbon\Carbon;
use Nabre\Models\Role;
use Nabre\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    function run()
    {
        $minPri = Role::whereNotNull("priority")->get()->min("priority");

        $exists = (bool)User::whereHas('roles', function ($q) use ($minPri) {
            $q->where("priority", $minPri);
        })->get()->count();

        if (!$exists) {
            $node = User::create();
            $data = [
                'name' => 'Admin account',
                'email' => 'admin@' . request()->getHttpHost(),
                'password' => 'password',
                "email_verified_at" => Carbon::now(),
                'roles' => [
                    data_get(Role::where('priority', $minPri)->first(), 'id'),
                ],
            ];
            $node->recursiveSave($data);
        }
    }
}
