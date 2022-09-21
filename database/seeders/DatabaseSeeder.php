<?php

namespace Nabre\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    function run()
    {
        Artisan::call('optimize');
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            FormFieldsTypeSeeder::class,
            SettingSeeder::class,
            SettingGroupSeeder::class,
            PageSeeder::class,
            MenuSeeder::class,
        ]);
    }
}
