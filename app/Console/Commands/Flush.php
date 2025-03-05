<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Flush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flush';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears both the Cache and Views';

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
     */
    public function handle(): void
    {
        // Clears the cache
        $this->call('queue:clear');
        $this->call('config:clear');
        $this->call('view:clear');
        $this->call('route:clear');
        $this->call('event:clear');
    }
}
