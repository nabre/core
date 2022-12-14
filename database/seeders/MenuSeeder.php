<?php

namespace Nabre\Database\Seeders;

use Illuminate\Database\Seeder;
use Nabre\Models\Menu as Model;
use Nabre\Models\Page;

class MenuSeeder extends Seeder
{
    function run()
    {
        #autoMenu
        collect(['admin', 'manage', 'user'])->each(function ($page) {
            $text = $icon = true;
            $page = data_get(Page::where('uri', $page)->first(), 'id');
            $data = get_defined_vars();
            $node = Model::whereHas('page', function ($q) use ($page) {
                $q->where('_id', $page);
            })->first();
            if (is_null($node)) {
                $node = Model::create();
                $node->recursiveSave($data);
            }
        });

        #customMenu
        collect([
            [
                'string' => 'mainmenu',
                'text'=>false,
                'icon'=>true,
                'items' => [
                    ['page' => data_get(Page::where(['uri' => 'user'])->first(), 'id')],
                    ['page' => data_get(Page::where(['uri' => 'manage'])->first(), 'id')],
                    ['page' => data_get(Page::where(['uri' => 'admin'])->first(), 'id')],
                    ['page' => data_get(Page::where(['uri' => 'login'])->first(), 'id')],
                    ['page' => data_get(Page::where(['uri' => 'register'])->first(), 'id')],
                    ['page' => data_get(Page::where(['uri' => 'logout'])->first(), 'id')],
                ]
            ]
        ])->each(function ($data) {
            $node = Model::where('string', data_get($data, 'string'))->first();
            if (is_null($node)) {
                $node = Model::create();
                $node->recursiveSave($data);
            }
        });
    }
}
