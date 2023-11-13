<?php

return [
    'override_ini' => env('NEW_RELIC_OVERRIDE_INI', false),
    'application_name' => env('NEW_RELIC_APP_NAME', ini_get('newrelic.appname')),
    'license' => env('NEW_RELIC_LICENSE_KEY', ini_get('newrelic.license')),
];
