<?php
$domainName = isset($domainName) ? $domainName : '';
?>
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
	<script src="{{asset('js/jquery-3.4.1.js')}}"></script>
	<script src="{{asset('js/bootstrap/bootstrap.bundle.min.js')}}"></script>
	<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>

    <script src="{{ asset('js/project.js') }}"></script>
	<script src="{{ asset('js/quiz.js') }}"></script>

	<!-- Bootstrap core CSS -->
	<link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/css/project.css" rel="stylesheet">
    <link href="/css/glyphicons-short.css" rel="stylesheet">

</head>

<body style="margin:0; padding:0;">

@if (false)
	<header style="">
	</header>
@endif

<main role="main">
	<div class="">
		@yield('content')
	</div>
</main>

@if (false)
	<!-- FOOTER -->
	<footer class="">
		<div class="container">
Footer
		</div>
	</footer>
@endif

</body>
</html>
