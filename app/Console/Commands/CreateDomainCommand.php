<?php

namespace App\Console\Commands;

use App\Console\Commands\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;

class CreateDomainCommand extends BaseCommand
{
    protected $signature = 'create:domain {domain}';
    protected $description = 'Create new hole domain';
    protected $filesMap = [
        'Businesses' => [
            'url' => '/Businesses/',
            'files' => [
                '{{domainCaps}}AddBusiness' => 'business.add',
                '{{domainCaps}}BulkBusiness' => 'business.bulk',
                '{{domainCaps}}DeadDetailBusiness' => 'business.dead_detail',
                '{{domainCaps}}DeadListBusiness' => 'business.dead_list',
                '{{domainCaps}}DeleteBusiness' => 'business.delete',
                '{{domainCaps}}DetailBusiness' => 'business.detail',
                '{{domainCaps}}EditBusiness' => 'business.edit',
                '{{domainCaps}}ListBusiness' => 'business.list',
            ],
        ],
        'Controllers' => [
            'url' => '/Http/Controllers/',
            'files' => [
                '{{domainCaps}}AddController' => 'controller.add',
                '{{domainCaps}}BulkController' => 'controller.bulk',
                '{{domainCaps}}DeadDetailController' => 'controller.dead_detail',
                '{{domainCaps}}DeadListController' => 'controller.dead_list',
                '{{domainCaps}}DeleteController' => 'controller.delete',
                '{{domainCaps}}DetailController' => 'controller.detail',
                '{{domainCaps}}EditController' => 'controller.edit',
                '{{domainCaps}}ListController' => 'controller.list',
            ],
        ],
        'Filters' => [
            'url' => '/Http/Filters/',
            'files' => [
                '{{domainCaps}}Filters' => 'filters',
            ],
        ],
        'Parameters' => [
            'url' => '/Http/Parameters/',
            'files' => [
                '{{domainCaps}}Parameters' => 'parameters',
            ],
        ],
        'Validators' => [
            'url' => '/Http/Validators/',
            'files' => [
                '{{domainCaps}}AddValidator' => 'validator.add',
                '{{domainCaps}}BulkValidator' => 'validator.bulk',
                '{{domainCaps}}DeadListValidator' => 'validator.dead_list',
                '{{domainCaps}}EditValidator' => 'validator.edit',
                '{{domainCaps}}ListValidator' => 'validator.list',
            ],
        ],
        'Repositories' => [
            'url' => '/Repositories/',
            'files' => [
                '{{domainCaps}}AddRepository' => 'repository.add',
                '{{domainCaps}}AddRepository' => 'repository.add',
                '{{domainCaps}}BulkRepository' => 'repository.bulk',
                '{{domainCaps}}DeadDetailRepository' => 'repository.dead_detail',
                '{{domainCaps}}DeadListRepository' => 'repository.dead_list',
                '{{domainCaps}}DeleteRepository' => 'repository.delete',
                '{{domainCaps}}DeleteRepository' => 'repository.delete',
                '{{domainCaps}}DetailRepository' => 'repository.detail',
                '{{domainCaps}}EditRepository' => 'repository.edit',
                '{{domainCaps}}EditRepository' => 'repository.edit',
                '{{domainCaps}}ListRepository' => 'repository.list',
            ],
        ],
        'Routes' => [
            'url' => '/routes/',
            'files' => [
                '{{domainOriginal}}' => 'route',
            ],
        ],
        'TestUnitBusinesses' => [
            'url' => '/Businesses/',
            'files' => [
                '{{domainCaps}}AddBusinessTest' => 'test.unit.business.add',
                '{{domainCaps}}BulkBusinessTest' => 'test.unit.business.bulk',
                '{{domainCaps}}DeadDetailBusinessTest' => 'test.unit.business.dead_detail',
                '{{domainCaps}}DeadListBusinessTest' => 'test.unit.business.dead_list',
                '{{domainCaps}}DeleteBusinessTest' => 'test.unit.business.delete',
                '{{domainCaps}}DetailBusinessTest' => 'test.unit.business.detail',
                '{{domainCaps}}EditBusinessTest' => 'test.unit.business.edit',
                '{{domainCaps}}ListBusinessTest' => 'test.unit.business.list',
            ],
        ],
        'TestFeatureControllers' => [
            'url' => '/Controllers/',
            'files' => [
                '{{domainCaps}}AddControllerTest' => 'test.controller.add',
                '{{domainCaps}}BulkControllerTest' => 'test.controller.bulk',
                '{{domainCaps}}DeadDetailControllerTest' => 'test.controller.dead_detail',
                '{{domainCaps}}DeadListControllerTest' => 'test.controller.dead_list',
                '{{domainCaps}}DeleteControllerTest' => 'test.controller.delete',
                '{{domainCaps}}DetailControllerTest' => 'test.controller.detail',
                '{{domainCaps}}EditControllerTest' => 'test.controller.edit',
                '{{domainCaps}}ListControllerTest' => 'test.controller.list',
            ],
        ],
        'Migrations' => [
            'url' => '/database/migrations/',
            'files' => [
                '{{dateTime}}create_{{domainOriginal}}_table' => 'table.create',
            ],
        ],
    ];
    protected $fromRoot = [
        'TestUnitBusinesses' => 'Unit',
        'TestFeatureControllers' => 'Feature',
    ];
    protected $fromApp = [
        'Templates' => 'Templates',
        'Seeds' => 'Seeds',
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
     * create folder if not exists
     * @param string $path
     * @param string $domainCaps
     * @param string $name
     * @return void
     */
    private function createFolder(
        string $path,
        string $domainCaps,
        string $name
    ) {
        $notCreate = [
            'Routes',
            'Migrations',
            'Templates',
            'Seeds',
        ];
        if (!in_array($name, $notCreate)) {
            $holePath = base_path('app') . '/Domains/' . $domainCaps . $path;
            if (array_key_exists($name, $this->fromRoot)) {
                $holePath = base_path() . '/tests/' . $this->fromRoot[$name] . '/Domains/' . $domainCaps . $path;
            }
            if (!file_exists($holePath)) {
                mkdir($holePath, 0777, true);
            }
        }
    }

    /**
     * create main folder if not exists
     * @param string $domainCaps
     * @return void
     */
    private function createMainFolder(
        string $domainCaps
    ) {
        $holePath = base_path('app') . '/Domains/' . $domainCaps;
        if (!file_exists($holePath)) {
            mkdir($holePath, 0777, true);
        }
    }

    /**
     * create file with contents
     * @param string $path
     * @param string $name
     * @param string $contents
     * @return void
     */
    private function createFile(
        string $path,
        string $domainCaps,
        string $nameStub,
        string $contents,
        string $name
    ) {
        $holePath = base_path('app') . '/Domains/' . $domainCaps . $path . $nameStub;
        if ($name == 'Routes' || $name == 'Migrations') {
            $holePath = base_path() . $path . $nameStub;
        }
        if (array_key_exists($name, $this->fromRoot)) {
            $holePath = base_path() . '/tests/' . $this->fromRoot[$name] . '/Domains/' . $domainCaps . $path . $nameStub;
        }
        if (array_key_exists($name, $this->fromApp)) {
            $holePath = base_path('app') . '/' . $this->fromApp[$name] . '/' . $nameStub;
        }
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
        $dateTime = date('Y_m_d_His_');
        $domainOriginal = strtolower($this->argument('domain'));

        $validDomain = preg_match('/^[a-z_]+$/', $domainOriginal);
        if (!$validDomain) {
            $this->error('Domain name must have only lowercase letters and underscore!');
            die;
        }

        $domain = $this->prepareDomainName($this->argument('domain'));
        $domainCaps = ucfirst($domain);

        $this->createMainFolder($domainCaps);

        foreach ($this->filesMap as $name => $info) {
            $this->info('');
            $this->info('Creating ' . $name . '...');
            $this->createFolder(
                $info['url'],
                $domainCaps,
                $name
            );

            foreach ($info['files'] as $nameStub => $stub) {
                $nameStub = str_replace('{{domainOriginal}}', $domainOriginal, $nameStub);
                $nameStub = str_replace('{{domain}}', $domain, $nameStub);
                $nameStub = str_replace('{{domainCaps}}', $domainCaps, $nameStub);
                $nameStub = str_replace('{{dateTime}}', $dateTime, $nameStub);
                $content = $this->getStubContent(
                    $name,
                    $stub
                );
                $content = str_replace('{{domainOriginal}}', $domainOriginal, $content);
                $content = str_replace('{{domain}}', $domain, $content);
                $content = str_replace('{{domainCaps}}', $domainCaps, $content);

                $this->info('Creating file ' . $nameStub . '...');
                $this->createFile(
                    $info['url'],
                    $domainCaps,
                    $nameStub,
                    $content,
                    $name
                );
            }
        }
        $this->info('');
        $this->info('All done!');
    }
}
