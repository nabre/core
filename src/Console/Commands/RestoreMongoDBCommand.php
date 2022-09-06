<?php

namespace Nabre\Console\Commands;

use Illuminate\Console\Command;
use Nabre\Database\MongoDB\Backup\Execute;

class RestoreMongoDBCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:mongodb';

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
