@extends('main')
@section('title', 'Key generator')
@section('styles')
    <style>
        .help {
            cursor: help;
        }
        h1 {
            font-size: 1.75rem;
            margin-bottom: 1rem;
        }
        h2 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }
        @media (min-width: 576px) {
            .modal-dialog {
                max-width: 540px;
                margin: 1.75rem auto;
            }
        }
    </style>
@endsection
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
                        <div class="alert alert-danger mb-4" role="alert">
                            <p class="mb-0">
                                A seed phrase includes all the information needed to recover a wallet.
                                Please write it down on paper and store safely.
                            </p>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <label for="text-mnemonic">Mnemonic seed phrase</label>
                            </div>
                            <div class="col-sm-6 text-right">
                                <button class="btn btn-sm btn-outline-primary" id="btn-random">
                                    <i class="fas fa-redo"></i>
                                    Regenerate phrase
                                </button>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <textarea class="form-control" id="text-mnemonic">
                            </textarea>
                            {{--<div class="valid-feedback">--}}
                                {{--Correct--}}
                            {{--</div>--}}
                            <div class="invalid-feedback">
                                Please provide a valid seed phrase
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content text-center">
                    <button class="btn btn-primary" id="btn-generate">Get keys</button>
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
                    <div class="alert alert-danger mb-3" role="alert">
                        <p class="mb-0">
                            Store the secret keys safely. Only the public key and signatures can be revealed. The seed
                            phrase and the secret key must not be transferred to anyone.
                        </p>
                    </div>

                    {{--<a href="#" data-toggle="tooltip" title="Seed is used to derive keys"><span--}}
                    {{--class="badge badge-secondary">SEED</span> Seed</a>--}}
                    {{--<div class="border border-secondary p-1 my-3">--}}
                    {{--<pre><code id="seed">---</code></pre>--}}
                    {{--</div>--}}
                    <span class="help" data-toggle="tooltip" title="Secret key is used to sign transaction">Secret key
                        <span class="badge badge-secondary">SK</span></span>
                    <div class="border border-secondary p-1 my-3">
                        <code id="secretKey">---</code>
                    </div>
                    <span class="help" data-toggle="tooltip" title="Public key can be used to validate signatures">Public key
                        <span class="badge badge-secondary">PK</span></span>
                    <div class="border border-secondary p-1 my-3">
                        <code id="publicKey">---</code>
                    </div>
                    <span class="help" data-toggle="tooltip" title="It can be used to double check your public key">Signature of an empty string
                        <span class="badge badge-secondary">SG</span></span>
                    <div class="border border-secondary p-1 my-3">
                        <code id="signature">---</code>
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
    <script>
      $(function () {
        $('[data-toggle="tooltip"]').tooltip()
      })
    </script>
    <script src="/js/nacl-fast.min.js"></script>
    <script src="/js/key-generator.js"></script>
@endsection
