<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiStatsLogger
{
    public function handle($request, Closure $next)
    {
        $startTime = microtime(true);
        $queries = [];

        // Listen for queries and collect their stats
        DB::listen(function ($query) use (&$queries) {
            $queries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time, // ms
            ];
        });

        $response = $next($request);

        $endTime = microtime(true);
        $loadingTime = round(($endTime - $startTime) * 1000, 2); // ms

        // Log the stats
        Log::info('API Stats', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'loading_time_ms' => $loadingTime,
            'queries' => $queries,
        ]);

        return $response;
    }
}
