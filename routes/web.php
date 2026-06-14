<?php

declare(strict_types=1);
Route::get('/home', function () {
    return file_get_contents(public_path('home.html'));
});
// Web routes disabled - API only application
// Scramble documentation available at /docs/api
