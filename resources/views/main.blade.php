<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>ESC - ADS - @yield('title')</title>
    <meta name="description" content="ADS Tools">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
          integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="canonical" href="<?=url()?>">
    <meta property="og:title" content="ESC - ADS Tools">
    <meta property="og:description" content="ADS Tools">
    <meta property="og:url" content="<?=url()?>">
    <meta property="og:image" content="<?=url('images/logo.png')?>">
    <link rel="icon" type="image/png" href="favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="favicon-16x16.png" sizes="16x16">
    <style>

        @font-face {
            font-family: 'AvenirNext';
            font-weight: normal;
            src: url(/fonts/AvenirNext-Regular.otf) format('opentype');
        }

        @font-face {
            font-family: 'AvenirNext';
            font-weight: bold;
            src: url(/fonts/AvenirNext-Bold.otf) format('opentype');
        }

        body {
            font-family: "AvenirNext", apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            background-color: #f8f9fc;
            min-height: 100vh;
        }

        body .nav-container > .navbar,
        body > footer > .container {
            max-width: 800px;
        }

        body > .container.converter {
            max-width: 600px;
        }

        .nav-container {
            background: rgb(11, 19, 38);
            color: #fff;
        }

        .nav-container a {
            color: #fff;
        }

        .content {
            background: #fff;
            padding: 25px;
            margin: 30px 0;
        }

        h1, h2, h3 {
            color: #121f3e;
            font-weight: bold;
        }

        .btn {
            border-radius: 2px;
        }

        .btn-primary {
            background-color: #55a8fd;
            border-color: #55a8fd;
        }

        .modal .alert {
            margin-bottom: 0;
        }

        .footer {
            background: url(images/footer.jpg);
            background-position: 100% 100%;
            background-size: cover;
            background-repeat: no-repeat;
            position: relative;
            padding-top: 1px;
            color: #3A4B79;
            align-items: center;
            font-size: .875rem;
        }

        .footer .mail {
            align-items: center;
        }

        .footer .mail a {
            align-items: center;
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            font-family: "AvenirNext-Demi", sans-serif;
            font-weight: bold;
        }

    </style>
    @yield('styles')
</head>
<body>

<div class="nav-container">
    <nav class="navbar mx-auto">
        <a class="navbar-brand mr-auto" href="#">
            <img class="d-none d-sm-inline" src="/images/logo_h.svg" width="160" height="40" alt="">
            <img class="d-inline d-sm-none" src="/images/logo_x.svg" width="40" height="40" alt="">
        </a>

        <ul class="nav my-2 my-lg-0">
            <li class="nav-item">
                <a class="nav-link" href="/">Converter</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/status">Status</a>
            </li>
        </ul>
    </nav>
</div>

@yield('content')

<footer class="footer">
    <div class="container">
        <div class="footer-content row py-5">
            <div class="col-xs-12 col-md-9 row mail">
                <p class="m-2">
                    Got any Questions?
                </p>
                &nbsp;
                <a class="office-mail" href="mailto:office@adshares.net">office@adshares.net</a>
            </div>
            <div class="col-xs-12 col-md-3">
                <div class="footer-content__social-media row">
                    <div class="m-2">
                        <a id="menu_down_social_telegram" href="https://t.me/adshares" target="_blank">
                            <img src="images/telegram.svg" width="16" height="16" alt="Telegram icon">
                        </a>
                    </div>
                    <div class="m-2">
                        <a id="menu_down_social_twitter" href="https://twitter.com/adsharesNet" target="_blank">
                            <img src="images/twitter.svg" alt="Twitter icon">
                        </a>
                    </div>
                    <div class="m-2">
                        <a id="menu_down_social_facebook" href="https://www.facebook.com/adshares/" target="_blank">
                            <img src="images/facebook.svg" alt="Facebook icon">
                        </a>
                    </div>
                    <div class="m-2">
                        <a id="menu_down_social_github" href="https://github.com/adshares" target="_blank">
                            <img src="images/github.svg" alt="Github icon">
                        </a>
                    </div>
                    <div class="m-2">
                        <a id="menu_down_social_medium" href="https://medium.com/adshares" target="_blank">
                            <img src="images/medium.svg" alt="Medium icon">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
        crossorigin="anonymous"></script>
@yield('scripts')
</body>
</html>