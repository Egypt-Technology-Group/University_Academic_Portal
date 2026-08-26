<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Registered Application Modules
    |--------------------------------------------------------------------------
    |
    | List of ModuleInterface classes to register into the ModuleManager.
    |
    */
    'modules' => [
        // Module classes will be registered here or auto-discovered
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Enabled Modules
    |--------------------------------------------------------------------------
    |
    | Initial list of module IDs that are enabled by default on fresh install.
    |
    */
    'default_enabled' => [
        'academic-structure',
        'admissions',
        'student-portal',
        'cms',
        'site-settings',
        'academic-services',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache key used to store the array of active module IDs.
    |
    */
    'cache_key' => env('MODULES_CACHE_KEY', 'app_modules_enabled'),

    /*
    |--------------------------------------------------------------------------
    | Auto Boot Modules
    |--------------------------------------------------------------------------
    |
    | Automatically boot all enabled modules during application boot.
    |
    */
    'auto_boot' => env('MODULES_AUTO_BOOT', true),
];
