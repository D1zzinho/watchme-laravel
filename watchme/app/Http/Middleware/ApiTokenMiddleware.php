<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request $request
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse|Response|StreamedResponse|RedirectResponse
     */
    public function handle(Request $request, Closure $next): Response|JsonResponse|StreamedResponse|RedirectResponse
    {
        if ($request->token) {
            $request->headers->set('Authorization', "Bearer {$request->token}");
        }

        return $next($request);
    }
}
