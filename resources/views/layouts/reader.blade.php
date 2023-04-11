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

    <!-- Web Site Icon -->
    @component('components.icons-load', ['iconFolder' => $iconFolder])@endComponent

	<title>{{appName()}} - @yield('title')</title>

    <!-- Scripts -->

	<script src="{{asset('js/jquery-3.4.1.js')}}"></script>
	<script src="{{asset('js/bootstrap/bootstrap.bundle.min.js')}}"></script>
	<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>

	@if (true)
	<script src="https://cdn.jsdelivr.net/npm/microsoft-cognitiveservices-speech-sdk@latest/distrib/browser/microsoft.cognitiveservices.speech.sdk.bundle-min.js"></script>
	@endif

    <script src="{{ asset('/js/project.js?' . getVersionJs()) }}"></script>
    <script src="{{ asset('/js/recorder.js?' . getVersionJs()) }}"></script>
	<script src="{{ asset('/js/reader.js?' . getVersionJs()) }}"></script>
	<script src="{{ asset('/js/speech.js?' . getVersionJs()) }}"></script>

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
