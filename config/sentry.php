<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sentry DSN
    |--------------------------------------------------------------------------
    |
    | This value is the DSN (Data Source Name) for your Sentry project.
    | You can find this in your Sentry project settings.
    |
    */

    'dsn' => env('SENTRY_LARAVEL_DSN'),

    /*
    |--------------------------------------------------------------------------
    | Release Version
    |--------------------------------------------------------------------------
    |
    | This value represents the release version of your application.
    | It's useful for tracking which version of your app caused errors.
    |
    */

    'release' => env('SENTRY_RELEASE', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Set the environment name for Sentry error tracking.
    |
    */

    'environment' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Sample Rate
    |--------------------------------------------------------------------------
    |
    | The percentage of transactions to send to Sentry.
    | 1.0 = 100%, 0.2 = 20%
    |
    */

    'traces_sample_rate' => (float) env('SENTRY_TRACES_SAMPLE_RATE', 0.2),

    /*
    |--------------------------------------------------------------------------
    | Send Default PII
    |--------------------------------------------------------------------------
    |
    | If true, personal data like user IP and ID will be sent to Sentry.
    | Set to false for privacy compliance.
    |
    */

    'send_default_pii' => (bool) env('SENTRY_SEND_DEFAULT_PII', false),

    /*
    |--------------------------------------------------------------------------
    | Breadcrumbs
    |--------------------------------------------------------------------------
    |
    | Configure which breadcrumbs to capture for debugging context.
    |
    */

    'breadcrumbs' => [
        'logs' => true,
        'cache' => true,
        'livewire' => true,
        'sql_queries' => env('SENTRY_TRACE_SQL_QUERIES', true),
        'sql_bindings' => env('SENTRY_TRACE_SQL_BINDINGS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Exceptions
    |--------------------------------------------------------------------------
    |
    | List of exception types that should not be reported to Sentry.
    |
    */

    'ignore_exceptions' => [
        Illuminate\Auth\AuthenticationException::class,
        Illuminate\Auth\Access\AuthorizationException::class,
        Symfony\Component\HttpKernel\Exception\HttpException::class,
        Illuminate\Database\Eloquent\ModelNotFoundException::class,
        Illuminate\Session\TokenMismatchException::class,
        Illuminate\Validation\ValidationException::class,
    ],

];
