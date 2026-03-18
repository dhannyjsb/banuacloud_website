<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Banua Cloud Nusantara') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite('src/main.ts')
    </head>
    <body>
        <div id="root"></div>
    </body>
</html>