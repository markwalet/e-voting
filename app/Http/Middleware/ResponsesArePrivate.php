<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResponsesArePrivate
{
    /**
     * Handle an incoming request.
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Handle first, we're modifying the response
        $done = $next($request);

        // Add cache flags if possible
        if ($done instanceof Response) {
            $done->setCache([
                'public' => false,
                'private' => true,
                'no_cache' => true,
                'no_store' => true,
                'no_transform' => true,
                'max_age' => 0
            ]);
        }

        // Done
        return $done;
    }
}
