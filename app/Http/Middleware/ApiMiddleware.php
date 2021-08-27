<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        //do thanh nien Linh ko hieu nen de page bat dau tu 1
		if($request->route('page')){
            $page = $request->route('page');			
		}
        if($request->page){
            $page = $request->page;
        }
        if(isset($page)){
            $request->merge([
                'page' => max((int)$page-1,0)
            ]);
        }
        
		
        $request->headers->add(['Accept' => 'application/json']);
        return $next($request);
    }
	
	public function terminate($request, $response)
	{
		Log::debug('app.requests', ['request' => $request, 'response' => $response]);
	}
}
