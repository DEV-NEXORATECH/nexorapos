<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XssMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $userInput = $request->all();
        // $editorsArray = editorsArray();
        $editorsArray=[];
        array_walk_recursive($userInput, function (&$userInput, $key) use($editorsArray) {

            if(!in_array($key, $editorsArray))
            {
                $userInput = strip_tags($userInput);
            }
        });
        $request->merge($userInput);
        return $next($request);
    }
}
