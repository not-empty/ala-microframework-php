<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BaseCommand extends Command
{
    /**
     * prepare domain name
     * @param string $domain
     * @return string domain
     */
    public function prepareDomainName(
        string $domain
    ) {
        $domain = strtolower($domain);
        if (strpos($domain, '_') !== false) {
            $domainArray = explode('_', $domain);
            $name = '';
            foreach ($domainArray as $partialName) {
                if (empty($name)) {
                    $name .= $partialName;
                    continue;
                }
                $name .= ucfirst($partialName);
            }
            return $name;
        }
        return $domain;
    }

    /**
     * @codeCoverageIgnore
     * get lumen config
     * @param string $config
     * @return array
     */
	public function getConfig(
		string $config
	): array {
		return config($config) ?? [];
	}
}
