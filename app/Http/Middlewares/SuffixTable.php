<?php

namespace App\Http\Middlewares;

use App\Constants\PatternsConstants;
use App\Exceptions\Custom\SuffixRequiredException;
use Closure;

class SuffixTable
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
        $suffixTable = '';
        $useSuffix = $this->getConfig('db_use_suffix');

        if (!$useSuffix) {
            $this->setTableAttribute($suffixTable);
            return $next($request);
        }

        $suffixTable = $request->headers->get('Suffix') ?? '';
        if (empty(preg_match(PatternsConstants::SUFFIX, $suffixTable))) {
            $suffixTable = '';
        }

        $suffixListConfig = $this->getConfig('db_suffix_list');
        $suffixList = explode(',', $suffixListConfig);

        if (
            !empty($suffixListConfig) &&
            !in_array($suffixTable, $suffixList)
        ) {
            $suffixTable = '';
        }

        $suffixRequired = $this->getConfig('db_suffix_required');
        if (
            $suffixRequired &&
            empty($suffixTable)
        ) {
            throw new SuffixRequiredException(
                'Suffix header is missing or invalid.',
                400
            );
        }

        $this->setTableAttribute($suffixTable);
        return $next($request);
    }

    /**
     * @codeCoverageIgnore
     * put sufix table if is necessary
     * @return bool
     */
    public function setTableAttribute($suffix): bool
    {
        $config = $this->makeConfig();
        $config->set('app.db_suffix', $suffix);
        return true;
    }

    /**
     * @codeCoverageIgnore
     * create and return a config
     * @param string $configName
     */
    public function getConfig(
        string $configName
    ) {
        return config('suffix.' . $configName);
    }

    /**
     * @codeCoverageIgnore
     * make config
     */
    public function makeConfig()
    {
        return app()->make('config');
    }
}
