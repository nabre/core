<?php

namespace Nabre\Console\Commands\MongoDB;

use Illuminate\Console\Command;
use Nabre\Database\MongoDB\Backup\Execute;

class RestoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongodb:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually dump the mongodb database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $disk = \Storage::disk('backup');
        $path = $disk->path('');
        $file=collect($disk->allFiles())->sort()->reverse()->values()->first();
        $file = $path . $file;
        $this->info(Execute::restore($file));
    }
}
