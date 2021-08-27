<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Admin
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
        if($request->header('adminkey')!= 'maichaudaimachdonganhhanoi'){
            return response()->json(['message' => 'unauthorize'], 403);
        }
        return $next($request);

    }
}
