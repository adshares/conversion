<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {

    return view('converter', [
        'environment' => $app->environment(),
        'settings' => [
            'contractAddress' => env('ADS_CONTRACT_ADDRESS'),
            'transferMethod' => env('ADS_TRANSFER_METHOD'),
            'burnAddress' => env('ADS_BURN_ADDRESS'),
            'minTokenAmount' => (int)env('ADS_MIN_TOKEN_AMOUNT'),
            'minMasterNodeTokenAmount' => (int)env('ADS_MIN_MASTER_NODE_TOKEN_AMOUNT')
        ]
    ]);
});

$app->get('/status', function () use ($app) {
    return view('status', ['environment' => $app->environment()]);
});