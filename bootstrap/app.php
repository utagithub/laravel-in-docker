<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/
//$app->configureMonologUsing(function(Monolog\Logger $monolog) {
//    $monolog->pushProcessor(function ($record) use ($monolog) {
//        if(php_sapi_name() == 'cli'){
//            $filename = storage_path('logs/stdout-'.php_sapi_name().'.log');
//        }else{
//            $filename = storage_path('logs/stdout.log');
//        }
//
//        $handler = new Monolog\Handler\RotatingFileHandler($filename);
//        $monolog->setHandlers([$handler]);
//        $logSign = app()['logSign'] ;
//        $logFormat = "%datetime% %level_name% %extra.class%.%extra.function% Line:%extra.line% " . $logSign . " - %message%\n";
//        $handler->setFormatter(new \Monolog\Formatter\LineFormatter($logFormat, 'Y-m-d H:i:s.u', true, true));
//        $traces    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
//        $skip      = [
//            'AppServiceProvider',
//            'Monolog',
//            'Illuminate',
//            'Symfony',
//            'Barryvdh',
//            'Fideloper'
//        ];
//        $extra     = [
//            'class'    => 'UNKNOW',
//            'line'     => 0,
//            'function' => 'UNKNOW'
//        ];
//        foreach ($traces as $key => $trace) {
//            foreach ($skip as $sk) {
//                if (isset($trace['class']) && (strpos($trace['class'], $sk) !== false)) {
//                    continue 2;
//                }
//            }
//            if (isset($trace['class'])) {
//                $extra['class'] = $trace['class'];
//                if (isset($traces[$key - 1]['line'])) {
//                    $extra['line'] = $traces[$key - 1]['line'];
//                }
//                $extra['function'] = $trace['function'];
//                break;
//            }
//        }
//        $record['extra'] = $extra;
//        return $record;
//    });
//});

return $app;
