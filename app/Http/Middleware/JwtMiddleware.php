<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
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
        try {
            $user = \JWTAuth::parseToken()->authenticate();
        }
        catch (\Exception $e){
            if ($e instanceof TokenInvalidException){
                return response()->json(['status' => 403, 'message' => 'Bearer token is invalid!'], 403);
            }
            else if ($e instanceof TokenExpiredException){
                return response()->json(['status' => 403, 'message' => 'Bearer token is expired!'], 403);
            }
            else {
                return response()->json(['status' => 403, 'message' => 'Bearer token not found!'], 403);
            }
        }
        return $next($request);
    }
}
