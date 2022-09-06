<?php

namespace Nabre\Console\Commands;

use Illuminate\Console\Command;
use Nabre\Database\MongoDB\Backup\Execute;

class DumpMongoDBCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:mongodb';

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
        $info=Execute::dump();
        $this->info($info);
    }
}
