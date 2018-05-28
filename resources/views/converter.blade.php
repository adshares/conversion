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
                                       value="{{ $settings['minMasterNodeTokenAmount'] }}">
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
                                    <label for="keyInput">Public key <span
                                                class="badge badge-secondary">PK</span></label>
                                </div>
                                <div class="col-sm-6 text-right">
                                    <a href="https://github.com/adshares/esc/wiki/How-to-generate-esc-keys"
                                       target="_blank"
                                       title="How to generate ESC keys?">
                                        <small>How to generate ESC keys?</small>
                                    </a>
                                </div>
                            </div>
                            <textarea class="form-control" id="keyInput" placeholder="Enter public key" rows="3"
                                      required></textarea>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div class="invalid-feedback">
                                Please provide a valid public key.
                            </div>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="doubleCheckInput" checked>
                            <label class="form-check-label" for="doubleCheckInput">Double key verification</label>
                        </div>
                        <div class="form-group">
                            <label for="signatureInput">Empty string signature <span
                                        class="badge badge-secondary">SG</span></label>
                            <textarea class="form-control" id="signatureInput" placeholder="Enter signature"
                                      rows="5"></textarea>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div class="invalid-feedback">
                                Please provide a valid signature.
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

                    <div class="alert alert-warning mb-3" role="alert" id="masterNodeWarning">
                        <p>
                            To become masternode operators in the ESC network without having to pay a fee you have to
                            destroy (burn) at least <span class="minMasterNodeTokenAmount">20,000</span> ADST.
                        </p>
                        <hr>
                        <p class="mb-0">
                            All your funds will go to the standard user account without creating a masternode.
                        </p>
                    </div>

                    Send <code>0 ETH</code> to:
                    <div class="border border-secondary p-1 my-3">
                        <code class="contractAddress">---</code>
                    </div>
                    With transaction data:
                    <div class="border border-secondary p-1 my-3">
                        <code id="transactionData">---</code>
                    </div>

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
    <div class="modal fade" id="keyWarningModal" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading text-center">Signature verification faild</h4>
                        <hr>
                        <p>
                            Signature of an empty string <span class="badge badge-secondary">SG</span> doesn't match the
                            public key <span class="badge badge-secondary">PK</span>. Make sure you generate correct
                            keys.
                        </p>
                        <p>
                            <strong>Retrieve founds with an incorrect key will be impossible.</strong>
                        </p>
                        <p class="mb-0">
                            <small>You can find more about keys on <a
                                        href="https://github.com/adshares/esc/wiki/How-to-generate-esc-keys"
                                        target="_blank"
                                        title="How to generate ESC keys?">this page</a>.
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
    <script src="/js/converter.js"></script>
@endsection
