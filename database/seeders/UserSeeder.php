<?php

namespace Nabre\Database\Seeders;

use Carbon\Carbon;
use Nabre\Models\Role;
use Nabre\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    function run()
    {
        Artisan::call('optimize');
        $minPri = Role::whereNotNull("priority")->get()->min("priority");

        $exists = (bool)User::whereHas('roles', function ($q) use ($minPri) {
            $q->where("priority", $minPri);
        })->get()->count();

        if (!$exists) {
            $node = User::create();
            $data = config('auth.adminaccountdefault');
            data_set($data, "email_verified_at", Carbon::now());
            data_set($data, 'roles', [
                data_get(Role::where('priority', $minPri)->first(), 'id'),
            ]);

            $node->recursiveSave($data);
        }
    }
}
