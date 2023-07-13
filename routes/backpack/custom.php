<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('donor', 'DonorCrudController');
    Route::crud('glossary', 'GlossaryCrudController');
    Route::crud('donations', 'DonationsCrudController');
    Route::crud('organizations', 'OrganizationsCrudController');
    Route::crud('inventory', 'InventoryCrudController');
    Route::crud('camps', 'CampsCrudController');
    Route::crud('requests', 'RequestsCrudController');
    Route::crud('organizers', 'OrganizersCrudController');
    //Route::crud('user', 'UserCrudController');
}); // this should be the absolute last line of this file
