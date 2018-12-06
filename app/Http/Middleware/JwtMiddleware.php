<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use App\User;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Response;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (!$has_supplied_credentials || $_SERVER['PHP_AUTH_USER'] != env('AUTH_USER') || $_SERVER['PHP_AUTH_PW'] != env('AUTH_PASS'));
        if ($is_not_authenticated) {
            return response()->json([
                'error' => 'Basic Auth failed.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        $token = $request->header('token');
        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'error' => 'Token not provided.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        try {
            $credentials = JWT::decode($token, env('JWT_SECRET'), [env('JWT_ENCRYPTION')]);
        } catch(ExpiredException $e) {
            return response()->json([
                'error' => 'Provided token is expired.'
            ], Response::HTTP_UNAUTHORIZED);
        } catch(Exception $e) {
            return response()->json([
                'error' => 'An error while decoding token.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $user = User::find($credentials->sub);
        // Now let's put the user in the request class so that you can grab it from there
        $request->auth = $user;
        return $next($request);
    }
}