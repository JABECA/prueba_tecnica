<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $provided = $request->header('X-API-KEY') ?? $request->query('api_key');
        $expected = config('services.api.key');

        if (!$expected || $provided !== $expected) {
            return response()->json([
                'error'   => 'Unauthorized',
                'message' => 'Missing or invalid API key.',
            ], 401);
        }

        return $next($request);
    }
}
