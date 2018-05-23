@extends('main')
@section('title', 'ADST Converter Tool')
@section('content')

    <div class="container converter">
        <div class="row">
            <div class="col">
                <h1 class="text-center mt-4">@yield('title')</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form class="needs-validation" novalidate id="converterForm">
                    <div class="content">
                        <div class="form-group">
                            <label for="amountInput">Amount</label>
                            <div class="input-group mb-3">
                                <input type="number" step="any" min="0" class="form-control" id="amountInput"
                                       placeholder="Enter amount" required>
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
                            <label for="keyInput">Public key</label>
                            <textarea class="form-control" id="keyInput" placeholder="Enter public key" rows="5"
                                      required></textarea>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                            <div class="invalid-feedback">
                                Please provide a valid public key.
                            </div>
                        </div>
                        <!--                        <div class="form-group form-check">-->
                        <!--                            <input type="checkbox" class="form-check-input" id="exampleCheck1">-->
                        <!--                            <label class="form-check-label" for="exampleCheck1">Check my key</label>-->
                        <!--                        </div>-->
                        <!--                        <div class="form-group">-->
                        <!--                            <label for="exampleInputPassword1">Empty message signature</label>-->
                        <!--                            <textarea class="form-control" id="exampleInputPassword1" placeholder="Enter signature" rows="5"></textarea>-->
                        <!--                        </div>-->
                    </div>
                    <div class="content text-center">
                        <button disabled type="submit" class="btn btn-primary" id="generateButton">Generate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="convertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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

                    <div class="alert alert-warning" role="alert" id="masterNodeWarning">
                        <p>
                            To become masternode operators in the ESC network without having to pay a fee you have to
                            destroy (burn) at least <span class="minMasterNodeTokenAmount">20,000</span> ADST.
                        </p>
                        <hr>
                        <p class="mb-0">
                            All your funds will go to the standard user account without creating a masternode.
                        </p>
                    </div>

                    Send <code><span id="transactionAmount">---</span> ADST</code> to:
                    <div class="border border-secondary p-1 my-3">
                        <code class="burnAddress">---</code>
                    </div>
                    with transaction data:
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
@endsection

@section('scripts')
    <script src="/js/converter.js"></script>
@endsection
