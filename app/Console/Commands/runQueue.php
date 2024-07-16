<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\UserQueueController;

class runQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-queue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a queue of users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $queue = new UserQueueController();
        $queue->queueUsers();
    }
}
