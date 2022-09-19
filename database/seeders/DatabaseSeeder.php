<?php

namespace Nabre\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    function run()
    {
        $this->call([
            RoleSeeder::class,
            FormFieldsTypeSeeder::class,
            SettingSeeder::class,
            SettingGroupSeeder::class,
            PageSeeder::class,
            MenuSeeder::class,
            UserSeeder::class,
        ]);
    }
}
