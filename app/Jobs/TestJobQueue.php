<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TestJobQueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->queue = 'TestJobQueue';
        \Log::info("队列任务TestJob测试开始");
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info("队列任务TestJob测试执行中");
        \Log::info("队列任务TestJob测试结束");
    }
}
