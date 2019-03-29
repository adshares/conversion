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

$router->get('/', function () use ($router) {

    return view('converter', [
        'settings' => [
            'contractAddress' => env('ADST_CONTRACT_ADDRESS'),
            'transferMethod' => env('ADST_TRANSFER_METHOD'),
            'burnAddress' => env('ADST_BURN_ADDRESS'),
            'minTokenAmount' => (int)env('ADST_MIN_TOKEN_AMOUNT')
        ]
    ]);
});

$router->get('/key', function () use ($router) {
    return view('key_generator');
});

$router->get('/status', function () use ($router) {

    $db = app('db');

    $balance = $db->selectOne(
        'SELECT
          treasury_balance
        FROM scans
        ORDER BY created_at DESC');

    $totalSupply = (int)env('ADST_TOTAL_SUPPLY') * pow(10, 11);
    $progress = 100 * (1 - ($balance->treasury_balance / $totalSupply));

    $conversions = $db->select(
        'SELECT
          log_date,
          tx_hash,
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
        'progress' => $progress,
        'conversions' => $conversions,
        'transactions' => $transactions
    ]);
});

$router->get('/genesis', function () use ($router) {

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
        'nodes' => $nodes,
        'accounts' => $accounts
    ]);
});