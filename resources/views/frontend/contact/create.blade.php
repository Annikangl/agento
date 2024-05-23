<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agento: Contact form</title>
    <link rel="apple-touch-icon" sizes="180x180" href={{ asset('assets/apple-touch-icon.png') }}>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('assets/safari-pinned-tab.svg') }}" color="#f69d63">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#f69d63">
{{--    <script--}}
{{--        src="https://www.google.com/recaptcha/enterprise.js?render=6LdkD1YpAAAAAF0VFnYO6omtCNOc0tePEAdh9rEi"></script>--}}
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f4f4f4;
        }

        header {
            background-color: #fff;
            padding: 10px 20px;
            text-align: left;
        }

        header img {
            max-height: 50px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-top: 10px;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            padding: 15px;
            margin: 5px 0 22px 0;
            border: none;
            background: #f1f1f1;
        }

        input[type="file"] {
            margin: 5px 0 22px 0;
        }

        label[for="attachment"] {
            color: rgba(0,0,0,0.3);
            font-size: 12px;
        }

        .alert {
            padding: 20px;
            color: white;
            margin-bottom: 15px;
        }

        .alert-success {
            background-color: #78cb66;
        }

        .alert-danger {
            background-color: #c2475f;
        }

        .closebtn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 22px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        button {
            background: rgb(2, 2, 2);
            border-radius: 7px;
            color: white;
            font-weight: bold;
            padding: 14px 20px;
            margin: 10px 0;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            opacity: 0.8;
        }

        @media screen and (max-width: 600px) {
            .closebtn {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <header style="background-color: #f69d63; opacity: 0.7">
        <img src="{{ asset('logo.png') }}" alt="Agento">
    </header>
    <h2>Contact us</h2>
    <p>Here you can leave a review about the application <b>Agento</b> or contact the user care service from 9:00
        to 21:00 on weekdays and from 9:00 to 19:00 on weekends.</p>
    <p>We will be grateful for reliable errors and suggestions on adding categories that you are missing. Thanks!</p>

    @if (session()->has('success'))
        <div class="alert alert-success">
            <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
            {{ session()->get('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger">
            {{ session()->get('error') }}
        </div>
    @endif

    <form action="{{ route('contact.create-ticket') }}" method="POST" id="contact-form" enctype="multipart/form-data">
        @csrf
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Message</label>
        <textarea id="message" name="content" rows="5" required></textarea>

{{--        <label for="attachment">You can attach an image of no more than 5 MB in size.</label>--}}
{{--        <input type="file" name="attachment">--}}

        <button type="submit">
            Send
        </button>
    </form>
</div>

</body>
</html>





