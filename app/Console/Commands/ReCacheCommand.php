<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ReCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 're:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all caches and optimize the application';

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
        $this->call('optimize:clear');
        $this->call('optimize');
        exec('npm run build');
    }
}
