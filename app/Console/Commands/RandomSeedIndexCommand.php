<?php

namespace App\Console\Commands;

use App\Console\Commands\BaseCommand;
use Faker\Factory as FakerFactory;
use Faker\Generator as Faker;
use GuzzleHttp\Client as Guzzle;
use Symfony\Component\Console\Input\InputArgument;

class RandomSeedIndexCommand extends BaseCommand
{
    protected $signature = 'random:seed {domain} {rows}';
    protected $description = 'Seed random data to an index into elastic';

    private $allowedTypes = [
        'date',
        'fixed',
        'float',
        'geo',
        'geobr',
        'integer',
        'list',
        'name',
        'word',
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
    protected function getArguments(): array
    {
        $arguments = [
            [
                [
                    'name', InputArgument::REQUIRED, 'Domain name.',
                ],
                [
                    'rows', InputArgument::REQUIRED, 'Number of rows.',
                ]
            ]
        ];
        return $arguments;
    }

    /**
     * execute the console command
     * @return void
     */
    public function handle()
    {
        $domainOriginal = strtolower($this->argument('domain'));
        $rows = (int) $this->argument('rows');

        $validDomain = preg_match('/^[a-z_]+$/', $domainOriginal);
        if (!$validDomain) {
            $this->error('Domain name must have only lowercase letters and underscore!');
            die;
        }

        if (!is_int($rows) || $rows <= 0 || $rows > 1000000) {
            $this->error('Rows must be an integer between 1 and 1.000.000');
            die;
        }

        $domain = $this->prepareDomainName($this->argument('domain'));
        $domainCaps = ucfirst($domain);

        $class =  '\App\Seeds\\' . $domainCaps . 'Seed';
        $seedClass = $this->newSeed($class);
        $seedClass = $seedClass->getSeed();

        $indexName = $seedClass['index'];
        $indexFields = $seedClass['fields'];

        $config = $this->getConfig('token');
        $token = array_key_first($config['data']);
        $secret = $config['data'][$token]['secret'];
        $context = $config['data'][$token]['name'];

        $authUrl = 'localhost:8101/auth/generate';
        $authRequest = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => [
                'token' => $token,
                'secret' => $secret,
            ],
        ];
        $guzzle = $this->newGuzzle();
        $authResponse = $guzzle->POST(
            $authUrl,
            $authRequest
        );

        $authData = json_decode($authResponse->getBody()->getContents(), true);
        $jwtToken = $authData['data']['token'];

        for ($i = 0; $i < $rows; $i++) {
            echo '.';
            $saveData = [];
            foreach ($indexFields as $field => $params) {
                $saveData[$field] = $this->getRandomValue($params);
            }

            $url = 'localhost:8101/' . $indexName . '/add';
            $request = [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => $jwtToken,
                    'Context' => $context,
                ],
                'json' => $saveData,
            ];

            $response = $guzzle->POST(
                $url,
                $request
            );

            $reponseData = json_decode($response->getBody()->getContents(), true);
            $jwtToken = $reponseData['token']['token'];
        }

        $this->info('');
        $this->info('All done!');
    }

    /**
     * get random value based on parameters
     * @param array $params
     * @return mixed
     */
    public function getRandomValue(
        array $params
    ) {
        if (!isset($params['type'])) {
            $this->error('Invalid structure on seed');
            die;
        }
        if (!in_array($params['type'], $this->allowedTypes)) {
            $this->error('Forbidden type');
            die;
        }

        $callMethod = 'generate' . ucwords($params['type']);
        return $this->$callMethod(
            $params
        );
    }

    /**
     * generate random name
     * @param array $params
     * @return string
     */
    public function generateName(): string
    {
        $faker = $this->newFaker();
        return $faker->firstNameMale . ' ' . $faker->lastName;
    }

    /**
     * generate random word
     * @return mixed
     */
    public function generateWord(): string
    {
        $faker = $this->newFaker();
        return $faker->word;
    }

    /**
     * generate random geo positions
     * @param array $params
     * @return array
     */
    public function generateGeo(): array
    {
        $faker = $this->newFaker();
        return [
            [
                'lat' => $faker->latitude,
                'lon' => $faker->longitude,
            ]
        ];
    }

    /**
     * generate random geo positions in brasil
     * @return array
     */
    public function generateGeobr(): array
    {
        $faker = $this->newFaker();
        return [
            [
                'lat' => $faker->latitude(
                    -33.69111,
                    2.81972
                ),
                'lon' => $faker->longitude(
                    -77.89583,
                    -34.80861
                ),
            ],
        ];
    }

    /**
     * pass fixed value
     * @param array $params
     * @return mixed
     */
    public function generateFixed(
        array $params
    ) {
        return $params['value'];
    }

    /**
     * generate random integer
     * @param array $params
     * @return int
     */
    public function generateInteger(
        array $params
    ): int {
        if (!isset($params['min']) || !isset($params['max'])) {
            $this->error('Missing int parameters min and/or max');
            die;
        }
        if (!is_int($params['min']) || !is_int($params['max'])) {
            $this->error('Int parameters min and max must be integer');
            die;
        }
        return mt_rand($params['min'], $params['max']);
    }

    /**
     * generate random float
     * @param array $params
     * @return mixed
     */
    public function generateFloat(
        array $params
    ): float {
        if (!isset($params['min']) || !isset($params['max'])) {
            $this->error('Missing int parameters min and/or max');
            die;
        }
        if (!is_int($params['min']) || !is_int($params['max'])) {
            $this->error('Int parameters min and max must be integer');
            die;
        }
        $int = mt_rand($params['min'], $params['max']);
        $decimals = 0;
        if ($int !== $params['max']) {
            $decimals = mt_rand(0, 99);
        }
        return (float) $int . '.' . $decimals;
    }

    /**
     * generate random item from list
     * @param array $params
     * @return string
     */
    public function generateList(
        array $params
    ): string {
        if (!isset($params['values'])) {
            $this->error('Missing list parameter values');
            die;
        }
        if (!is_array($params['values'])) {
            $this->error('List parameter values must be an array');
            die;
        }
        $last = count($params['values']) - 1;
        $index = mt_rand(0, $last);
        return $params['values'][$index];
    }

    /**
     * generate random item for date
     * @param array $params
     * @return string
     */
    public function generateDate(
        array $params
    ): string {
        if (!isset($params['min_year'])) {
            $params['min_year'] = 1970;
        }
        if (!isset($params['max_year'])) {
            $params['max_year'] = (int) date('Y');
        }
        if (!is_int($params['min_year']) || !is_int($params['max_year'])) {
            $this->error('Parameters min_year and max_year values must be integer');
            die;
        }
        $firstDay = strtotime($params['min_year'] . '-01-01 00:00:00');
        $lastDay = strtotime($params['max_year'] . '-12-31 23:59:59');
        $randomTimestamp = mt_rand($firstDay, $lastDay);
        return date('Y-m-d H:i:s', $randomTimestamp);
    }

    /**
     * @codeCoverageIgnore
     * create new seed instance
     * @return mixed
     */
    public function newSeed(
        string $name
    ) {
        return new $name();
    }

    /**
     * @codeCoverageIgnore
     * create new Faker instance
     * @return Faker
     */
    public function newFaker(): Faker
    {
        return FakerFactory::create();
    }

    /**
     * @codeCoverageIgnore
     * method newGuzzle
     * create and return new GuzzleHttp\Client object
     * @return GuzzleHttp\Client
     */
    public function newGuzzle()
    {
        return new Guzzle();
    }
}
