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
            'minTokenAmount' => (int)env('ADS_MIN_TOKEN_AMOUNT')
        ]
    ]);
});

$app->get('/status', function () use ($app) {

    $db = app('db');

    $conversions = $db->select(
        'SELECT
          log_date,
          from_address,
          amount,
          ads_address,
          status,
          info
        FROM conversions
        ORDER BY log_date DESC');

    $transactions = $db->select(
        'SELECT
          log_date,
          from_address,
          amount,
          public_key
        FROM transactions
        ORDER BY log_date DESC');

    return view('status', [
        'environment' => $app->environment(),
        'conversions' => $conversions,
        'transactions' => $transactions
    ]);
});

$app->get('/genesis', function () use ($app) {

    $db = app('db');

    $nodes = $db->select(
        'SELECT
          id,
          num,
          public_key
        FROM genesis_nodes
        ORDER BY id ASC');

    $accounts = $db->select(
        'SELECT
          node_id,
          id,
          num,
          address,
          amount,
          public_key
        FROM genesis_accounts
        ORDER BY node_id, id ASC');

    return view('genesis', [
        'environment' => $app->environment(),
        'nodes' => $nodes,
        'accounts' => $accounts
    ]);
});