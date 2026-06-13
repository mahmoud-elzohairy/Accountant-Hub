<?php

use Illuminate\Support\Facades\Route;

// Serve the Vue SPA for all non-API routes; Vue Router handles client-side routing.
Route::view('/{any?}', 'app')->where('any', '.*');
