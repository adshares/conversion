@extends('main')
@section('title', 'ADST ⇝ ADS')
@section('content')

    <div class="container converter">
        <div class="row">
            <div class="col">
                <h1 class="text-center mt-4">@yield('title')</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form novalidate id="converterForm">
                    <div class="content">
                        <div class="form-group">
                            <label for="amountInput">Amount <span class="badge badge-secondary">ADST</span></label>
                            <div class="input-group mb-3">
                                <input type="number" step="1" min="{{ $settings['minTokenAmount'] }}"
                                       class="form-control" id="amountInput"
                                       placeholder="Enter amount" required
                                       value="{{ $settings['minTokenAmount'] }}">
                                <div class="input-group-append">
                                    <span class="input-group-text">ADST</span>
                                </div>
                                <div class="valid-feedback">
                                    Looks good!
                                </div>
                                <div class="invalid-feedback">
                                    Please provide a valid token amount.
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="keyInput">Account address <span class="badge badge-secondary">ADS</span></label>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <a href="https://github.com/adshares/ads/wiki/How-to-convert-ADST-tokens"
                                       target="_blank"
                                       rel="noopener"
                                       title="How to get the ADS account?">
                                        <small>How to get the ADS account?</small>
                                    </a>
                                </div>
                            </div>
                            <input class="form-control" id="addressInput" placeholder="Enter the ADS account address" required />
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div class="invalid-feedback">
                                Please provide a valid ADS account address.
                            </div>
                        </div>
                    </div>
                    <div class="content text-center">
                        <button disabled type="submit" class="btn btn-primary" id="generateButton">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="convertModal" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Transaction details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="alert alert-warning mb-3" role="alert">
                        <p class="mb-0">
                            All your funds will go to the provided account within 24 hours.
                        </p>
                    </div>

                    Send <code>0 ETH</code> to ADST contract address:
                    <div class="border border-secondary p-1 my-3">
                        <code class="contractAddress">---</code>
                    </div>

                    Set transaction data (yours <code><span class="tokenAmount">20,000</span> ADST</code> and the account address are encoded in this data):
                    <div class="border border-secondary p-1 my-3">
                        <code id="transactionData">---</code>
                    </div>

                    We recommend setting the gas limit to <code>100,000</code>.<br />

                    <small>
                        You can check your conversion status on <a href="/status">this page</a>.
                    </small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="addressWarningModal" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading text-center">Account address verification faild</h4>
                        <hr>
                        <p>
                            CRC checksum of an account address <span class="badge badge-secondary">ADS</span> doesn't valid.
                            Make sure you enter the correct address.
                        </p>
                        <p>
                            <strong>Retrieve founds with an incorrect address will be impossible.</strong>
                        </p>
                        <p class="mb-0">
                            <small>You can find more about converting on <a
                                        href="https://github.com/adshares/ads/wiki/How-to-convert-ADST-tokens"
                                        target="_blank"
                                        rel="noopener"
                                        title="How to convert ADST tokens?">this page</a>.
                            </small>
                        </p>
                        <hr>
                        <p class="mb-0 text-right">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        const converter = {
            settings: <?php echo json_encode($settings); ?>
        };
    </script>
    <script src="/js/nacl-fast.min.js"></script>
    <script src="/js/converter.js?ver=2"></script>
@endsection
