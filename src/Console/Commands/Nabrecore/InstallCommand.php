<?php

namespace Nabre\Console\Commands\Nabrecore;

use Illuminate\Console\Command;
use Nabre\Database\MongoDB\Backup\Execute;
use Storage;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nabrecore:install';

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
/*
        $directory=__DIR__.'/../../../files/';
        $this->info($directory);
        $files=Storage::allFiles($directory);
        dd($files);*/
    }
}
