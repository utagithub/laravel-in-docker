<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use OpenTracing\GlobalTracer;
use Symfony\Component\HttpFoundation\Response;
use App\Tools\Jaeger;

class JargerMiddleware{

    public $jaeger;

    public function __construct(Jaeger $jaeger)
    {
        $this->jaeger = $jaeger;
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->jaeger->config->initializeTracer();
        $tracer = GlobalTracer::get();
        $scope = $tracer->startActiveSpan($request->getRequestUri(), []);
        $scope->getSpan()->setTag($request->getRequestUri(), json_encode($request->all()));
        $scope->getSpan()->log($request->all());
        $scope->close();
        $tracer->flush();
        return $next($request);
    }
}
