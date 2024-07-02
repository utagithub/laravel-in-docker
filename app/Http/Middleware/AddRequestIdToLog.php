<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Symfony\Component\HttpFoundation\Response;
use Ramsey\Uuid\Uuid;
class AddRequestIdToLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 生成唯一请求 ID
        $requestId = (string) Uuid::uuid4();

        // 将请求 ID 添加到日志上下文中
        \Log::withContext(['request_id' => $requestId]);

        return $next($request);
    }
}
