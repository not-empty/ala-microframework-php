<?php

namespace App\Http\Middlewares;

use Illuminate\Support\Facades\Validator;

use Closure;

class RequestFilters extends BaseRequest
{
    /**
     * handle an incoming request,
     *   validating and setting filters
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        $request->filters = null;

        $routeDetails = $this->getRouteDetails($request);
        $filters = $routeDetails['filters'] ?? null;

        if (empty($filters)) {
            return $next($request);
        }

        $requestData = $request->only($request->validFields);
        $filtersData = $this->extractFilters($requestData);

        if (empty($filtersData)) {
            return $next($request);
        }

        $classFilters = $this->newClass($filters, $filtersData);
        $validFilters = $classFilters->getValidFilters();
        
        if (!empty($validFilters)) {
            $request->filters = $validFilters;
        }
        
        return $next($request);
    }

    public function extractFilters(
        array $requestData
    ) : array {
        $filtersData = [];
        foreach ($requestData as $key => $value) {
            if (substr($key, 0, 7) == 'filter_') {
                $filtersData[substr($key, 7)] = $value;
            }
        }
        return $filtersData;
    }
}
