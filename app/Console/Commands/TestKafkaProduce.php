<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Library\KafkaProducer;

class TestKafkaProduce extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test_kafka:produce';

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
     * @return mixed
     */
    public function handle()
    {
        $data = [
            "head" => [
                "type" => "OVERDUE",
                "messageId" => "341254542354",
                "binlogTime" => 341254542354,
                "canalTime" => 432512354235,
                "collectorTime" => 432523454
            ],
            "data" => [
                "loanId" => 1,
                "period" => 1
            ]
        ];
        //KafkaProducer::produce(env('KAFKA_TOPIC'), 'hello');
        KafkaProducer::produce(env('KAFKA_TOPIC'), json_encode(['text' => '哈哈哈', 'id' => 3, 'desc' => ['描述']], JSON_UNESCAPED_UNICODE),"HAFAKJHFKA141798147");
        $this->info('发送消息: '.json_encode(['text' => '哈哈哈', 'id' => 3, 'desc' => ['描述']]));
    }
}
