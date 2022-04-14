<?php

return [
    'db_use_suffix' => (bool) env('DB_USE_SUFFIX', 0),
    'db_suffix_required' => (bool) env('DB_SUFFIX_REQUIRED', 0),
    'db_suffix_list' => env('DB_SUFFIX_LIST', ''),
];
