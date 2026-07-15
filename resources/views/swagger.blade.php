<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>API Documentation - {{ config('app.name', 'Laravel') }}</title>
        @vite(['resources/swagger/swagger.js'])
    </head>
    <body>
        <div id="swagger-ui"></div>
    </body>
</html>
