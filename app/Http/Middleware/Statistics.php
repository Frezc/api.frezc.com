<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ApiStatistics;

class Statistics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $stat = ApiStatistics::where('ip', $request->ip())->where('path', $request->path())->where('method', $request->method())->first();
        if ($stat != null) {
          $stat->times++;
          $stat->save();
        } else {
          $stat = new ApiStatistics;
          $stat->ip = $request->ip();
          $stat->path = $request->path();
          $stat->method = $request->method();
          $stat->times = 1;
          $stat->save();
        }

        return $next($request);
    }
}
