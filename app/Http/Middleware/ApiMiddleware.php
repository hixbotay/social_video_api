<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
		if($request->route('page')){
			$request->merge([
				'page' => $request->route('page'),
			]);
		}
		
        $request->headers->add(['Accept' => 'application/json']);
        return $next($request);
    }
}
