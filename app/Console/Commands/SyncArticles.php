<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\JoomlaConnect;

class SyncArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:articles {--catid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $cat = [];
        if($this->option('catid')) {
            $cat = [$this->option('catid')];
        }
        $jmc = new JoomlaConnect;

        $jmc->syncArticles($cat);
        
    }
}
