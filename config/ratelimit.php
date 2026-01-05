<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limiter Store
    |--------------------------------------------------------------------------
    |
    | This configuration determines which cache store will be used for
    | rate limiting. Using Redis ensures rate limits persist across
    | application restarts and work in multi-server environments.
    |
    */

    'limiter' => env('RATE_LIMITER_STORE', 'redis'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limit Configurations
    |--------------------------------------------------------------------------
    |
    | Define various rate limit configurations for different routes.
    |
    */

    'api' => [
        'limit' => env('RATE_LIMIT_API', 60),
        'decay' => env('RATE_LIMIT_DECAY', 1), // minutes
    ],

    'auth' => [
        'limit' => env('RATE_LIMIT_AUTH', 5),
        'decay' => env('RATE_LIMIT_AUTH_DECAY', 1), // minutes
    ],

    'global' => [
        'limit' => env('RATE_LIMIT_GLOBAL', 1000),
        'decay' => env('RATE_LIMIT_GLOBAL_DECAY', 1), // minutes
    ],

];
