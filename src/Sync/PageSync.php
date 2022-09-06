<?php

namespace Nabre\Sync;

use Nabre\Models\Page as Model;
use Nabre\Repositories\Pages;
use Nabre\Routing\RouteHierarchy;
use Nabre\Services\AutoSync;

class PageSync extends AutoSync
{
    var $model = Model::class;

    function current()
    {
        return $this->model::where('protected', true);
    }

    function exists()
    {
        $pages = (new RouteHierarchy)->pagesSync();
        return $pages->map(function ($i) {
            $data = ['name' => $i->name, 'uri' => $i->uri, 'protected' => true, 'folder' => is_null($i->action ?? null)];
            if (!is_null($d = Pages::definedConfig($i->name)) ) {
                $data['icon'] = $d['i'] ?? null;
                $data['title'] = $d['t'] ?? null;
            }
            return $data;
        });
    }

    function put($data)
    {
        return $this->model::where('name', $data['name'])->orWhere('uri', $data['uri'])->firstOrCreate();
    }
}
