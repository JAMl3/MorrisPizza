<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ThrottleLogins
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next)
    {
        $key = $this->resolveRequestSignature($request);

        if ($this->limiter->tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again in ' . 
                    $this->limiter->availableIn($key) . ' seconds.'
            ], 429);
        }

        $this->limiter->hit($key, 60); // 1 minute decay

        $response = $next($request);

        if ($response->getStatusCode() === 422) {
            $this->limiter->hit($key, 60);
        }

        return $response;
    }

    protected function resolveRequestSignature(Request $request)
    {
        return Str::lower($request->input('email')) . '|' . $request->ip();
    }
} 