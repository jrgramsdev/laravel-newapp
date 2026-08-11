<?php

use Illuminate\Support\Facades\Route;

// The Vue router owns every non-API path, so deep links (/products/3) have to
// resolve to the SPA shell rather than 404 on a hard refresh.
Route::view('/{any?}', 'app')->where('any', '^(?!api).*$');
