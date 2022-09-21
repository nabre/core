<?php

namespace Nabre\Database\Seeders;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
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
           /* $data = config('auth.adminaccountdefault') ?? [];
            data_set($data, "email_verified_at", Carbon::now());
            data_set($data, 'roles', [
                data_get(Role::where('priority', $minPri)->first(), 'id'),
            ]);*/
            $data=[
                'name'=>'Account admin',
                'email'=>'admin@account.test',
                'password'=>'password',
                "email_verified_at"=> Carbon::now(),
                'roles'=> [
                    data_get(Role::where('priority', $minPri)->first(), 'id'),
                ]
            ];

            if (!is_null(data_get($data, 'email')) && !is_null(data_get($data, 'password'))) {
                $node = User::create();
                $node->recursiveSave($data);
            }
        }
    }
}
