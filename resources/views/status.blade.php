@extends('main')
@section('title', 'Conversion status')
@section('styles')
    <style>
        table td.date {
            white-space: nowrap;
        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="text-center mt-4">@yield('title')</h1>
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
                        <th scope="col">Key</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td scope="row" class="date">{{ $transaction->log_date }}</td>
                            <td>
                                <a href="https://etherscan.io/address/{{ $transaction->from_address  }}"
                                   title="{{ $transaction->from_address  }}"
                                   rel="noopener"
                                   target="_blank">{{ substr($transaction->from_address, 0, 10) }}…</a>
                            </td>
                            <td>{{ $transaction->amount }}</td>
                            <td>{{ $transaction->public_key }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection