<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Marshmallow\LaravelFacebookWebhook\Jobs\ProcessFacebookLeadJob as MarshmallowWebhookJob;
use App\Http\Controllers\LeadController;

class ProcessFacebookDataJobs extends MarshmallowWebhookJob
{
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lead = new LeadController();

        $data = json_decode($this->webhookData, true);
        $lead->storeWebhookResponse($data);
        //logger($data);
    }
}