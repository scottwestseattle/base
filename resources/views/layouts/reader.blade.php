@php
    $iconFolder = App\Site::getIconFolder();
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Learn Systems">
	<meta name="generator" content="Jekyll v3.8.5">

    <!-- Icon -->
    @if (isset($iconFolder))
        <link rel="apple-touch-icon" sizes="180x180" href="/{{$iconFolder}}/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/{{$iconFolder}}/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/{{$iconFolder}}/favicon-16x16.png">
        <link rel="manifest" href="/{{$iconFolder}}/site.webmanifest">
        <link rel="mask-icon" href="/{{$iconFolder}}/safari-pinned-tab.svg" color="#5bbad5">
        <link rel="shortcut icon" href="/{{$iconFolder}}/favicon.ico">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="msapplication-config" content="/{{$iconFolder}}/browserconfig.xml">
        <meta name="theme-color" content="#ffffff">
    @else
        <!-- use the default icon in the public folder -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#ffc40d">
        <meta name="theme-color" content="#ffffff">
    @endif

	<title>{{appName()}} - @yield('title')</title>

    <!-- Scripts -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
	<script src="https://getbootstrap.com/docs/4.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>

	@if (true)
	<script src="https://cdn.jsdelivr.net/npm/microsoft-cognitiveservices-speech-sdk@latest/distrib/browser/microsoft.cognitiveservices.speech.sdk.bundle-min.js"></script>
	@endif

    <script src="{{ asset('/js/project.js') }}"></script>
    <script src="{{ asset('/js/recorder.js') }}"></script>
	<script src="{{ asset('/js/reader.js') }}"></script>

	<!-- Bootstrap core CSS -->
	<link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/project.css" rel="stylesheet">
    <link href="/css/glyphicons.css" rel="stylesheet">
    <link href="/css/recorder.css" rel="stylesheet" type="text/css">
    <link href="/css/reader.css" rel="stylesheet" type="text/css">

</head>

<body style="margin:0; padding:0;">

<main role="main">
	<div class="">
		@yield('content')
	</div>
</main>

</body>
</html>
