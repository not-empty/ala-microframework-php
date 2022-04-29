<?php

namespace App\Console\Commands;

use App\Console\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;

class RandomSeedCreateCommand extends BaseCommand
{
    protected $signature = 'random:create {domain}';
    protected $description = 'Create new seed';
    protected $filesMap = [
        'Seeds' => [
            'url' => '/app/Seeds/',
            'files' => [
                '{{domainCaps}}' => 'seed.create',
            ],
        ],
    ];

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
     * get the stub file for the generator.
     * @return string
     */
    protected function getStubContent(
        $name,
        $stub
    ) {
        $content = file_get_contents(base_path('app') . '/Console/Stubs/' . $name . '/' . $stub . '.stub');
        return $content;
    }

    /**
     * create file with contents
     * @param string $path
     * @param string $name
     * @param string $contents
     * @return void
     */
    private function createFile(
        string $nameStub,
        string $contents
    ) {
        $holePath = base_path('app') . '/Seeds/' . $nameStub . 'Seed';

        if (!file_exists($holePath . '.php')) {
            $file = fopen($holePath . '.php', 'wr');
            fwrite($file, $contents);
            fclose($file);
        }
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

        $domain = $this->prepareDomainName($this->argument('domain'));
        $domainCaps = ucfirst($domain);

        foreach ($this->filesMap as $name => $info) {
            $this->info('');
            $this->info('Creating ' . $name . '...');

            foreach ($info['files'] as $nameStub => $stub) {
                $nameStub = str_replace('{{domainOriginal}}', $domainOriginal, $nameStub);
                $nameStub = str_replace('{{domainCaps}}', $domainCaps, $nameStub);
                $content = $this->getStubContent(
                    $name,
                    $stub
                );
                $content = str_replace('{{domainOriginal}}', $domainOriginal, $content);
                $content = str_replace('{{domainCaps}}', $domainCaps, $content);

                $this->info('Creating file ' . $nameStub . '...');
                $this->createFile(
                    $nameStub,
                    $content
                );
            }
        }
        $this->info('');
        $this->info('All done!');
    }
}
