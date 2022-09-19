<?php

namespace Nabre\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseBuildSeeder extends Seeder
{
    function run()
    {
        $this->call([
            RoleSeeder::class,
            FormFieldsTypeSeeder::class,
            SettingSeeder::class,
            SettingGroupSeeder::class,
        ]);
    }
}
