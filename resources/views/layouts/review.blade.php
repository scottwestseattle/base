@php
$domainName = isset($domainName) ? $domainName : '';
$iconFolder = App\Site::getIconFolder();
@endphp
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Learn Systems">
	<meta name="generator" content="Jekyll v3.8.5">
	<title>{{appName()}} - @yield('title')</title>

    <!-- Web Site Icon -->
    @component('components.icons-load', ['iconFolder' => $iconFolder])@endComponent

    <!-- Scripts -->
	<script src="{{asset('js/jquery-3.4.1.js')}}"></script>
	<script src="{{asset('js/bootstrap/bootstrap.bundle.min.js')}}"></script>
	<script>window.jQuery || document.write('<script src="https://getbootstrap.com/docs/4.3/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>

    <script src="{{ asset('js/project.js?' . getVersionJs()) }}"></script>

	<!-- qnaBase.js is the base class with all the common logic -->
	@if (isset($settings['loadJs']))
		<script src="{{ asset('js/qnaBase.js?' . getVersionJs()) }}"></script>
		<!-- qna*.js is the custom class such as qnaFlashcards, qnaMultipleChoice, etc -->
		<script src="{{ asset('js/' . $settings['loadJs'] . '?' . getVersionJs()) }}"></script>
	@else
		<!-- old way, not plugged in yet -->
		<script src="{{ asset('js/review.js') }}"></script>
	@endif

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
