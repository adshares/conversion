@extends('main')
@section('title', 'Conversion status')
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
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="text-center mt-4">Genesis registration has been completed</h1>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Address</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Public key</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td scope="row" class="date"><pre>{{ $transaction->log_date }}</pre></td>
                            <td>
                                <a href="https://etherscan.io/address/{{ $transaction->from_address  }}"
                                   title="{{ $transaction->from_address  }}"
                                   rel="noopener"
                                   target="_blank"><pre>{{ substr($transaction->from_address, 0, 10) }}…</pre></a>
                            </td>
                            <td class="text-right pr-4"><pre>{{ $transaction->amount }}</pre></td>
                            <td><pre>{{ $transaction->public_key }}</pre></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection