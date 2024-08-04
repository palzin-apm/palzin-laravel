<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enabling
    |--------------------------------------------------------------------------
    |
    | Setting "false" the package stop sending data to Palzin.
    |
    */

    'enable' => env('PALZIN_APM_ENABLE', true),


    /*
    |--------------------------------------------------------------------------
    | Enabling Package Information
    |--------------------------------------------------------------------------
    |
    | Setting "false" the package stop sending data to Palzin Monitor about your composer packages.
    |
    */

    'package_enable' => env('PALZIN_APM_COMPOSER_ENABLE', true),



    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | You can find your API key on your Palzin project settings page.
    |
    | This API key points the Palzin notifier to the project in your account
    | which should receive your application's events & exceptions.
    |
    */



    'key' => env('PALZIN_APM_INGESTION_KEY',''),

    /*
    |--------------------------------------------------------------------------
    | Remote URL
    |--------------------------------------------------------------------------
    |
    | You can set the url of the remote endpoint to send data to.
    |
    */

    'url' => env('PALZIN_APM_URL', 'https://demo.palzin.app'),

    /*
    |--------------------------------------------------------------------------
    | Transport method
    |--------------------------------------------------------------------------
    |
    | This is where you can set the data transport method.
    | Supported options: "sync", "async"
    |
    */

    'transport' => env('PALZIN_APM_TRANSPORT', 'async'),

    /*
    |--------------------------------------------------------------------------
    | Max number of items.
    |--------------------------------------------------------------------------
    |
    | Max number of items to record in a single execution cycle.
    |
    */

    'max_items' => env('PALZIN_APM_MAX_ITEMS', 100),

    /*
    |--------------------------------------------------------------------------
    | Proxy
    |--------------------------------------------------------------------------
    |
    | This is where you can set the transport option settings you'd like us to use when
    | communicating with Palzin.
    |
    */

    'options' => [
        // 'proxy' => 'https://55.88.22.11:3128',
        // 'curlPath' => '/usr/bin/curl',
    ],

    /*
    |--------------------------------------------------------------------------
    | Query
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to automatically add all queries executed in the timeline.
    |
    */

    'query' => env('PALZIN_APM_QUERY', true),

    /*
    |--------------------------------------------------------------------------
    | Bindings
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to include the query bindings.
    |
    */

    'bindings' => env('PALZIN_APM_QUERY_BINDINGS', true),

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to set the current user logged in via
    | Laravel's authentication system.
    |
    */

    'user' => env('PALZIN_APM_USER', true),

    /*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor email sending.
    |
    */

    'email' => env('PALZIN_APM_EMAIL', true),

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor notifications.
    |
    */

    'notifications' => env('PALZIN_APM_NOTIFICATIONS', true),

    /*
    |--------------------------------------------------------------------------
    | Job
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor background job processing.
    |
    */

    'job' => env('PALZIN_APM_JOB', true),

    /*
    |--------------------------------------------------------------------------
    | Job
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to monitor background job processing.
    |
    */

    'redis' => env('PALZIN_APM_REDIS', true),

    /*
    |--------------------------------------------------------------------------
    | Exceptions
    |--------------------------------------------------------------------------
    |
    | Enable this if you'd like us to report unhandled exceptions.
    |
    */

    'unhandled_exceptions' => env('PALZIN_APM_UNHANDLED_EXCEPTIONS', true),

    /*
 |--------------------------------------------------------------------------
 | Http Client monitoring
 |--------------------------------------------------------------------------
 |
 | Enable this if you'd like us to report the http requests done using the Laravel Http Client.
 |
 */
    'http_client' => env('PALZIN_APM_HTTP_CLIENT', true),

    'http_client_body' => env('PALZIN_APM_HTTP_CLIENT_BODY', false),


    /*
        |--------------------------------------------------------------------------
        | Report JS Errors
        |--------------------------------------------------------------------------
        |
        | This parameter offers the ability to record JavaScript errors.
        | You can specify either true or false value
        */

    'report_js_error' => env('PALZIN_APM_REPORT_JS_ERROR', false),

    /*
    |--------------------------------------------------------------------------
    | Hide sensible data from http requests
    |--------------------------------------------------------------------------
    |
    | List request fields that you want mask from the http payload.
    | You can specify nested fields using the dot notation: "user.password"
    */

    'hidden_parameters' => [
        'password',
        'password_confirmation'
    ],

    /*
    |--------------------------------------------------------------------------
    | Artisan command to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list all command signature that you don't want monitoring
    | in your Palzin dashboard.
    |
    */

    'ignore_commands' => [
        'storage:link',
        'optimize',
        'optimize:clear',
        'schedule:run',
        'schedule:finish',
        'vendor:publish',
        'list',
        'test',
        'package:discover',
        'migrate',
        'migrate:rollback',
        'migrate:refresh',
        'migrate:fresh',
        'migrate:reset',
        'migrate:install',
        'db:seed',
        'cache:clear',
        'config:cache',
        'config:clear',
        'route:cache',
        'route:clear',
        'view:cache',
        'view:clear',
        'queue:listen',
        'queue:work',
        'queue:restart',
        'vapor:work',
        'vapor:health-check',
        'horizon',
        'horizon:work',
        'horizon:supervisor',
        'horizon:terminate',
        'horizon:snapshot',
        'nova:publish',
    ],

    /*
    |--------------------------------------------------------------------------
    | Web request url to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list the url schemes that you don't want monitoring
    | in your Palzin dashboard. You can also use wildcard expression (*).
    |
    */

    'ignore_url' => [
        'telescope*',
        'vendor/telescope*',
        'horizon*',
        'vendor/horizon*',
        'nova*'
    ],

    /*
    |--------------------------------------------------------------------------
    | Job classes to ignore
    |--------------------------------------------------------------------------
    |
    | Add at this list the job classes that you don't want monitoring
    | in your Palzin dashboard.
    |
    */

    'ignore_jobs' => [
        //\App\Jobs\MyJob::class
    ],
];
