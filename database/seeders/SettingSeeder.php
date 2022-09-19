<?php

namespace Nabre\Database\Seeders;

use Illuminate\Database\Seeder;
use Nabre\Models\Setting as Model;
use Nabre\Repositories\Form\Field;

class SettingSeeder extends Seeder
{
    function run()
    {
        $configKey = config('setting.override');
        collect($configKey)->each(function ($key, $read) {
            $data = [
                config('setting.database.key') => $key,
                config('setting.database.value') => config($read),
            ];

            data_set($data, 'type', Field::TEXT);

            switch ($key) {
                case 'app_debug':
                    data_set($data, 'string', ['it' => 'Debug status']);
                    data_set($data, 'type', Field::BOOLEAN);
                    break;
                case 'app_locale':
                    data_set($data, 'string', ['it' => 'Lingua predefinita']);
                    data_set($data, 'type', Field::LANG_SELECT);
                    break;
                case 'app_name':
                    data_set($data, 'string', ['it' => 'titolo applicazione']);
                    data_set($data, 'type', Field::TEXT);
                    break;
                case 'mail_encryption':
                    data_set($data, 'string', ['it' => 'e-mail: codifica']);
                    data_set($data, 'type', Field::TEXT);
                    break;
            }

            Model::whereDoesntHave('user')->firstOrCreate([config('setting.database.key') => $key], $data);
        });
    }
}
