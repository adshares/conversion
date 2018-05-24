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
        'settings' => [
            'contractAddress' => env('CONVERTER_CONTRACT_ADDRESS'),
            'transferMethod' => env('CONVERTER_TRANSFER_METHOD'),
            'burnAddress' => env('CONVERTER_BURN_ADDRESS'),
            'minTokenAmount' => (int)env('CONVERTER_MIN_TOKEN_AMOUNT'),
            'minMasterNodeTokenAmount' => (int)env('CONVERTER_MIN_MASTER_NODE_TOKEN_AMOUNT')
        ]
    ]);
});

$app->get('/status', function () use ($app) {
    return view('status');
});