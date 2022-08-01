<?php
namespace App\Http\Middlewares;

use Closure;

class Cors
{
    /**
     * handle an incoming request,
     *   setting cross-domain headers
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        $accessMethods = 'POST, GET, OPTIONS, PUT, DELETE, PATCH';
        $accessHeaders = 'Content-Type, Accept, Accept-Language, Authorization, X-Requested-With, Context, Suffix';
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => $accessMethods,
            'Access-Control-Allow-Credentials' => 'true',
            'Access-Control-Max-Age' => '86400',
            'Access-Control-Allow-Headers' => $accessHeaders,
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->json(
                '{"method":"OPTIONS"}',
                200,
                $headers
            );
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }

        return $response;
    }
}
