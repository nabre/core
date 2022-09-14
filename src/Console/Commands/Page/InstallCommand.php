<?php

namespace Nabre\Console\Commands\Page;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Nabre\Models\Page;
use Nabre\Sync\PageSync;
use Nabre\Repositories\Pages;

class InstallCommand extends Command
{
    protected $hidden = true;
    protected $signature = 'page:install';
    protected $description = 'Sync a pages in database';

    public function handle()
    {
        $this->info('Optimize...');
        Artisan::call('optimize');
        $this->info('Pages snyc...');
        (new PageSync);

        $file = Page::where('folder', false)->get();

        Page::where('folder', true)->get()->each(function ($page) use ($file) {
            $file = $file->like('uri', $page->uri . "%")->where('lvl', $page->lvl + 1)->sortBy('uri')->values();
            $data = [
                'childs' => $file->modelKeys(),
                'redirectdefpage' => optional($file->where('name', Pages::restoreName(Pages::definedConfig($page->name, 'd')))->first())->id,
            ];

            if (is_null($data['redirectdefpage']) && is_null(optional($page->redirectdefpage)->id)) {
                $data['redirectdefpage'] = optional($file->sortBy('uri')->first())->id ?? null;
            }

            if (is_null($data['redirectdefpage'])) {
                unset($data['redirectdefpage']);
            }

            $page->recursiveSave($data);
        });

        $this->info('Finished!');
    }
}
