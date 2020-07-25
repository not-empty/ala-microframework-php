<?php

namespace App\Http\Middlewares;

use Closure;

class RequestValidator extends BaseRequest
{
    /**
     * handle an incoming request,
     *   validating all data
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        $routeDetails = $this->getRouteDetails($request);
        $validator = $routeDetails['validator'] ?? null;

        if (empty($validator)) {
            return $next($request);
        }

        $toValidate = $this->newClass($validator);
        $request->validFields = $toValidate->validate($request->all());

        return $next($request);
    }
}
