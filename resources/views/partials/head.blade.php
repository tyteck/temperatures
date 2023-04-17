<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('pageTitle') - Courbes Ecologiques</title>
    <meta name="description" content="Podcast hosting for youtube channels made easy.">
    <meta name="keywords" content="youtube, podcast, podcasts, hosting, channel">
    <meta name="author" content="Frederick Tyteca">

    <!--Favicon-->
    <link rel="icon" type="image/png" href="/favicon.png" />

    @vite(['resources/js/app.js'])

    @livewireStyles
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
