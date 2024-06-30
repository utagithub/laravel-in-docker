<?php

namespace App\Tools;

use Jaeger\Config;
class Jaeger
{
    public $config;
    public function __construct()
    {
        $this->config = new Config(
            config('jaeger'),
            env('APP_NAME','laravel-in-docker')
        );
    }
}