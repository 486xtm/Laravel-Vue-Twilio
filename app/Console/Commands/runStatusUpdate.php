<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\UpdateStatusController;


class runStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-status-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update leads status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //STATUS UPDATE
        $update = new UpdateStatusController();
        $update->setStatusArchive();
        $update->setStatusPending();
    }
}
