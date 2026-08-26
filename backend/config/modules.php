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
        \App\Modules\AcademicStructure\AcademicStructureModule::class,
        \App\Modules\Admissions\AdmissionsModule::class,
        \App\Modules\AcademicServices\AcademicServicesModule::class,
        \App\Modules\Cms\CmsModule::class,
        \App\Modules\Events\EventsModule::class,
        \App\Modules\Documents\DocumentsModule::class,
        \App\Modules\Results\ResultsModule::class,
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
        'academic-services',
        'cms',
        'events',
        'documents',
        'results',
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
