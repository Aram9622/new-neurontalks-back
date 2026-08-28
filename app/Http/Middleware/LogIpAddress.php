<?php

namespace App\Http\Middleware;

use App\Models\IpLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogIpAddress
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethod('OPTIONS')) {
            IpLog::create([
                'ip_address' => $request->ip(),
                'method' => $request->method(),
                'url' => $request->url(),
                'referrer' => $request->headers->get('referer'),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return $response;
    }
}
