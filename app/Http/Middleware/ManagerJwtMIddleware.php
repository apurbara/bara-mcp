<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\ {
    ExpiredException,
    JWT
};
//use function env;
//use function response;

class ManagerJwtMIddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        if(!$token) {
            return response()->json([
                'meta' => [
                    "code" => 401,
                    "type" => 'Unauthorized ',
                    "error_detail" => 'Token not provided',
                ]
            ], 401);
        }
        try {
            $key = env('JWT_KEY');
            $credentials = JWT::decode($token, $key, ['HS256']);
        } catch(ExpiredException $e) {
            return response()->json([
                'meta' => [
                    "code" => 400,
                    "type" => 'Bad Request',
                    "error_detail" => 'Token expired',
                ]
            ], 400);
        }
        $request->firmId = $credentials->data->firmId;
        $request->managerId = $credentials->data->managerId;
        return $next($request);
    }
}
