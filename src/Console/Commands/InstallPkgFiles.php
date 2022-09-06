<?php

namespace Nabre\Console\Commands;

use Illuminate\Console\Command;
use Nabre\Database\MongoDB\Backup\Execute;
use Storage;

class InstallPkgFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install:nabrecore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installazione dei contenuti principali del pacchetto nabre/core';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        $this->info("Installazione");

        $directory='files';
        $files=Storage::allFiles($directory);
        dd($files);
    }
}
