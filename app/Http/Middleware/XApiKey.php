<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $validApiKey = \Cache::get('api-key');

        if ($request->header('X-Api-Key') !== $validApiKey) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
