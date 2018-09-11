@extends('main')
@section('title', 'Conversion status')
@section('styles')
    <style>
        .help {cursor: help;}
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
                <br>
                <a class="btn btn-primary" role="button" href="#conversion">Conversion</a>
                <a class="btn btn-primary" role="button" href="#genesis">Genesis</a>
            </div>
        </div>
        <div class="row">
            <h2 class="text-center mt-4" id="conversion">Token conversion</h2>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">TxHash</th>
                        <th scope="col">ETH Address</th>
                        <th scope="col">Amount</th>
                        <th scope="col">ADS Address</th>
                        <th scope="col">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($conversions as $transaction)
                        <tr>
                            <td scope="row" class="date"><pre>{{ $transaction->log_date }}</pre></td>
                            <td>
                                <a href="https://etherscan.io/tx/{{ $transaction->tx_hash  }}"
                                   title="{{ $transaction->tx_hash  }}"
                                   rel="noopener"
                                   target="_blank"><pre>{{ substr($transaction->tx_hash, 0, 16) }}…</pre></a>
                            </td>
                            <td>
                                <a href="https://etherscan.io/address/{{ $transaction->from_address  }}"
                                   title="{{ $transaction->from_address  }}"
                                   rel="noopener"
                                   target="_blank"><pre>{{ $transaction->from_address }}</pre></a>
                            </td>
                            <td class="text-right pr-4"><pre>{{ number_format($transaction->amount) }}</pre></td>
                            <td>
                                @if ($transaction->status <= 1)
                                <a href="https://operator.e11.click/blockexplorer/accounts/{{ $transaction->ads_address  }}"
                                   title="{{ $transaction->ads_address  }}"
                                   rel="noopener"
                                   target="_blank"><pre>{{ $transaction->ads_address }}</pre></a>
                                @else
                                    <pre>{{ $transaction->ads_address }}</pre>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($transaction->status == 0)
                                    <i class="fas fa-clock text-primary help"
                                       data-toggle="tooltip"
                                       data-placement="left"
                                       title="Conversion in progress…"></i>
                                @elseif ($transaction->status == 1)
                                    <a href="https://operator.e11.click/blockexplorer/transactions/{{ $transaction->info  }}"
                                       data-toggle="tooltip"
                                       data-placement="left"
                                       title="All right! Transaction id: {{ $transaction->info  }}"
                                       rel="noopener"
                                       target="_blank"><i class="fas fa-check-circle text-success"></i></a>
                                @elseif ($transaction->status == 2)
                                    <i class="fas fa-exclamation-triangle text-warning help"
                                       data-toggle="tooltip"
                                       data-placement="left"
                                       title="A smart contract has been detected. Manual checking is required."></i>
                                @else
                                   <i class="fas fa-exclamation-triangle text-danger help"
                                      data-toggle="tooltip"
                                      data-placement="left"
                                      title="{{ $transaction->info }}"></i>
                                @endif

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <h2 class="text-center mt-4" id="genesis">Genesis registration</h2>
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">ETH Address</th>
                        <th scope="col">Amount</th>
                        <th scope="col">ADS Public key</th>
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
                                   target="_blank"><pre>{{ $transaction->from_address }}</pre></a>
                            </td>
                            <td class="text-right pr-4"><pre>{{ number_format($transaction->amount) }}</pre></td>
                            <td><pre>{{ $transaction->public_key }}</pre></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })
    </script>
@endsection