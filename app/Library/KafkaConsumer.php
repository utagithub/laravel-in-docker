<?php

namespace App\Library;

use \RdKafka;
use Log;

class KafkaConsumer {

    //主题名称
    private $topicName;
    //消费者组标识
    private $groupId;
    //broker列表
    private $brokerList;
    //回调函数
    private $callback;

    public function __construct($topicName, $groupId, $brokerList, $callback)
    {
        $this->topicName  = $topicName;
        $this->groupId    = $groupId;
        $this->brokerList = $brokerList;

        if (!is_callable($callback)) {
            throw new \Exception('回调函数不正确 | callback->'.$callback);
        }

        $this->callback = $callback;
    }

    public function consume() {
        $conf = new \Rdkafka\Conf();
        //消息组id
        $conf->set('group.id', $this->groupId);
        //客户端标识，默认值:"rdkafka"
        $conf->set('client.id', 'client-'.$this->groupId);
        //初始化broker列表，值是host或者host:port
        $conf->set('metadata.broker.list', $this->brokerList);
        //设置初始偏移量 smallest,earliest,largest,latest,error
        $conf->set('auto.offset.reset', 'latest');
        //自动提交偏移量
        $conf->set('enable.auto.commit', 'false');
        //分区分配策略
        $conf->set('partition.assignment.strategy', 'roundrobin');

        $consumer = new \Rdkafka\KafkaConsumer($conf);
        //topic+partition
        // $topicPartition = new \RdKafka\TopicPartition($this->topicName, 0);
        // $earliestOffset = $consumer->offsetsForTimes([$topicPartition], 1576036541000);
        // Log::info("earliestOffset => ".$earliestOffset[0]->getOffset());
        //订阅
        $consumer->subscribe([$this->topicName]);

        Log::info("开始消费主题: {$this->topicName}, groupId: {$this->groupId}");

        $end = false;
        while(!$end) {
            $message = $consumer->consume(120*1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $msg = sprintf("Topic: %s, 分区: %d, 消息内容: %s, 偏移量: %d", $message->topic_name, $message->partition, $message->payload, $message->offset);
                    Log::info("[kafka-$this->topicName]: ". $msg);
                    call_user_func($this->callback, $message->payload);
                    $consumer->commit($message);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    Log::info("[kafka-$this->topicName]: No more messages; will wait for more\n");
                    $end = true;
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $errMessage = '连接Kafka超时, Topic: '.$this->topicName;
                    Log::info($errMessage.'| '.$message->errstr());
                    $end = true;
                    break;
                default:
                    throw new \Exception($message->errstr(), $message->err);
                    break;
            }
        }
    }
}