<?php

namespace App\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Intouch\Newrelic\Newrelic;
use App\Utils\FixPathUtil;

class NewRelicLumen
{
    protected $newRelic;
    private $fixPathUtil;

    /**
     * constructor
     * @param Newrelic $newRelic
     * @return void
     */
    public function __construct(
        Newrelic $newRelic,
        FixPathUtil $fixPathUtil
    ) {
        $this->newRelic = $newRelic;
        $this->fixPathUtil = $fixPathUtil;
    }

    /**
     * Handles the request by naming the
     *   transaction for New Relic
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle(
        $request,
        Closure $next
    ) {
        if (config('newRelic.override_ini')) {
            $this->newRelic->setAppName(
                config('newRelic.application_name'),
                config('newRelic.license'),
                true
            );
        }

        $this->newRelic->nameTransaction(
            $this->fixPathUtil->fixPath($request->getPathInfo())
        );

        $this->newRelic->addCustomParameter(
            'requestUri',
            $request->getRequestUri()
        );

        $request->newRelic = $this->newRelic;

        return $next($request);
    }
}
