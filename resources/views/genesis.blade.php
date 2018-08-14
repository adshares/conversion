@extends('main')
@section('title', 'Genesis block')
@section('styles')
    <style>
        table td.date {
            white-space: nowrap;
        }
        table pre {
            margin-bottom: 0;
        }
        h1 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }
        h2 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col text-center">
                <h1 class="text-center mt-4">Genesis block</h1>
                <br>
                <a class="btn btn-primary" role="button" href="#nodes">Nodes</a>
                <a class="btn btn-primary" role="button" href="#accounts">Accounts</a>
            </div>
        </div>
        <div class="row">
            <h2 class="text-center mt-4" id="nodes">Nodes</h2>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Identifier</th>
                        <th scope="col">Public key</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($nodes as $node)
                        <tr>
                            <td scope="row"><pre>{{ $node->id }}</pre></td>
                            <td><pre>{{ $node->public_key }}</pre></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row">
            <h2 class="text-center mt-4" id="accounts">Accounts</h2>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Node</th>
                        <th scope="col">Identifier</th>
                        <th scope="col">Address</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Public key</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($accounts as $account)
                        <tr>
                            <td scope="row"><pre>{{ $account->node_id }}</pre></td>
                            <td class="pr-4"><pre>{{ $account->id }}</pre></td>
                            <td class="pr-4"><pre>{{ $account->address }}</pre></td>
                            <td class="text-right pr-4"><pre>{{ number_format($account->amount, 2) }}</pre></td>
                            <td><pre>{{ $account->public_key }}</pre></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection