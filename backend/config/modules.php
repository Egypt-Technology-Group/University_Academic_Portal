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
    | Initial list of module IDs enabled on fresh install. By default, no
    | module is active until cryptographically licensed by the vendor.
    |
    */
    'default_enabled' => [],

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
    | Vendor Signing Key
    |--------------------------------------------------------------------------
    |
    | Master secret used to sign and verify entitlement packages.
    |
    */
    'vendor_signing_key' => env('VENDOR_SIGNING_KEY', 'egyitech_vendor_root_master_signing_key_2026_x99'),

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
