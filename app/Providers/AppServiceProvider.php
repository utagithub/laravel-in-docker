<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\LineFormatter;
use Monolog\Processor\IntrospectionProcessor;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //自定义日志格式
        $this->formatLog();

        //队列开始时重置logSign
        app('queue')->before(function (){
            $this->formatLog();
        });

        //记录mysql查询日志
        if(env('DB_QUERY_LOG_ON')) {
            $this->recordSqlQUeryLog();
        }
    }


    public function formatLog()
    {
        $logSign = strtoupper(env('DB_DATABASE', 'laravel_in_docker')) . "_" . date('md_His') . "_" . rand(100, 999);
        app()['logSign'] = $logSign;

        // Laravel 10 方式获取 logger 实例
        $logger = \Log::getLogger();

        // 自定义 processor 添加文件路径信息
        $logger->pushProcessor(function ($record) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
            foreach ($trace as $frame) {
                // 跳过日志相关的类，找到实际调用日志的文件
                if (isset($frame['file']) &&
                    strpos($frame['file'], 'vendor/monolog') === false &&
                    strpos($frame['file'], 'vendor/laravel') === false &&
                    strpos($frame['file'], 'LogManager') === false) {
                    /*$record['extra']['file'] = $frame['file'];
                    $record['extra']['line'] = $frame['line'];
                    $record['extra']['class'] = $frame['class'] ?? 'N/A';*/

                    // 去除路径前缀
                    $basePath =  base_path();
                    $relativePath = str_replace($basePath, '', $frame['file']);
                    $record['extra']['file'] = $relativePath;
                    $record['extra']['line'] = $frame['line'];
                    break;
                }
            }
            return $record;
        });

        foreach ($logger->getHandlers() as $handler) {
            $logFormat = "%datetime% %level_name% %extra.file% Line:%extra.line% " . $logSign . " - %message% %context%\n";
            $handler->setFormatter(new LineFormatter($logFormat, null, true, true));
        }

    }


    /**
     * 记录mysql查询日志
     *
     */
    public function recordSqlQUeryLog() {
        app('db')->listen(function ($query) {
            //记录执行sql语句
            if (env("DB_QUERY_LOG_TIME_LIMIT", 500) < $query->time) {
                $sql = str_replace("?", "'%s'", $query->sql);
                $sql = vsprintf($sql, $query->bindings);
                $logFile = fopen(storage_path('logs' . DIRECTORY_SEPARATOR . 'laravel-query-' . date('Y-m-d') . '.log'), 'a+');

                $log = "[".date("Y-m-d H:i:s")."] [" . app()['logSign'] . "][" . php_uname('n') . "][".$query->time."]" . $sql . PHP_EOL;
                fwrite($logFile, $log);
                fclose($logFile);
            }
        });
    }




}
