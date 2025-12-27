<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RemixController;
use Illuminate\Support\Facades\DB;
use App\Models\Genre;

Route::get('/remixes', [RemixController::class, 'index']);

Route::post('/remixes/{id}/vote', [RemixController::class, 'vote']);

Route::get('/genres', function() {
    return Genre::all();
});