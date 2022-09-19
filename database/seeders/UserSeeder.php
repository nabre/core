<?php

namespace Nabre\Database\Seeders;

use Nabre\Models\Role;
use Nabre\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Validation\Rules\Exists;

class UserSeeder extends Seeder
{
    function run()
    {
        $minPri = Role::whereNotNull("priority")->get()->min("priority");

        $exists = (bool)User::whereHas('roles', function ($q) use ($minPri) {
            $q->where("priority", $minPri);
        })->get()->count();

        if (!$exists) {
            $node=User::create();
            $data=[
                'email'=>'demo@admin.com',
                'password'=>'admin',
                'roles'=>[
                    data_get(Role::where('priority',$minPri)->first(),'id'),
                ],
            ];
            $node->recursiveSave($data);
        }
    }
}
