<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JaegerController extends Controller
{
    public function index()
    {
        Log::info('jaeger index');
    }
    public function insert()
    {
        Log::info('jaeger insert');
    }




}






