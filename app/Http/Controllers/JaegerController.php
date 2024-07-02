<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JaegerController extends Controller
{
    public function index()
    {
        Log::info('jaeger index');
        $this->test1();
    }
    public function insert()
    {
        Log::info('jaeger insert');
    }

    public function test1()
    {
        Log::info('jaeger test');
        $this->test2();
    }

    public function test2()
    {
        Log::info('jaeger test2');
        $this->test3();

    }

    public function test3()
    {
        Log::info('jaeger test3');
    }



}






