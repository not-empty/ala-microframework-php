# Lumen ALA

[![Latest Version](https://img.shields.io/github/v/release/kiwfy/lumen-ala.svg?style=flat-square)](https://github.com/kiwfy/lumen-ala/releases)
[![codecov](https://codecov.io/gh/kiwfy/lumen-ala/branch/master/graph/badge.svg)](https://codecov.io/gh/kiwfy/lumen-ala)
[![Build Status](https://img.shields.io/github/workflow/status/kiwfy/lumen-ala/CI?label=ci%20build&style=flat-square)](https://github.com/kiwfy/lumen-ala/actions?query=workflow%3ACI)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat-square)](http://makeapullrequest.com)

API Rest Full created in lumen using query builder that auto generate base code for simple crud (with unit tests and feature tests)

### Installation

Requires [PHP](https://php.net) 7.2.

Run [Composer](https://getcomposer.org/) to install all dependencies.

```sh
composer install --no-dev --prefer-dist
```

### Sample

Start project with Docker using compose tool.

```sh
docker-compose up
```

To see what is build for this project look at docker -> dev folder.

Create .env file

```sh
cp .env.example .env
```

Put key value in `APP_KEY` and `JWT_APP_SECRET`.

**You can use `/health/key` uri to generate this keys or use another value if you want.**

Using [Postman](https://www.postman.com/downloads/) to consulting the routes created throw this two files.

`lumen_ala.postman_collection.json` 
`lumen_ala.postman_environment.json` 

### Automatic CRUD

For create a new Domain with a complete CRUD use the command:

```sh
php artisan create:domain {YOU_DOMAIN_NAME_HERE}
```

This command create another folder in `app/Domains`, new file in routes folder and `database/migrations`

**If your domain name has 2 words use underline (_) to separate.**

All your test unit and feature about you new domain already created to.

### Configure new Domain

- You need to configure your new migrate with your fields and remove de default field created.
- Open your domain and configure your fields and field ordenations in `app/Domains/YOUR_DOMAIN/Http/Parameters`
- Your validator rules in `app/Domains/YOUR_DOMAIN/Http/Validators`
- All your businesses you put in `app/Domains/YOUR_DOMAIN/Businesses`
- Your route is put in `bootstrap/list_routes` folder

### Ulid

When you use the add (insert) route, for default this project use [Ulid](https://github.com/kiwfy/ulid-php) value in ID.

You can use the validate reserved word `ulid` to validate if the value pass is correct in validator folder.

For example:

```
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

In auth route this projet use [JWT](https://github.com/kiwfy/jwt-manager-php) lib.

### Response

The pattern used to return all request is json and the layout is configure in your [Response](https://github.com/kiwfy/response-json-php) lib.

### Filters

Follow this steps to configure a new field to accepted a filter in list route

- In validator folder `app/Domains/YOUR_DOMAIN/Http/Validators` configure de list rules `{YOU_DOMAIN_NAME}ListValidator`. For example:

Configure a `name` field.
```
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
    ];
}
```

After that, you need to finish in `app/Domains/YOUR_DOMAIN/Filters`.

The parameter accept equal, not equal and like query.

**To see another types look at `FiltersTypesConstants` class in `app/Constants`.**

```
/**
 * set filter rules for this domain
 */
public $filter = [
    'name' => [
        'validate' => 'string|min:3',
        'permissions' => [
            FiltersTypesConstants::FILTER_EQUAL,
            FiltersTypesConstants::FILTER_NOT_EQUAL,
            FiltersTypesConstants::FILTER_LIKE,
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

After you can send this param in url query, for example:

`/{YOUR_DOMAIN}/list?filter_name=lik,fred` OR `/{YOUR_DOMAIN}/list?filter_name=eql,fred`.

**To see the reservate works look at `FiltersTypesConstants` class in `app/Constants`.**

### Recomendations

Use this project with [MySql](https://www.mysql.com/) with no relationship keys and NOT use JOIN.

In this way you can use all database maturity with as fast as possible.

Use [Clear Linux](https://clearlinux.org/) image in your PHP container to get more 50% speed and 50% less memory.

### Production

Don't forget to change `APP_ENV` to `production` value. Don't use that in develop mode because this parameter cache all your project.

### Development

Want to contribute? Great!

The project using a simple code.
Make a change in your file and be careful with your updates!
**Any new code will only be accepted with all viladations.**

To ensure that the entire project is fine:

First install all the dev dependences
```sh
composer install --dev --prefer-dist
```

Second run all validations
```sh
composer check
```

**Kiwfy - Open your code, open your mind!**
