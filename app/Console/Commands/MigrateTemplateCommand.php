<?php

namespace App\Console\Commands;

use App\Console\Commands\BaseCommand;
use SimpleElasticsearch\SimpleElasticsearch;
use Symfony\Component\Console\Input\InputArgument;

class MigrateTemplateCommand extends BaseCommand
{
    protected $signature = 'migrate:template {domain}';
    protected $description = 'Migrate a mapping into elastic';

    /**
     * create a new command instance
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * get the console command arguments
     * @return array
     */
    protected function getArguments()
    {
        $arguments = [
            ['name', InputArgument::REQUIRED, 'Domain name.'],
        ];
        return $arguments;
    }

    /**
     * execute the console command
     * @return mixed
     */
    public function handle()
    {
        $domainOriginal = strtolower($this->argument('domain'));

        $validDomain = preg_match('/^[a-z_]+$/', $domainOriginal);
        if (!$validDomain) {
            $this->error('Domain name must have only lowercase letters and underscore!');
            die;
        }

        $config = $this->getConfig('elasticsearch');
        $domain = $this->prepareDomainName($this->argument('domain'));
        $domainCaps = ucfirst($domain);

        $elastic = $this->newSimpleElasticsearch(
            $config['host']
        );


        $class =  '\App\Templates\\' . $domainCaps . 'Template';
        $templateClass = $this->newTemplate($class);
        $template = $templateClass->getTemplate();

        $putTemplate = $elastic->putTemplate(
            $domainOriginal,
            $template
        );

        print_r($putTemplate);

        $this->info('');
        $this->info('All done!');
    }

    /**
     * @codeCoverageIgnore
     * create new Template instance
     * @return mixed
     */
    public function newTemplate(
        string $name
    ) {
        return new $name();
    }

    /**
     * @codeCoverageIgnore
     * create new SimpleElasticsearch instance
     * @return SimpleElasticsearch
     */
    public function newSimpleElasticsearch(
        string $host
    ): SimpleElasticsearch {
        return new SimpleElasticsearch($host);
    }
}
