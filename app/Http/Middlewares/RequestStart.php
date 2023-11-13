<?php

namespace App\Http\Middlewares;

use Ulid\Ulid;

use Closure;

class RequestStart
{
    private $ulid;

    /**
     * contructor
     * @param Ulid $ulid
     * @return void
     */
    public function __construct(
        Ulid $ulid
    ) {
        $this->ulid = $ulid;
    }

    /**
     * handle an incoming request,
     *   setting start and request id
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        $request->requestId = $this->ulid->generate();
        $request->startProfile = microtime(true);
        
        return $next($request);
    }
}
