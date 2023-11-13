# Lumen ALA

[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square)](http://makeapullrequest.com)

API Rest Full based in lumen using query builder that auto generate base code for simple crud (with unit tests and feature tests)

Requires [PHP](https://php.net) 7.1.

### Installation

Clone the repository

(optional) Stop all other containers to avoid conflict.

```sh
docker stop $(docker ps -q)
```

Start project with Docker using compose tool.

```sh
docker-compose up -d
```

Access the container

```sh
docker exec -it ala-php bash
```

Run [Composer](https://getcomposer.org/) to install all dependencies.

```sh
composer install --prefer-dist
```

Ensure the folder ./storage are with all rights to save log and cache (alread set in composer install, but)
```sh
chmod -R 777 ./storage
```

To see what is build for this project look at ./ops/docker/dev folder.

Copy and modify the .env file

```sh
cp .env.example .env
```

Include values for `APP_KEY` and `JWT_APP_SECRET`, we strongly recommend a 32 length random string.

**You can use `/health/key` uri to generate this keys.**


Now you can access the health-check [http://localhost:8101](http://localhost:8101) and get a json response like this:

```json
{
    "status": "online",
    "version": "0.0.1"
}
```

### Requests Samples

You can find a sample of all requests you can do in the file `requests.http` (for the [REST Client](https://marketplace.visualstudio.com/items?itemName=humao.rest-client) extension on VSCode) or using curl commands listed in the file `requests.curl`


### Creating you Automatic Crud Domain

For create your brand new domain with a complete Crud use the command:

```sh
php artisan create:domain {your_domain}
```

This command will create a folder in `app/Domains`, new files in `routes`, `database/migrations` and `database/seeds` folder with all base code including all the units and feature tests.

**If your domain name has 2 words use underline (_) to separate.**

### Configuring your new Domain

- Configure your migration file in `database/migrations`
- Open your domain and configure your fields and the ordenations in `app/Domains/{your_domain}/Http/Parameters`
- Your validator rules in `app/Domains/{your_domain}/Http/Validators`
- You can modify or add more business rule in `app/Domains/{your_domain}/Businesses`
- Or your routes in `bootstrap/{your_domain}_routes` folder

### Ulid

For primary key value, this project using [Ulid](https://github.com/not-empty/ulid-php-lib) value, but you can pass other pattern in insert route if you prefer.

You can use the validate reserved word `ulid` in `app/Domains/{your_domain}/Http/Validators` folder. Config in `app/Providers/AppServiceProvider.php`

For example:

```php
/**
 * get rules for this request
 * @return array
 */
public function getRules(): array
{
    return [
        'another_id' => 'required|ulid',
    ];
}
```

### JWT

In auth route this projet use [JWT](https://github.com/kiwfy/jwt-manager-php) lib. This token will be generate if your secret, token and context is correct. This configuration stay in [Config](https://github.com/kiwfy/ala-microservice/blob/master/config/token.php) folder.

```php
return [
    'data' => [
        //Token
        '32c5a206ee876f4c6e1c483457561dbed02a531a89b380c3298bb131a844ac3c' => [
            // Context
            'name' => 'app-test',
            // Secret
            'secret' => 'a1c5930d778e632c6684945ca15bcf3c752d17502d4cfbd1184024be6de14540',
        ],
    ],
];
```

### Request Service

To make request between two or more services, this project use [Request Service](https://github.com/not-empty/request-service-php-lib) lib.

### Response

The pattern used to return all request is json and the layout is configure in your [Response](https://github.com/not-empty/response-json-php-lib) lib.

### Filters

Follow this steps to configure a new field to accepted a filter in list route

- In your domain validators list file `app/Domains/{your_domain}/Http/Validators/{your_domain}ListValidator` you can change or add more filters options.

For example, to add a filter to `age` field just include a new entry like that

```php
/**
 * get rules for this request
 * @return array
 */
public function getRules() : array
{
    return [
        'class' => 'string|in:"asc","desc"',
        'fields' => 'string',
        'order' => 'string',
        'page' => 'integer|min:1',
        'filter_name' => 'string|filter',
        'filter_age' => 'string|filter', /* here your new filter */
    ];
}
```

After that, you need to configure your filters in `app/Domains/{your_domain}/Filters`.

you can user various patterns like `FILTER_EQUAL`, `FILTER_NOT_EQUAL`, etc.

**Check all types look at `FiltersTypesConstants` class in `app/Constants`.**

```php
/**
 * set filter rules for this domain
 */
public $filter = [
    'age' => [
        'validate' => 'integer|min:18|max:99',
        'permissions' => [
            FiltersTypesConstants::FILTER_EQUAL,
            FiltersTypesConstants::FILTER_NOT_EQUAL,
        ],
    ],
    'created' => [
        'validate' => 'date',
        'permissions' => [
            FiltersTypesConstants::FILTER_LESS_THAN,
            FiltersTypesConstants::FILTER_GREATER_THAN,
            FiltersTypesConstants::FILTER_GREATER_THAN_OR_EQUAL,
            FiltersTypesConstants::FILTER_LESS_THAN_OR_EQUAL,
        ],
    ],
    'modified' => [
        'validate' => 'date',
        'permissions' => [
            FiltersTypesConstants::FILTER_LESS_THAN,
            FiltersTypesConstants::FILTER_GREATER_THAN,
            FiltersTypesConstants::FILTER_GREATER_THAN_OR_EQUAL,
            FiltersTypesConstants::FILTER_LESS_THAN_OR_EQUAL,
        ],
    ],
];
```

After that you can send this param in url query, for example:

`/{your_domain}/list?filter_name=lik,vitor` OR `/{your_domain}/list?filter_name=eql,vitor`.

### Recomendations

Use this project with [MySql](https://www.mysql.com/) with no relationship keys and NOT use JOIN.

### Production

Don't forget to change `APP_ENV` to `production` value. This enable the op_cache PHP extension, so dont use in development environment.

The production docker is located in `ops/docker/prod` and you can change the Nginx config or PHP all the way you want.


### Development

Want to contribute? Great!

Make a change and be careful with your updates!
**Any new code will only be accepted with all validations.**

To ensure that the entire project is fine:

First install the dependences (with development ones)

```sh
composer install --dev --prefer-dist
```

Second run all validations
```sh
composer checkall
```

You can run all validations plus test coverage metrics
```sh
composer checkallcover
```

### Code Quality

We create this project under stricts good pratices rules. Bellow you can see some composer commands to validate the framework code and your code as well.

**We recommend you aways run the `composer checkallcover` command to validate all your code, tests and coverage.**

lint - check for sintax errors on PHP (PHP Lint)
```sh
composer lint
```

cs - check for smells in general (Code Snifer)
```sh
composer cs
```

mess - check for smells in a more deep way (Mess Detector)
```sh
composer mess
```

test - run all tests (Unit and Feature)
```sh
composer test
```

test-cover - run all tests with code coverage (Unit and Feature)
```sh
composer test-cover
```

test-unit - run all unit tests
```sh
composer test-unit
```

test-unit-cover - run all unit tests with code coverage
```sh
composer test-unit-cover
```

test-feat - run all feature tests
```sh
composer test-feat
```

test-feat-cover - run all feature tests with code coverage
```sh
composer test-feat-cover
```

ccu - check unit coverage level (100% is required)
```sh
composer ccu
```

ccf - check feature coverage level (100% is required)
```sh
composer ccf
```

check - execute lint, cs, mess and unit tests
```sh
composer check
```

checkcover - execute lint, cs, mess and unit tests with coverage
```sh
composer check
```

checkall - execute lint, cs, mess, unit and feature tests
```sh
composer check
```

checkall - execute lint, cs, mess, unit and feature tests with coverage
```sh
composer check
```

#Sonarqube

This project is also validated with Sonarqube, has a `sonar-project.properties` file to support sonarqube execution.

To do that, edit the `sonar-project.properties` with your sonar url (maybe something like http://192.168.0.69:9900 if you running sonar in your machine), and execute sonar scan.

![Sonarqube results](https://github.com/not-empty/ala-microframework-php/blob/master/ops/sonar.png)

### Automatic Validation Before Commit

If you want to force the checkallcover in your project before commit, you can just copy the file `ops/contrib/pre-commit` to your `.git/hook`. Be aware your development environment will need to have PHP with xdebug installed in order to commit.

### Random Seed Data

You can create an automatic seeder to generate data using you add endpoint to tests purposes (or any other purpose you like).

To do that you must create a random seeder with the command:
```sh
php artisan random:create {domain_name}
```

It will create a file inside `app/Seeds/` with your domain name with all possibilities.

You may change to fullfill your needs (and your domain validations)

Now you may configure on `.env` the `SEED_URL`and `SEED_PORT` environments. (if you want to run inside docker don't change at all).

And run your seed with the domain name and the amount to records to generate.
```sh
php artisan random:seed {domain_name} {number_of_records}
```


**Not Empty Foundation - Free codes, full minds**
