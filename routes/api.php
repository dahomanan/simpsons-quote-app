<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\SimpsonsQuotesController;
use App\SimpsonsQuotes\Authenticator;
use App\SimpsonsQuotes\SimpsonsQuotes;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/simpsons-quotes/login', function (Authenticator $authenticator, Request $request) {
    try {
        $controller = new AuthenticationController($authenticator, $request);
        return $controller->getLoginToken();
    } catch (Exception $exception) {
        Log::error(__METHOD__ . ': ' . $exception);
        throw $exception;
    }
});

Route::get('/simpsons-quotes/show', function (SimpsonsQuotes $simpsonsQuotes) {
    try {
        $controller = new SimpsonsQuotesController($simpsonsQuotes);
        return $controller->show();
    } catch (Exception $exception) {
        Log::error(__METHOD__ . ': ' . $exception);
        throw $exception;
    }
})->middleware('auth:sanctum');
