<?php

namespace App\Jobs;

use App\Models\BloodRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ResetBloodRequestLockJob implements ShouldQueue
{
    protected BloodRequest $bloodRequest;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(BloodRequest $bloodRequest)
    {
        $this->bloodRequest = $bloodRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->bloodRequest && !is_null($this->bloodRequest->locked_by)
         && $this->bloodRequest->status == 'pending') {
            DB::beginTransaction();
            $this->bloodRequest->locked_by = null;
            $this->bloodRequest->save();
            DB::commit();
        }
    }
}
