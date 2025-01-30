<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GenerateFetchQueryParams
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //check requested
        $request->merge([
           'per_page' => $request->get('per_page') ?? '15',
           'sort_by' => $request->get('sort_by') ?? 'id',
           'sort_type' => $request->get('sort_type') ?? 'desc',
        ]);
        return $next($request);
    }
}
