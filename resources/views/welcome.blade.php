<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Retail Point Of Sale') }}</title>
        <style>
            body {
                margin: 0;
                padding: 0;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: #F4FAFF;
                color: #002E58;
                font-family: Arial, sans-serif;
            }
            .card {
                background-color: #FFFFFF;
                border: 1px solid #DCEEFF;
                border-radius: 12px;
                padding: 40px 48px;
                text-align: center;
                box-shadow: 0 4px 12px rgba(0, 46, 88, 0.12);
            }
            .brand {
                color: #0058A6;
                font-size: 24px;
                font-weight: bold;
                margin-bottom: 8px;
            }
            p {
                color: #1976C9;
                margin: 0 0 24px;
            }
            a {
                display: inline-block;
                background-color: #0058A6;
                color: #FFFFFF;
                text-decoration: none;
                padding: 10px 24px;
                border-radius: 6px;
                font-weight: bold;
            }
            a:hover {
                background-color: #003F7D;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="brand">Retail Point Of Sale</div>
            <p>Welcome to Retail Point Of Sale.</p>
            <a href="{{ route('login') }}">Log in</a>
        </div>
    </body>
</html>
