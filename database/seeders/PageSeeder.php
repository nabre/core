<?php

namespace Nabre\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class PageSeeder extends Seeder
{
    function run()
    {
        Artisan::call('page:install');
    }
}
