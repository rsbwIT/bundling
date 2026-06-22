<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\bpjs\bridginginacbg2;
use Exception;
use Illuminate\Support\Facades\Log;

class SendInacbgClaim implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function handle()
    {
        try {
            $controller = new bridginginacbg2();
            $controller->processFullClaim($this->payload);
        } catch (Exception $e) {
            Log::error('SendInacbgClaim job failed: ' . $e->getMessage(), ['payload' => $this->payload]);
            throw $e;
        }
    }
}
