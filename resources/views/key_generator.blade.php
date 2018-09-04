@extends('main')
@section('title', 'Key generator')
@section('content')

    <div class="container converter">
        <div class="row">
            <div class="col">
                <h1 class="text-center mt-4">@yield('title')</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="content">
                    <div class="form-group mb-0">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="text-mnemonic">Mnemonic</label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <textarea disabled class="form-control" id="text-mnemonic">
                            </textarea>
                            <div class="valid-feedback">
                                Correct
                            </div>
                            <div class="invalid-feedback">
                                Invalid words
                            </div>
                        </div>
                        <button class="btn btn-primary" id="btn-random">Random mnemonic</button>
                    </div>
                </div>
                <div class="content text-center">
                    <button class="btn btn-primary" id="btn-generate">Generate keys</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="keygenModal" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Generated keys</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-3" role="alert">
                        <p class="mb-0">
                            Important security note.
                        </p>
                        <p class="mb-0">
                            Store seed and keys safely. Only public key can be revealed. Seed and private key must not
                            be transferred to anyone.
                        </p>
                    </div>

                    <a href="#" data-toggle="tooltip" title="Seed is used to derive keys"><span
                                class="badge badge-secondary">SEED</span> Seed</a>
                    <div class="border border-secondary p-1 my-3">
                        <pre><code id="seed">---</code></pre>
                    </div>
                    <a href="#" data-toggle="tooltip" title="Secret key is used to sign transaction"><span
                                class="badge badge-secondary">SK</span> Secret key</a>
                    <div class="border border-secondary p-1 my-3">
                        <pre><code id="secretKey">---</code></pre>
                    </div>
                    <a href="#" data-toggle="tooltip" title="Public key can be used to validate signatures"><span
                                class="badge badge-secondary">PK</span> Public key</a>
                    <div class="border border-secondary p-1 my-3">
                        <pre><code id="publicKey">---</code></pre>
                    </div>
                    <a href="#" data-toggle="tooltip" title="Signature of empty string"><span
                                class="badge badge-secondary">SG</span> Signature of empty string</a>
                    <div class="border border-secondary p-1 my-3">
                        <pre><code id="signature">---</code></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="/js/nacl-fast.min.js"></script>
    <script src="/js/key-generator.js"></script>
@endsection
