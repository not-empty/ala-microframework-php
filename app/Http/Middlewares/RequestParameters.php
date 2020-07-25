<?php

namespace App\Http\Middlewares;

use Closure;

class RequestParameters extends BaseRequest
{
    /**
     * handle an incoming request,
     *   setting the parametes
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        $routeDetails = $this->getRouteDetails($request);
        $parameters = $routeDetails['parameters'] ?? null;

        $request->params = null;
        if (!empty($parameters)) {
            $construct = [
                'fields' => $request->get('fields') ?? [],
                'order' => $request->get('order') ?? '',
                'class' => $request->get('class') ?? '',
            ];

            $request->params = $this->newClass($parameters, $construct);
        }
        
        return $next($request);
    }
}
