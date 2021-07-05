@php
$domainName = isset($domainName) ? $domainName : '';
@endphp

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Learn Systems">
	<meta name="generator" content="Jekyll v3.8.5">
	<title>Site Title</title>

    <!-- Scripts -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
	<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
	<script src="https://getbootstrap.com/docs/4.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>

    <script src="{{ asset('js/project.js') }}"></script>
	<script src="{{ asset('js/timedSlides.js') }}"></script>

	<!-- Bootstrap core CSS -->
	<link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/project.css" rel="stylesheet">
    <link href="/css/glyphicons.css" rel="stylesheet">

</head>

<body style="margin:0; padding:0;">

<main role="main">
	<div class="">
		@yield('content')
	</div>
</main>

</body>
</html>
