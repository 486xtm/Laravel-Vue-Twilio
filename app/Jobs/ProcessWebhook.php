<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob as SpatieProcessWebhookJob;

class ProcessWebhook extends SpatieProcessWebhookJob
{

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = json_decode($this->webhookCall, true)['payload'];
        logger($data['lead']);
    }
}
