<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - {{ $subject }}</title>
    <style>
        * {
            padding: 0;
            margin: 0;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            font-family: "Gill Sans", "Gill Sans MT", Calibri, "Trebuchet MS", sans-serif;
            /* color: #5f5f98; */
        }

        .main-wrapper {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            justify-content: center;
            background-color: #f1f5f8;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            width: 80%;
            height: fit-content;
            margin: 0 auto;
            background-color: #fff;
        }

        .topbar {
            width: 100%;
            height: 10px;
            background-color: #5F5F98;
        }

        .line-bar {
            height: 1px;
            max-width: 60%;
            background-color: rgba(95, 95, 152, 0.6901960784);
            opacity: 0.3;
            margin: 0 auto;
        }

        .logo {
            text-align: center;
            padding: 20px 0;
            background-color: #016148;
        }

        .text-capitalize {
            text-transform: capitalize;
        }

        .head {
            padding: 30px 0;
        }

        .head .head-heading {
            font-size: 35px;
            text-align: center;
        }

        .main {
            max-width: 60%;
            margin: 0 auto;
            padding-bottom: 30px;
        }

        .main h2 {
            /* color: #5F5F98; */
            font-weight: 400;
            font-size: 22px;
        }

        .main p {
            font-size: 18px;
            margin: 30px 0;
        }

        .main .btn-primary {
            background-color: #5f5f98;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 20px;
            text-decoration: none;
        }

        ul {
            padding-left: 18px;
            font-size: 16px;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="container">
            <div class="topbar"></div>
            <div class="logo" style="">
                <img src="{{ URL('/') }}/assets/images/app/logo.png" alt="logo" height="100">
            </div>
            <div class="line-bar"></div>
            <div class="head">
                <h1 class="head-heading">{{ $subject }}</h1>
            </div>

            <div class="main">
                <div class="content" style="text-align: start;">
                    {!! $content !!}
                </div>
            </div>

        </div>
    </div>
</body>

</html>
