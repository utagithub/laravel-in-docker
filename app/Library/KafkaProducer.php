<?php

namespace App\Library;

use \RdKafka;
use Log;

class KafkaProducer {

    private static $producer;

    /**
     * 生产单条消息
     * @param $topicName
     * @param $message
     * @param null $key
     * @param bool $isAsyn
     * @throws \Exception
     */
    public static function produce($topicName, $message, $key=null, $isAsyn=true) {
        if (is_null($topicName)) {
            throw new \Exception('必须指定kafka主题Topic');
        }

        // init producer
        if (is_null(self::$producer)) {
            $conf = new \RdKafka\Conf();

            /**
             * 异步发送消息时，调用produce方法后，会立即返回，不管有没有发送成功。
             * 发送成功后会回调这个方法，可以这时再去删除消息。
             */
            $conf->setDrMsgCb(function ($kafka, $message) {
                Log::info("[Kafka Message Send]: ". $message->payload);
            });

            // 异步发送消息失败的回调。
            $conf->setErrorCb(function ($kafka, $err, $reason) {
                Log::error("[Kafka Message Error]: ". rd_kafka_err2str($err));
            });

            $producer = new \RdKafka\Producer($conf);
            //$producer->setLogLevel(LOG_DEBUG);
            $producer->addBrokers(env("KAFKA_BROKER_LIST"));
        }

        $topicConf = new \RdKafka\TopicConf();
        /**
         * ack: 指定需要多少个分区副本收到消息，生产者才会认为消息写入成功（对消息的丢失有很大影响）
         * 0: 生产者不会等待服务器的响应，也就说即便是消息发送失败，生产者也无从得知，导致消息丢失。但这种方式吞吐量最高。
         * 1: 首领节点接收到消息就可以了（也有丢失消息的可能性）
         * all: 所有参与复制的节点都收到消息，生产者才会收到来自服务器的成功响应。这种方式是安全的，不过延迟更高。
         */
        $topicConf->set('request.required.acks', 0);
        $topic = $producer->newTopic($topicName, $topicConf);

        /**
         * 消息先被放入到缓冲区，然后会有单独的线程发送到服务器端
         * 第一个参数：partition，可以是RD_KAFKA_PARTITION_UA(自动分区)，或者手动指定分区
         * 第二个参数：msgFlags，必须是[0 | RD_KAFKA_MSG_F_BLOCK]
         * 第三个参数：payload，消息体
         * 第四个参数：key，不是必填，如果有的话，既可以作为传给消费者的附加信息，也可以作为消息分区的依据
         */
        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, $key);

        \Log::info('[Produce Msg]: '.$message);

        /**
         * 生产者发送消息时，会把消息堆积在记录批次里，直到批次的数量达到最大值，或者达到最大发送时间时，才会统一的发送到服务器
         * 下边代码检查缓冲区的消息数量，直到全部发送完毕才退出程序
         */
       $len = $producer->getOutQLen();
       $ret = [];
       while ($len > 0) {
           $len = $producer->getOutQLen();
           $producer->poll(50);
           $ret[] = "len: ".$len."<br>";
       }

       \Log::info('[Produce Msg]:', $ret);
    }

    /**
     * 异步发送消息
     * @param $topic
     * @param $message
     * @param null $key
     * @throws \Exception
     */
    public static function produceAsyn($topic, $message, $key=null) {
        self::produce($topic, $message, $key, true);
    }

    /**
     * 发送批量消息
     * @param $topic
     * @param $messages
     * @param null $key
     * @throws \Exception
     */
    public static function produceBatch($topic, $messages, $key=null) {
        if (!is_array($messages)) {
            $messages = array($messages);
        }

        foreach ($messages as $message) {
            self::produce($topic, $message, $key);
        }
    }

    /**
     * 异步发送批量消息
     * @param $topic
     * @param $messages
     * @param null $key
     * @throws \Exception
     */
    public static function produceBatchAsyn($topic, $messages, $key=null) {
        if (!is_array($messages)) {
            $messages = array($messages);
        }

        foreach ($messages as $message) {
            self::produceAsyn($topic, $message, $key);
        }
    }
}
