<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Library\KafkaConsumer;
use App\Events\SendCommonEmail;
use Exception, Log;

class TestKafkaConsume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test_kafka:consume';

    protected $topic;
    protected $groupId;
    protected $brokerList;


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

      $this->topic      = env('KAFKA_TOPIC');
      $this->groupId    = env('KAFKA_GROUP_ID');
      $this->brokerList = env('KAFKA_BROKER_LIST');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
      $consumer = new KafkaConsumer($this->topic, $this->groupId, $this->brokerList, function($message) {
        try {

          $this->info('收到消息: '.$message);

        } catch (\Exception $e) {
          \Log::error($e->getMessage());
          //event(new SendCommonEmail($e, true));
        }

      });

      $consumer->consume();

    }
}
