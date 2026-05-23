<?php

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiPerformaneLoger
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = microtime(true) - $startTime;
        ApiLog::create([
            'user_id' => $request->user()?->id,
            'duration' => $duration,
            'endpoint' => $request->path(),
            'method' => $request->method(),
            'ipAddress' => $request->ip(),
            'statusCode' => $response->getStatusCode(),
        ]);
        return $response;
    }
}
