<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFrontendKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secretKey = env('FRONTEND_API_KEY');
        // If the key is not set in .env, we allow requests (to not break everything immediately)
        // or you can choose to block them. Let's block them if the key is expected.
        if ($secretKey && $request->header('X-FRONTEND-KEY') !== $secretKey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access Denied: Invalid or missing API Key.'
            ], 403);
        }

        return $next($request);
    }
}
